<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'issued_by',
        'reason',
        'status',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
