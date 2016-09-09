<?php


/**

CLARIFICATIONS - 
***UNIT_PRICE IS CONSTANT, BID_PRICE UPDATES.
NUM_UNITS CAN DECREASE
***Bid price is price of a share, it is given by total_cost/total_shares. It increases so as to return decay loss to god.


PROBS-

**any point of keeping bid_price in investment ? we are returning godReturns on the spot! while funding bar is seen by $base_share * num_shares
				//seed GT shall later also depend on its sub-type


//pivot not well when nulti investments between same ppl
$num_shares = $inv->pivot->num_shares; //beware - same investor & same product can have multi investments
//The above needs to be replaced with a loop on $investor->products()->where('id',product_id

TODO-
//Make every created_at into Nullable default null
		LAND MENU : integrate & check amoghhController

	Show RFT in makeInvestment {array-$change = array('key1' => $var1, 'key2' => $var2, 'key3' => $var3);return json_encode(change);}
				Then the jquery script ...
			$.post("location.php", function(data){var duce = jQuery.parseJSON(data);var art2 = duce.key2;});	

	if($p->avl_shares)$p->being_funded=0;		         <-- What's correct place to update this?

		Decays & Threshold => SIGMOID FUNCTION MUST
			REDIRECTS : Character transitions,create product makeinvestment,etc buy/make functions redirect after submit 

/Tolerance should affect price thru a const, but thru the unit_price * (100+tol) 	- Done 
/all product parameters shall be confirmed positive before storing (price,FT(divides in bid_price) shall not be negative) - Sliders do that.
ALL KINDS OF SLIDERS SHALL NOT START FROM 0
SOLVED->//BUT SAVING IS RESETING THE TIMESTAMP in buyProduct too num_units--
$p->save() is changing created_at timestamp also (seen thru phpmyadmin)
Done- **write ajax for bid_price by prod id -> *changed Default=NULL in table* 
Done- unit_price shall get calc at backend again. 
**/

class admin extends \BaseController {
//CALL THIS FUNCTION EVERY 30MIN OR SO => To avoid Stucks
//MOVE THE FUNCTION BELOW TO SQL functions in Workbench
	public function boostLE(){
		$user=Auth::user()->get();
		if($user->id == 42){
			$boostG=1.2;
			$boostI=1.5;
			$boostF=1.8;
			$users=User::all();
			foreach ($users as $u) {
				
				$u->le *= $boostG;
				$u->save();
				echo $u->le." ";
			}
		}
		else echo "<a href='/login/42'> Login</a>";
	}


	public function updateUsers(){
		$user=Auth::user()->get();
		if($user->id == 42){
			$users=User::all();
			foreach ($users as $u) {
				$u->prev_time =time();;
				$u->save();
			}
		}
	}

}

/*
EACH FUNCTION SHALL ADD AN IF CONDN TO CHECK IF USER IS GOD/FARMER/INV -Done
	//USER MIDDLEWARE for checking it is a farmer & all.


 TODO-
# >Ajax for bid price? -> IMPLEMENT NOW 	


//USER MIDDLEWARE for checking it is a farmer & all.

#*** Admin Panel REQUIRED  boostLE/reset FT(time_when_created+= time passed) => the decay/FT/ET will still go on due to timestamp stuff in case server stops !


		    	// ->> CHANGED THIS to saving a column in user database.
#Target -

# >Make forms for Product class  (exclude necessary paras) -Done

# >Makeinvestment function. - Done

# >Make similar for fruit price setting 	-Done

# > buy fruit

#ajax for Decay & LE  - Done => but add it to master.

# thresholdHandle -Done
# decayHandle - Done

# >Ajax  CHANGING CHARACTER THR Check   

#> Ajax to update FT & GT. also do this inside makeInvestment


# >Land mini game -> returning land blocks array.

  // $k=Config::get('game.catTables');	 $le = $k[$active_cat]::where('user_id',$id)->first()->le;


    	// if(!Session::has('prev_time')){ //first time
		    	// 	Session::put('prev_time',$ctime);
		    	// }
		    	// $ptime = Session::get('prev_time');

s		    	// Log::info($ptime); // <--- THIS  is not taking the updated time 

**/