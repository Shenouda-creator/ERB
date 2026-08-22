<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'code', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }
}