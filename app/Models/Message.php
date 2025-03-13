<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'title',
        'description',
    ];

    /**
     * Get the employee that owns the message.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}