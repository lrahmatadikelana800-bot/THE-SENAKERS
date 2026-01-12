<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    
    protected $fillable = [
        'user_id',
        'log_method',
        'log_url',
        'log_ip',
        'log_request',
        'log_response'
    ];
    
    protected $casts = [
        'log_request' => 'array',
        'log_response' => 'array'
    ];
    
    // Relasi ke user jika diperlukan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}