<?php
namespace App\Http\Requests;

// ItemStoreRequest を継承する
class ItemUpdateRequest extends ItemStoreRequest
{
    public function rules()
    {
        // 基本は Store と同じでOK
        $rules = parent::rules();

        // もし更新時だけ変えたいルール（IDの重複チェック除外など）があればここで上書き
        // 今回のケースなら parent::rules() のままで動くはずです
        return $rules;
    }
}