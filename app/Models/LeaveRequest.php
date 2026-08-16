<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'company_id', 'start_date', 'end_date', 'reason', 'status'];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
