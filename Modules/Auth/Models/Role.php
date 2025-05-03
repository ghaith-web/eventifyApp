<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Auth\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id','name'];

    // protected static function newFactory(): RoleFactory
    // {
    //     // return RoleFactory::new();
    // }
}
