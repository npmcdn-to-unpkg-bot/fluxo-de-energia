<?php
// NOTE : config/session.php => CHANGED expire_on_close = false

class IC extends \BaseController {

//list of products(being funded) by all gods-
	//make Array of this later
	public function godProducts(){
		$user=Auth::user()->get();
		if(!$user)return "Please log in again.";
		if($user->category!='investor')return "You are not Investor!";
		$gods= God::all();
		foreach ($gods as $god) {
			echo " God : ".$god->user->username."<br>";
			$products = $god->products->all(); // $products = Product::where('being_funded',1)->where('god_id',$god->id);// var_dump($products);
			foreach ($products as $product) {
				if($product->being_funded == 1){
					echo $product->id;
					echo " -> ";
				echo $product->name; //currently Null
				echo "<br>";
			}
		}
		echo "<br>";
	}
}


public function check($investor,$input){
		//Life Energy price check
		// FT positive check
		// avl_shares check
	if(!($input['num_shares']&& $input['product_id']))return false;

	$p=Product::find($input['product_id']); //other way is to reach thru saved $i
	if(!$p)return false;
	if(!$p->FT){echo "FT is over. ";return false;}

	if($p->avl_shares < (int)$input['num_shares']){ echo "Insufficient shares. Try reducing number of shares. (".$p->avl_shares.")"; return false;}
	else {
		$p->avl_shares -= (int)($input['num_shares']);
		$p->save();
		$price= (int)($input['num_shares'])* (int)($p->bid_price);

			//REAL TIME THR <- repeated use. THis can go into game.php BUT EXP LATER
		$gods=God::all()->sum('le');
		$invs=Investor::all()->sum('le');
		$farmers=Farmer::all()->sum('le');
		$total=$gods+$invs+$farmers;
		$facFI = Config::get('game.facFI');

		$LE=$investor->le;
			$THR= $facFI* $total; //this factor may depend on number of users ?!

			if($LE - $price > $THR)
			{
				$investor->le -= $price;
				$investor->save();
				return true;
			}
			else {echo $THR." >  ".$LE."-".$price." - > Insufficient LE. ";return false;}
		}
	}





	public function makeInvestment(){
		//from POST request by Investor
		$input = Input::except('_token');
		$user= Auth::user()->get();
		//investor id,bid_price, set at backend

		if($user->investor){
			$investor=$user->investor;
			echo "Current LE = ".$investor->le."<BR>";

			if($this->check($investor,$input)){ //THIS WILL ALREADY REDUCE LE, make sure the product gets Added
				$i=new Investment();
				$i->investor_id = $investor->id;
				$p=Product::find($input['product_id']); //other way is to reach thru saved $i
				$i->bid_price = $p->bid_price;
				foreach(array_keys($input) as $field){
					echo $field." -> ".$input[$field];
					$i->$field=$input[$field]; 				// if(property_exists($p,$field))
					if($i->$field)echo " added.";echo "<br>";			
				}
				$i->save();	
				echo "Success. Now LE = ".$investor->le;
			}
			else echo "<BR>Transaction failed. ";
		}
	} 
	
	public function makeInvestmentForm(){
		$user= Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		if($user->category=='investor'){
			return View::make('invest')->with('user',$user);
		}
		else return "You are not Investor!";
	} 
}	
