<?php
namespace App\Http\Requests;

// ItemStoreRequest を継承する
class AccountUpdateRequest extends AccountStoreRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
