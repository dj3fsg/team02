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
        return [
            'date'                => 'required|date',
            'account_category_id' => 'required|integer|exists:account_categories,id',
            'subcategory_id'      => 'required|integer|exists:subcategories,id',
            'title'               => 'nullable|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'memo'                => 'nullable|string|max:255',
            'repeat'             => 'nullable|in:0,1,2,3',
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
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => ':attributeは必須です。',
            'date.date'     => ':attributeの形式が正しくありません。',

            'amount.required' => ':attributeを入力してください。',
            'amount.numeric'  => ':attributeは数値で入力してください。',
            'amount.min'      => ':attributeは0以上で入力してください。',
        ];
    }
}