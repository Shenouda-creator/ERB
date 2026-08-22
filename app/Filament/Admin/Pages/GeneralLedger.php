<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use Filament\Pages\Page;

class GeneralLedger extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'General Ledger';

    protected string $view = 'filament.admin.pages.general-ledger';

    public function getAccountsBalances(): array
    {
        $accounts = Account::with('parent')->orderBy('code')->get();

        return $accounts->map(function (Account $account) {
            $debit = $account->lines()->sum('debit');
            $credit = $account->lines()->sum('credit');

            $balance = in_array($account->type, ['asset', 'expense'])
                ? $debit - $credit
                : $credit - $debit;

            return [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
            ];
        })->toArray();
    }
}