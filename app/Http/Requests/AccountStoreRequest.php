<?php
namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
class AccountStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $date = $this->input('date');

        $maxUntil = $date
            ? Carbon::parse($date)->addYears(2)->toDateString()
            : null;

        return [
            'date'                => ['required', 'date'],
            'account_category_id' => ['required', 'integer', 'exists:account_categories,id'],
            'subcategory_id'      => ['required', 'in:3,4'],
            'title'               => ['nullable', 'string', 'max:255'],
            'amount'              => ['required', 'numeric', 'min:0'],
            'memo'                => ['nullable', 'string', 'max:255'],
            'repeat'              => ['nullable', 'in:0,1,2,3'],

            'repeat_until'        => array_values(array_filter([
                'nullable',
                'date',
                'after_or_equal:date',
                $maxUntil ? 'before_or_equal:' . $maxUntil : null, // ←開始日から2年以内
            ])),
        ];
    }

    public function attributes(): array
    {
        return [
            'date'                => '日付',
            'account_category_id' => 'カテゴリ',
            'subcategory_id'      => 'サブカテゴリ',
            'title'               => 'タイトル',
            'amount'              => '金額',
            'memo'                => 'メモ',
            'repeat'              => '繰り返しパターン',
            'repeat_until'        => '繰り返し期限',
        ];
    }

    public function messages(): array
    {
        return [
            //日付
            'date.required' => ':attributeは必須です。',
            'date.date'     => ':attributeの形式が正しくありません。',

            //金額
            'amount.required' => ':attributeを入力してください。',
            'amount.numeric'  => ':attributeは数値で入力してください。',
            'amount.min'      => ':attributeは0以上で入力してください。',

            // 区分
            'subcategory_id.required' => '区分（入金・出金）を選択してください。',
            'subcategory_id.in'       => '区分の選択が不正です。',

            // カテゴリ
            'account_category_id.required' => 'カテゴリを選択してください。',
            'account_category_id.exists'   => '選択されたカテゴリは区分と一致していません。',

            //繰り返し期限
             'repeat_until.date' => '繰り返し期限は正しい日付を入力してください。',
            'repeat_until.after_or_equal' =>
                '繰り返し期限は指定した日付以降で設定してください。',
            'repeat_until.before_or_equal' =>
                '繰り返し期限は、入力した日付から最大2年以内で設定してください。',
        ];
    }

}