<?php
// NOTE : config/session.php => CHANGED expire_on_close = false

class GC extends \BaseController {
	//****  UPDATE THESE BELOW, ANALOGUS TO makeInvestment Check ****//


	public function check($god,$input){
		//Life Energy price check
		//FT ET positive check
		if(!($input['num_units']&& $input['unit_price'] && $input['FT']>0 && $input['ET']>0 ))return false;
		else {
			$price= (int)($input['num_units'])* (int)($input['unit_price']);
			$LE=$god->le;

			//REAL TIME THR <- repeated use
		
			$total=God::all()->sum('le') + Investor::all()->sum('le') + Farmer::all()->sum('le');
			
			$facGI = Config::get('game.facGI');

			$THR= $facGI* $total; //this factor may depend on number of users ?!

			if($LE - $price > $THR)
			{
			$god->le -= $price;
			$god->save();
			return true;
			}
			else return false;
		}
	}



public function getBasePrice($quality,$FT,$ET,$Tol){
	# quality, product_type, ini_sysLE
$c1=Config::get('game.baseC1');
$c2=Config::get('game.baseC2');
$c3=Config::get('game.baseC3');
$c4=Config::get('game.baseC4');

return ($c1*$quality + $c2*$FT + $c3*$ET)*(1 + $c4*$Tol);
}

	public function createProduct(){
		//from POST request by God
		$input = Input::except('_token');			// unset($input['_token']);
		$user= Auth::user()->get();		// $user = User::find($id);		$id= 32;//Session::get('id'); 
		
		if($user->god && $user->category=='god'){
			$god=$user->god;
			if($this->check($god,$input)){ //THIS WILL ALREADY REDUCE GOD'S LE, make sure the product gets Added/
				$p = new Product();
				$p->god_id = $user->god->id;
				$p->being_funded=1;
				$p->is_launched=0;
				
				$p->time_when_created=time();

				foreach(array_keys($input) as $field){
					//ADD CONDN to ensure column exists in 

					$p->$field=$input[$field]; 				// if(property_exists($p,$field))
					echo $field." -> ".$input[$field];
					if($p->$field)echo " added.";echo "<br>";			
				}
				$p->save();
			}
		}
	} 
	public function createProductForm(){
		$user= Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		if($user->category=='god')return View::make('createProd')->with('user',$user);
		else return "You are not God!".Config::get('debug.login');
	} 



//list of products(is_launched) by one god-
	//make Array of this later
	public function selfProducts(){
		// $user = User::find(Session::get('id'));
		$user=Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		if($user->category!='god')return "You are not God!".Config::get('debug.login');

		$products = $user->god->products;
		foreach ($products as $product) {
			echo $product->id;
			echo " -> ";
			echo $product->name; //currently Null
			echo "<br>";
		}
	}


	public function home(){
		//HERE Login form will send request to.
		//Authenticate here ?!

		// $input = Input::all();
		// $id=35; //ADD FROM LOGIN FORM
		// // $Name=$input['name']; //request data
		// Session::put('name',$Name);
		// Session::put('id',$id);
		// $cat= User::find($id)->category;
		// Session::put('cat',$cat);
		return "delete this View";

	}


	public function homeView(){
		//THIS WILL BE THE LOGIN VIEW LATER,
		//HERE USER_ID WILL BE SET.
		return View::make('home')->with('location','KGL');//OR use //->with('location',"GHY");
	} 
	
}	
