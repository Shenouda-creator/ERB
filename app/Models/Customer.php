<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['company_id', 'account_id', 'name', 'phone', 'email'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::created(function (Customer $customer) {
            $parent = Account::where('code', '1002')->first();

            if ($parent) {
                $account = Account::create([
                    'company_id' => $customer->company_id,
                    'parent_id' => $parent->id,
                    'code' => '1002-' . $customer->id,
                    'name' => 'عميل: ' . $customer->name,
                    'type' => 'asset',
                ]);

                $customer->update(['account_id' => $account->id]);
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}