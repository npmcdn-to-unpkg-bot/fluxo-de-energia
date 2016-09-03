<?php

class Fruit extends Eloquent {
    use SoftDeletingTrait;
    public $timestamps = true;
    protected $table='fruits';

    function purchased_seed() {
        return $this->belongsTo('Purchase');
    }

    function purchased_land() {
    	return $this->belongsTo('Land');
    }

    function purchased_fertilizer() {
    	return $this->belongsTo('Fertilizer');
    }

} 