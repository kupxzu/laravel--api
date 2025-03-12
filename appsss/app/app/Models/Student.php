<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    // Protected $table = "students";
    Protected $fillable= [
        'name',
        'section'
    ];
}
