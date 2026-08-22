<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['company_id', 'account_id', 'name', 'phone', 'email'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::created(function (Supplier $supplier) {
            $parent = Account::where('code', '2000')->first();

            if ($parent) {
                $account = Account::create([
                    'company_id' => $supplier->company_id,
                    'parent_id' => $parent->id,
                    'code' => '2000-' . $supplier->id,
                    'name' => 'مورد: ' . $supplier->name,
                    'type' => 'liability',
                ]);

                $supplier->update(['account_id' => $account->id]);
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