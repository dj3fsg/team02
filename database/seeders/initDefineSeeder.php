<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class initDefineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'database/migrations/testdata/testdata1_define.sql';
        DB::unprepared(file_get_contents($path));

        // DB::table('roles')->insert([
        //     [
        //         'id' => 0,
        //         'name' => '一般',
        //         'desc' => '一般のユーザーIDロール',
        //     ],
        //     [
        //         'id' => 99,
        //         'name' => '管理者',
        //         'desc' => '管理者ロール',
        //     ],
        // ]);
        // DB::table('categories')->insert([
        //     [
        //         'id' => 0,
        //         'category' => 'none',
        //         'desc' => 'none',
        //     ],
        //     [
        //         'id' => 1,
        //         'category' => 'schedule',
        //         'desc' => 'スケジュール',
        //     ],
        //     [
        //         'id' => 2,
        //         'category' => 'account',
        //         'desc' => '家計簿',
        //     ],
        // ]);
        // DB::table('subcategories')->insert([
        //     [
        //         'id' => 51,
        //         'category_id' => 0,
        //         'subcategory' => 'none',
        //         'desc' => '',
        //     ],
        //     [
        //         'id' => 52,
        //         'category_id' => 1,
        //         'subcategory' => '予定',
        //         'desc' => '予定',
        //     ],
        //     [
        //         'id' => 53,
        //         'category_id' => 1,
        //         'subcategory' => 'タスク',
        //         'desc' => 'タスク',
        //     ],
        //     [
        //         'id' => 54,
        //         'category_id' => 2,
        //         'subcategory' => '入金',
        //         'desc' => '入金',
        //     ],
        //     [
        //         'id' => 55,
        //         'category_id' => 2,
        //         'subcategory' => '出金',
        //         'desc' => '出金',
        //     ],
        //     [
        //         'id' => 56,
        //         'category_id' => 1,
        //         'subcategory' => 'メモ',
        //         'desc' => 'メモ',
        //     ],
        // ]);
        // DB::table('statuses')->insert([
        //     [
        //         'id' => 57,
        //         'status' => 'enable',
        //         'desc' => '有効',
        //     ],
        //     [
        //         'id' => 58,
        //         'status' => 'disable',
        //         'desc' => '無効',
        //     ],
        //     [
        //         'id' => 59,
        //         'status' => 'done',
        //         'desc' => '完了',
        //     ],
        //     [
        //         'id' => 99,
        //         'status' => 'removed',
        //         'desc' => '削除',
        //     ],
        // ]);
        // DB::table('types')->insert([
        //     [
        //         'id' => 0,
        //         'type' => 'none',
        //         'desc' => '日付設定なし',
        //     ],
        //     [
        //         'id' => 1,
        //         'type' => 'time',
        //         'desc' => 'ある日の時間帯',
        //     ],
        //     [
        //         'id' => 2,
        //         'type' => 'term',
        //         'desc' => 'ある日付の期間',
        //     ],
        //     [
        //         'id' => 3,
        //         'type' => 'date',
        //         'desc' => 'ある日',
        //     ],
        //     [
        //         'id' => 4,
        //         'type' => 'deadline',
        //         'desc' => '所定日の期限',
        //     ],
        //     [
        //         'id' => 5,
        //         'type' => 'repeat_daily',
        //         'desc' => '毎日同時刻',
        //     ],
        //     [
        //         'id' => 6,
        //         'type' => 'repeat_weekly',
        //         'desc' => '毎週同曜日',
        //     ],
        //     [
        //         'id' => 7,
        //         'type' => 'repeat_monthly',
        //         'desc' => '毎月同日',
        //     ],
        //     [
        //         'id' => 8,
        //         'type' => 'repeat_yearly',
        //         'desc' => '毎年同月同日',
        //     ],
        // ]);
    }
}
