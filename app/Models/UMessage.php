<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $table = 'u_messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];

    /**
     * Get the sender that owns the message.
     */
    public function sender()
    {
        return $this->belongsTo(Employee::class, 'sender_id');
    }

    /**
     * Get the receiver that owns the message.
     */
    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'receiver_id');
    }
}