<?php
/**
 * Created by PhpStorm.
 * User: aneeshdash
 * Date: 27/11/14
 * Time: 10:43 AM
 */
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class God extends Eloquent implements UserInterface {

    use UserTrait;
   
    protected $table='gods';
    
    public $timestamps = true;

    function user() {
        return $this->belongsTo('User');
    }

    function product() {
        return $this->hasMany('Product');
    }


} 