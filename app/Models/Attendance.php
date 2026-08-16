<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Attendance extends Model
{
     use HasFactory;

    protected $fillable = ['employee_id', 'company_id', 'date', 'check_in', 'check_out'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
