<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveHumidity extends Model
{
    use HasFactory;
    protected $table = 'hive_humidity';
    protected $fillable = ['record', 'hive_id'];
}
