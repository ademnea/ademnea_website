<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPackage extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'work_packages';

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
    protected $fillable = [ 
        'name',
        'partners',
        'duration',
        'instructions'
        

        ];



        /**
         * Get all of the comments for the WorkPackage
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
      
    

    
}
