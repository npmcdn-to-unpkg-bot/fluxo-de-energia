<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:34 AM
 */

class Product extends Eloquent {
    
// ADD-> time_when_funded
    
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

} 