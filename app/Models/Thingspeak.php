<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thingspeak extends Model
{

public $table="thingspeak_data";
    use HasFactory;
    protected $fillable = [
        'voltage',
        'battery_percentage',
        'created_at',
    ];
}