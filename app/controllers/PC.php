<?php

class PC extends \BaseController {

	public function makeUsers(){
		$seeds = Product::whereCategory('fertilizer')->get();
		foreach($seeds as $seed){
			$new = new Fertilizer;
			$new->product_id = $seed->id;
			$new->save();
		}
	}	

	public function godProduct($id=1){
		$products = God::find($id)->products;
		foreach ($products as $product) {
			echo $product->id."<br>";
		}
	}
	public function listFunds(){
    	$investors = Investor::all(); //investments function is defined in the model.
    	foreach ($investors as $investor) {
    		$
    		}
    	}
    }

    public function home(){
    	return "abPCc";
    }
}
