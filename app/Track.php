<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model {

    protected $fillable = ['id','artist','title','social','spotify','created_by'];

    protected $hidden = ['created_by','deleted_at','created_at', 'updated_at'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    public function charts(){
        return $this->belongsToMany(Chart::class)->withPivot('position');
    }

}
