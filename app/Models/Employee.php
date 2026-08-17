<?php

namespace App\Models;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
     use HasFactory, SoftDeletes, LogsActivity,InteractsWithMedia;

     protected $fillable = ['name', 'company_id', 'shift_id'];

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
     public function shift()
     {
          return $this->belongsTo(Shift::class);
     }
     public function getActivitylogOptions(): LogOptions
     {
          return LogOptions::defaults()
               ->logFillable()
               ->logOnlyDirty();
     }
}