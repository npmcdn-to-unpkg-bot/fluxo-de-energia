<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:34 AM
 */

class Product extends Eloquent {
    
// ADD-> time_when_funded
    use SoftDeletingTrait;
    protected $table='products';

    public $timestamps = true;
	

	function investments() {
		return $this->hasMany('Investment');
	}

    function investors() {
        // return $this->hasMany('Investor');
        return $this->belongsToMany('Investor','investments')->withPivot('bid_price','num_shares'); //Many to Many using THROUHGH
    }

    function god() {
        return $this->belongsTo('God');
    }

    function seed() {
        return $this->hasOne('Seed');
    }

    function land() {
        return $this->hasOne('Land');
    }

    function fertilizer() {
        return $this->hasOne('Fertilizer');
    }

} 