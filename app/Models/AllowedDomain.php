<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedDomain extends Model
{
    use HasFactory;

    protected $table = "allowed_domains";
    public $timestamps = false;
    protected $fillable = [
        'form_id',
        'domain',
        
        
    ];
}
