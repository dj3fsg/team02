<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // データの加工を行う前に、必要なキーが存在するか確認。
        if ($this->filled(['sche_start_date'])) {
            try {
                if ($this->boolean('chkAllday')) {
                    $start = Carbon::parse($this->sche_start_date)->startOfDay();
                    $end = Carbon::parse($this->sche_end_date ?? $this->sche_start_date)->addDay()->startOfDay();
                } else {
                    $start = Carbon::parse($this->sche_start_date . ' ' . $this->sche_start_time);
                    $end = Carbon::parse($this->sche_start_date . ' ' . $this->sche_end_time);
                }

                $this->merge([
                    'sche_start' => $start->toDateTimeString(),
                    'sche_end'   => $end->toDateTimeString(),
                ]);
            } catch (\Exception $e) {
                // パース失敗時は何もしない（rulesのdateバリデーションで弾く）
            }
        }

    }

    public function rules()
    {
        return [
            // typeは 1(schedule) か 2(task) など、入力値と合わせる
            'type'       => 'required', 
            'title'      => 'required|string|max:255',
            
            // 変換後の値に対するバリデーション
            'sche_start' => 'required_if:type,1|nullable|date',
            'sche_end'   => 'nullable|date|after_or_equal:sche_start',

            'sche_done'  => 'required_if:type,2|nullable|date',
            
            'memo'       => 'nullable|string|max:255',
            'location'   => 'nullable|string|max:255',
        ];
    }
}