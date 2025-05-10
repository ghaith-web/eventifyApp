<?php

namespace Modules\Location\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    protected $fillable = ['name'];
    
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
