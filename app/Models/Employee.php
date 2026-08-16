<?php

namespace App\Models;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
     use HasFactory, SoftDeletes;

     protected $fillable = ['name', 'company_id'];

     protected static function booted()
     {
          static::addGlobalScope(new CompanyScope);
     }
     public function company()
     {
          return $this->belongsTo(Company::class);
     }
     public function attendances()
     {
          return $this->hasMany(Attendance::class);
     }
     public function leaveRequests()
     {
          return $this->hasMany(LeaveRequest::class);
     }
}