<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'farms';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['ownerId', 'name', 'district','address','latitude','longitude','description'];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'ownerId');
    }

    /**
     * Get the hives for the farm.
     */
    public function hives()
    {
        return $this->hasMany(Hive::class);
    }
}