<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'branch_id', 'name', 'location'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}