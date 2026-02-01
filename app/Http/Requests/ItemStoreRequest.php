<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
    protected function prepareForValidation()
    {
        if ($this->filled(['sche_start_date'])) {
            try {
                // 終了日が空なら開始日と同じにする（単発予定への配慮）
                $endDateInput = $this->sche_end_date ?: $this->sche_start_date;
                // boolean('all_day')は "on", "1", true などの値を真偽値に変換します
                if ($this->boolean('chkAllday')) { 
                    $start = Carbon::parse($this->sche_start_date)->startOfDay();
                    $end = Carbon::parse($endDateInput)->endOfDay();
                } else {
                    // 開始日時と終了日時をそれぞれ組み立てる
                    $start = Carbon::parse($this->sche_start_date . ' ' . ($this->sche_start_time ?: '00:00:00'));
                    $end = Carbon::parse($endDateInput . ' ' . ($this->sche_end_time ?: '23:59:59'));
                }

                $this->merge([
                    'sche_start' => $start->toDateTimeString(),
                    'sche_end'   => $end->toDateTimeString(),
                ]);
            } catch (\Exception $e) {
                // パース失敗時はバリデーションルールの 'date' で弾かれます
            }
        }
    }

    /**
     * バリデーションルール
     */
    public function rules()
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
            'sche_start' => ['required_if:type,schedule', 'date'],
            'sche_end'   => ['required_if:type,schedule', 'date', 'after_or_equal:sche_start'],

            // task のとき
            'sche_done' => ['required_if:type,task', 'date'],

            'memo'     => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],

            'repeat'       => ['nullable', 'integer', 'in:0,1,2,3'],
            'repeat_until' => ['nullable', 'date', 'after_or_equal:sche_start_date'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('repeat_until', ['required', 'after_or_equal:sche_start_date'], function ($input) {
            return (string)$input->type === 'schedule' && (string)$input->repeat !== '0';
        });

        // 「schedule かつ all_day=on のときは end_date 必須」など必要なら
        $validator->sometimes('sche_end_date', ['required', 'after_or_equal:sche_start_date'], function ($input) {
            return (string)$input->type === 'schedule' && isset($input->all_day);
        });
    }
}