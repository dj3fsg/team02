<?php
namespace App\Http\Requests;

// ItemStoreRequest を継承する
class ItemUpdateRequest extends ItemStoreRequest
{
    public function rules()
    {
        // 基本は Store と同じでOK
        $rules = parent::rules();

        return $rules;
    }
}