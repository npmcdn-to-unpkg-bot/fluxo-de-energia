<?php
class UC extends \BaseController {
	
//call this function in master function that handles decay_LE ajax request of farmer
	public function growthCheck($user){
		
		$purchases = $user->farmer->purchases;
		foreach ($purchases as $purchase) {
			if($purchase->product->category == 'seed'){
				$plants = Fruit::where('purchase_id',$purchase->id)->where('in_progress',1)->get();
				foreach ($plants as $plant) {
					$time_elapsed = time() - $plant->plant_time;
					$purchased_seed = Purchase::find($plant->purchase_id); 
					$expiry = $purchased_seed->product->seed->GT;					
					if(($time_elapsed) * $plant->growth_factor >= $expiry)
						$this->makeFruit($plant);
				}
			}
		}
	}

	public function makeFruit($plant){
		$land = $fruit->purchased_land;
		$land->num_units++;							$land->save();
		$fruit = $plant;
		$fruit->ripe_time = time();
		$fruit->in_progress = 0;					$fruit->save();
		$user = Auth::user()->get();
		$user->le += $fruit->storage_le*0.5;		$user->save();
	}

	//ajax of farmer
	public function fruitExpiry($user){
		$purchases = $user->farmer->purchases;
		foreach ($purchases as $purchase) {
			if($purchase->product->category == 'seed'){
				$fruits = Fruit::where('purchase_id',$purchase->id)->where('in_progress',0)->get();
				foreach ($fruits as $fruit){
					$expiry = $fruit->ET;
					$time_elapsed = time() - $fruit->ripe_time;
					if($time_elapsed >= $expiry*60)
						$fruit->delete();
				}
			}
		}
	}

	//ajax of god
	public function fundingCheck($user){
		$products = $user->god->products()->where('being_funded',1)->get();
		foreach ($products as $product) {			
			$expiry = $product->FT;
			$time_elapsed = time() - strtotime($product->created_at);
			if($time_elapsed >= $expiry*60){
				$product->being_funded = 0;
				if($product->investments)$shares_bought = $product->investments->sum('num_shares');
				else Log::info("NO investments");
				$product->num_units = ($product->num_units * $shares_bought) / $product->total_shares;
				$product->save();
			}
		}
	}

	//ajax of god
	public function productExpiry($user){
		$products = $user->god->products()->where('being_funded',0)->get();
		foreach ($products as $product) {			
			$expiry = $product->ET;
			$time_elapsed = time() -  $product->launched_at;
			if($time_elapsed >= $expiry*60){
				$category = $product->category;
				$specific = $product->{$category}->delete();
				$product->delete();
			}
		}
	}

	    public function thresholdHandle(){
		//only ajax request
	    	$total=User::all()->sum('le');
	    	$facGI = Config::get('game.facGI');
	    	$facFI = Config::get('game.facFI');

		$thresholdGI= $facGI* $total; //this factor may depend on number of users ?!
		$thresholdFI= $facFI* $total;
		//Send this data to the graph.
		return array('total'=>$total,'thresholdGI'=>$thresholdGI,'thresholdFI'=>$thresholdFI);
	}
//TEST
	public function redeemLife(){
		$user = Auth::user()->get();
		$redeem = Input::get('redeem');
		if($redeem > $user->stored_le)
			return "Insufficient stored Life Energy";
		$user->le += $redeem;
		$user->stored_le -= $redeem;
		$user->save();
	}


//		Decays & Threshold => SIGMOID FUNCTION MUST
	public function decayHandle(){
		$user=Auth::user()->get();

    	$active_cat = $user->category; // Not Null in table
    	$char= $user->$active_cat;
///PUT FUNCTIONS TO UPDATE GT FT ET of user stuff deps on user type
//Make sure to change PRODUCT's being_funded = 0  & launched_at after FT ->being done at makeInvestment
    
//TEST
    	// if($active_cat=='farmer'){
    	// 	Log::info("GchecSTRD");
    	// 	$this->growthCheck($user);
    	// 	$this->fruitExpiry($user);
    	// 	Log::info("GchecEND");
    	// }
    	// if($active_cat=='god'){
    	// 	Log::info("FchecSTRD");
    	// 	$this->fundingCheck($user);
    	// 	$this->productExpiry($user);
    	// 	Log::info("FchecEND");
    	// }

    	if(!$user->prev_time){
    		$user->prev_time=time();$user->save();
    	}
    	$time_passed = time()-$user->prev_time;
    	$user->prev_time=time();$user->save();

    	if($char->decay)$decay = $char->decay;
	    	else return -1; // HOW TO SEND AJAX ERRORS?

	    	//Update decay-
	    	$new_decay=Config::get('game.facDecay')[$active_cat] * Config::get('game.sysLE');
	    	if($new_decay>0)$char->decay=$new_decay; $char->save();

	    	if($user->le - $decay*$time_passed > 10000)
	    		{	$user->le -= $decay*$time_passed;	    	$user->save();}
	    	$user->save();
	    	return $user->le;
	    }



	
	public function login($id=42){
		$user=User::find($id);
		if($user)Auth::user()->login($user);
		else return Config::get('debug.login');
	}

	public function logout(){
		$user= Auth::user()->get();
		if($user){
			echo $user->username." logged out";
			Auth::user()->logout();	
		}
		else echo "Already logged out <BR>".Config::get('debug.login');

	}

	function testUserData(){
		$user= Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		
		if($user){
			$total=Config::get('game.sysLE');
			$facGI = Config::get('game.facGI');

			$THR= $facGI* $total; //this factor may depend on number of users ?!
			var_dump($total);

	    	$active_cat = $user->category; // a string
	    	$char=$user->$active_cat;
	    	$cat = $user->category;
	    	echo $cat." LE-".$user->le."Name-".$user->username;
	    }
	}
}

#*** Admin Panel REQUIRED => in case light goes off OR server stops respondin, the decay will still go on due to timestamp stuff. !
# thresholdHandle -Done?
# >Make similar for fruit price setting
# > CHANGING CHARACTER THR CHeck   
# >Land mini game -> returning land blocks array.
