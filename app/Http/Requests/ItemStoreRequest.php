<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ItemStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

      protected function prepareForValidation()
    {
        //ラジオボタンでスケジュール選択の場合=>sche_start,sche_endを入力
         if( $this->input('type')== "1"){
            /*
            日付＋時刻のマージ
            終日の日程選択:開始日、終了日共通　
            ー＞（開始日、終了日）+ 00:00
            そうでない場合
            開始日時：開始日＋開始時刻
            終了日時：開始日＋終了時刻
             */
            if($this->boolean(is_allday)){
                $sche_start = Carbon::parse($this->sche_start_date)->startOfDay();
                $sche_end = Carbon::parse($this->sche_end_date)->addDay()->startOfDay();

            }else{
                $sche_start = Carbon::parse($this->sche_start_date . ' ' . $this->sche_start_time);
                $sche_end = Carbon::parse($this->sche_start_date . ' ' . $this->sche_start_time);
            }

            $this->merge([
                 'sche_start' => $this->input('sche_start'),
                 'sche_end' => $this->input('sche_end'),

            ]);

            //データタイプは有効：0を入れる
             $this->merge([
        'type' => 0,
    ]);
           
            //そうでなければsche_done入力
            }else{
                  $this->merge([
                 'sche_done' => $this->input('sche_done'),

            ]);
            }
        //ここでtypeの値を決める
        //完了なら 2 そうでなければ　1
        if($this->boolean('is_done')){
            $data_type = 2;

        }else{
            $data_type = 1;

        }

      


    }

    public function rules()
    {
        return [

            'title'   => 'string',
            'memo' => 'string',
            'location'  => 'string',
            'sche_start' => ['date'],
            'sche_end'   => ['date', 'after:sche_start'], //
        ];
    }
}
