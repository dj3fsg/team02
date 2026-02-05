<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\Account;
use App\Models\Account_category;
use App\Models\Repeat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // 収支作成画面
    public function create()
    {
        return view('accounts.create');
    }

    // 収支登録処理
   public function store(AccountStoreRequest $request)
{
    $validated = $request->validated();

    // 金額整形（updateと同じロジックで統一）
    $priceInput = $validated['amount'];
    $price = filter_var(str_replace(',', '', $priceInput), FILTER_VALIDATE_FLOAT);

    if ($price === false) {
        return redirect()->back()->withErrors(['error' => 'amount must be number'])->withInput();
    }
    if ($price < 0) {
        return redirect()->back()->withErrors(['error' => 'amount cannot be negative.'])->withInput();
    }

    // 1) 基準日（accounts は date だけなので日付で扱う）
    $originDate = Carbon::parse($validated['date'])->startOfDay();
    $baseDate   = $originDate->copy();
    $currentDate = $originDate->copy();

    // 2) 期限（無ければ1回だけ＝originDate）
    $until = $request->filled('repeat_until')
        ? Carbon::parse($request->repeat_until)->endOfDay()
        : $originDate->copy()->endOfDay();

    $maxLoops = 1200;
    $count = 1;

    // 3) repeats_id（繰り返し無しなら null）
    $repeatId = null;
    if (($request->repeat ?? '0') !== '0') {
        $repeatId = Repeat::create([])->id;
    }

    do {
        Account::create([
            'user_id'             => Auth::id(),
            'date'                => $currentDate->format('Y-m-d'),
            'account_category_id' => $validated['account_category_id'],
            'subcategory_id'      => $validated['subcategory_id'],
            'title'               => $validated['title'] ?? null,
            'amount'              => $price,
            'memo'                => $validated['memo'] ?? null,
            'type_id'             => null,
            'status_id'           => 1,
            'repeats_id'          => $repeatId, // ★全件同じID
        ]);

        // --- 繰り返し無し or ループ上限で終了 ---
        if (($request->repeat ?? '0') == '0' || $count >= $maxLoops) {
            break;
        }

        // --- 加算処理（1=毎週, 2=毎月, 3=毎年） ---
        if ($request->repeat == '1') {
            $currentDate = $baseDate->copy()->addWeeks($count);
        } elseif ($request->repeat == '2') {
            $currentDate = $baseDate->copy()->addMonthsNoOverflow($count);
        } elseif ($request->repeat == '3') {
            $currentDate = $baseDate->copy()->addYearsNoOverflow($count);
        } else {
            break; // 想定外値
        }

        $count++;
    } while ($currentDate->lte($until));

    return redirect()->route('calendar.events.show', [
        'date' => $originDate->format('Y-m-d'),
    ])->with('success', '登録しました');
}

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        $account_categories = Account_category::all();

        return view('accounts.edit', compact('account', 'account_categories'));
    }

    public function update(AccountUpdateRequest $request, $id)
    {
       
        $validated = $request->validated();

        $account = Account::findOrFail($id);

        $scope = $request->input('scope', 'single'); // single / future / all

        // 金額整形（あなたの実装踏襲）
        $priceInput = $validated['amount'];
        $price = filter_var(str_replace(',', '', $priceInput), FILTER_VALIDATE_FLOAT);

        if ($price === false) {
            return redirect()->back()->withErrors(['error' => 'amount must be number'])->withInput();
        }
        if ($price < 0) {
            return redirect()->back()->withErrors(['error' => 'amount cannot be negative.'])->withInput();
        }

        $account_category_id = $validated['account_category_id'];
        if ((int)$account_category_id === 0) {
            return redirect()->back()->withErrors(['error' => 'account category must be selected.'])->withInput();
        }

        // ============ A/B：単体更新 ============
        // ・シリーズ無し
        // ・シリーズ有りでも「この1件だけ更新」
        if ($scope === 'single' || empty($account->repeats_id)) {

            // 単体更新は date も更新してOK（singleではdate必須）
            $account->date                = $validated['date'] ?? $account->date;
            $account->subcategory_id       = $validated['subcategory_id'];
            $account->amount               = $price;
            $account->title                = $validated['title'] ?? null;
            $account->account_category_id  = $account_category_id;
            $account->memo                 = $validated['memo'] ?? null;
            $account->save();

            return redirect()->route('calendar.events.show', [
                'date' => Carbon::parse($account->date)->format('Y-m-d'),
            ])->with('success', '更新しました');
        }

        // ============ C：シリーズ有りの一括更新 ============
        // future/all で来たのに repeats_id がない → ガード
        if (($scope === 'future' || $scope === 'all') && empty($account->repeats_id)) {
            return redirect()->back()->withErrors([
                'error' => 'このデータは繰り返しではないため一括更新できません。',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $account, $scope, $price, $account_category_id) {

            $oldRepeatId = $account->repeats_id;

            // ★再附番（シリーズ分割）
            $newRepeatId = Repeat::create([])->id;

            $q = Account::where('repeats_id', $oldRepeatId)
                ->where('user_id', $account->user_id)
                ->where('status_id', '!=', 99); // 論理削除は除外

            if ($scope === 'future') {
                // ★A案：日付は維持するので基準はdate
                $q->where('date', '>=', $account->date);
            } elseif ($scope === 'all') {
                // 全件
            } else {
                $q->where('id', $account->id);
            }

            $targets = $q->orderBy('date')->lockForUpdate()->get();

            foreach ($targets as $t) {
                // ★A案：内容だけ更新（dateは触らない）
                $t->subcategory_id       = $validated['subcategory_id'];
                $t->amount              = $price;
                $t->title               = $validated['title'] ?? null;
                $t->account_category_id = $account_category_id;
                $t->memo                = $validated['memo'] ?? null;

                // ★ここが肝：対象範囲だけ repeats_id を振り直す
                $t->repeats_id = $newRepeatId;

                $t->save();
            }
        });

        return redirect()->route('calendar.events.show', [
            'date' => Carbon::parse($account->date)->format('Y-m-d'),
        ])->with('success', '更新しました');
    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);

        // 論理削除で対応する
        $account->status_id = 99;
        $account->save();

        return redirect()->route('calendar.events.show', [
            'date' => Carbon::parse($account->date)->format('Y-m-d'),
        ])->with('success', '削除しました');
    }
}
