<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model {

    protected $fillable = ['week', 'show', 'created_by'];

    protected $hidden = ['created_by','deleted_at','created_at', 'updated_at'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];


    public function tracks(){
        return $this->belongsToMany(Track::class)->withPivot('position')->withTimestamps();
    }

}
