<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GovTxtAutoResponse extends Model
{
    use HasFactory;

    protected $connection = 'govtxt';
    protected $table = 'auto_response';
    public $timestamps = false; // Table doesn't have timestamp columns
    
    protected $fillable = [
        'name',
        'terms',
        'response',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
} 