<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:34 AM
 */

class Product extends Eloquent {
    
    
    protected $table='products';

    public $timestamps = true;

    function investor() {
        return $this->hasMany('Investor');
    }
    function god() {
        return $this->belongsTo('God');
    }
} 