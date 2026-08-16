<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\CompanyScope;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = ['company_id','name','start_time','end_time'];

    protected static function booted ():void {
        static::addGlobalScope(new CompanyScope);
    }

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
        
}
