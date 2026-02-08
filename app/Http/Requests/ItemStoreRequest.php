<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるかどうか
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前にデータを加工する
     */
    protected function prepareForValidation(): void
{
    if (! $this->filled('sche_start_date')) {
        return;
    }

    // schedule 以外なら触らない（安全）
    if ($this->input('type') !== 'schedule') {
        return;
    }

    // 終了日が空なら開始日と同じ
    $endDateInput = $this->input('sche_end_date') ?: $this->input('sche_start_date');

    // ★HTMLに合わせる：name="all_day" なのでここを見る
    $isAllDay = $this->boolean('all_day'); // checkedなら true

    try {
        if ($isAllDay) {
            $start = Carbon::parse($this->input('sche_start_date'))->startOfDay();
            $end   = Carbon::parse($endDateInput)->endOfDay();
        } else {
            // ルールは H:i なのでデフォルトも H:i に寄せる
            $st = $this->input('sche_start_time') ?: '00:00';
            $et = $this->input('sche_end_time')   ?: '23:59';

            // フォーマット固定で安全に
            $start = Carbon::createFromFormat('Y-m-d H:i', $this->input('sche_start_date').' '.$st);
            $end   = Carbon::createFromFormat('Y-m-d H:i', $endDateInput.' '.$et);
        }

        $this->merge([
            'sche_start' => $start->toDateTimeString(),
            'sche_end'   => $end->toDateTimeString(),
        ]);
    } catch (\Exception $e) {
        // いったんログに出す（固まる原因特定のため）
        logger()->error('prepareForValidation parse failed', [
            'message' => $e->getMessage(),
            'input'   => $this->all(),
        ]);
    }
}

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'type'  => ['required', 'in:schedule,task'],
            'title' => ['required', 'string', 'max:255'],

            // schedule のとき
            'sche_start_date' => ['required_if:type,schedule', 'date'],
            'sche_end_date'   => ['nullable', 'date', 'after_or_equal:sche_start_date'],

            // 終日じゃない場合だけ時刻必須（終日なら除外）
            'sche_start_time' => ['exclude_if:all_day,on', 'required_if:type,schedule', 'date_format:H:i'],
            'sche_end_time'   => ['exclude_if:all_day,on', 'required_if:type,schedule', 'date_format:H:i'],

            // prepareForValidation で merge する想定の結合済み日時
            'sche_start' => ['nullable', 'date'],
            'sche_end'   => ['nullable', 'date', 'after_or_equal:sche_start'],

            // task のとき
            'sche_done' => ['required_if:type,task', 'date'],

            'memo'     => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],

            'repeat'       => ['nullable', 'integer', 'in:0,1,2,3'],
            'repeat_until' => ['nullable', 'date', 'after_or_equal:sche_start_date'],
        ];
    }

    /**
     * 項目名の日本語表示
     */
    public function attributes(): array
    {
        return [
            // 共通
            'type'     => '予定の種類',
            'title'    => 'タイトル',
            'memo'     => 'メモ',
            'location' => '場所',

            // schedule
            'sche_start_date' => '開始日',
            'sche_end_date'   => '終了日',
            'sche_start_time' => '開始時刻',
            'sche_end_time'   => '終了時刻',
            'sche_start'      => '開始日時',
            'sche_end'        => '終了日時',

            // task
            'sche_done' => '期限日時',

            // 繰り返し
            'repeat'       => '繰り返し',
            'repeat_until' => '繰り返し終了日',

            // UI
            'all_day' => '終日',
        ];
    }

    /**
     * メッセージの日本語表示
     */
    public function messages(): array
    {
         $today = now()->toDateString();
        return [
            // 必須系
            'required'               => ':attribute は必須項目です。',
            'required_if'            => ':other が :value の場合、:attribute は必須です。',
            'title.required'         => 'タイトルは必須です。',

            // 日付・時刻
            'date'                   => ':attribute は正しい日付を入力してください。',
            'date_format'            => ':attribute は :format 形式で入力してください。',
            'after_or_equal'         => ':attribute は :date 以降を指定してください。',
            'sche_start_date.after_or_equal'
            => "開始日時は本日（{$today}）以降を指定してください。",

            // 文字数
            'max.string'             => ':attribute は :max 文字以内で入力してください。',

            // 選択肢
            'in'                     => ':attribute の値が不正です。',
            'integer'                => ':attribute は整数で指定してください。',

            // schedule のとき必須（日本語で固定）
            'sche_start_date.required_if' => '予定を登録する場合、開始日は必須です。',
            'sche_start_time.required_if' => '予定を登録する場合、開始時刻は必須です。',
            'sche_end_time.required_if'   => '予定を登録する場合、終了時刻は必須です。',
            'sche_start.required_if'      => '予定を登録する場合、開始日時は必須です。',
            'sche_end.required_if'        => '予定を登録する場合、終了日時は必須です。',

            // task のとき必須（必要なら）
            'sche_done.required_if'       => 'タスクを登録する場合、期限日時は必須です。',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes(
            'repeat_until',
            ['required', 'after_or_equal:sche_start_date'],
            function ($input) {
                return (string) $input->type === 'schedule'
                    && (string) $input->repeat !== '0';
            }
        );

        // 「schedule かつ all_day=on のときは end_date 必須」など必要なら
        $validator->sometimes(
            'sche_end_date',
            ['required', 'after_or_equal:sche_start_date'],
            function ($input) {
                return (string) $input->type === 'schedule'
                    && isset($input->all_day);
            }
        );
    }
}
