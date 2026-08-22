<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JournalEntry extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['company_id', 'date', 'reference', 'description'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getTotalDebit(): float
    {
        return $this->lines->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return round($this->getTotalDebit(), 2) === round($this->getTotalCredit(), 2);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}