<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeeCount extends Model
{
    use HasFactory;

        public function hiveVideo()
    {
        return $this->belongsTo(HiveVideo::class);
    }

}
