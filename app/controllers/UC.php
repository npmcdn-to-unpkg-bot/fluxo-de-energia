<?php
// NOTE : config/session.php => CHANGED expire_on_close = false

class UC extends \BaseController {
	public function login($id=34){
		$user=User::find($id);
		if($id==34)return Config::get('debug.login');
		if($user)Auth::user()->login($user);
		else return "try another id.";
	}

	public function logout(){
		$user= Auth::user()->get();
		echo $user->username;
		Auth::user()->logout();

	}

	function testUserData(){
		$user= Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		
		if($user){
			$total=Config::get('game.sysLE');//God::all()->sum('le') + Investor::all()->sum('le') + Farmer::all()->sum('le');
			$facGI = Config::get('game.facGI');

			$THR= $facGI* $total; //this factor may depend on number of users ?!
			var_dump($total);

	    	$active_cat = $user->category; // a string
	    	$char=$user->$active_cat;
	    	$cat = $user->category;
	    	echo $cat." LE-".$char->le." Decay-".$char->decay." Name-".$char->user->username;
	    }
	}

	

	public function thresholdHandle(){
		//only ajax request
		$total=God::all()->sum('le') + Investor::all()->sum('le') + Farmer::all()->sum('le');
		$facGI = Config::get('game.facGI');
		$facFI = Config::get('game.facFI');

		$thresholdGI= $facGI* $total; //this factor may depend on number of users ?!
		$thresholdFI= $facFI* $total;
		//Send this data to the graph.
		return array('total'=>$total,'thresholdGI'=>$thresholdGI,'thresholdFI'=>$thresholdFI);
	}

	public function decayHandle(){
		// $id= Session::get('id'); // $user = User::find($id);
		$user=Auth::user()->get();

		if($user){
	    	$active_cat = $user->category; // Not Null in table
	    	$char=$user->$active_cat;

    		//$char decay is not diff for each user, it has only 3 diff values(for each cat) at a time
	    	if($char->decay)$decay = $char->decay;
		    	else $decay=100; // THIS SHOULD NEVER HAPPEN
		    	

		    	$ctime=time();
		    	if(!$user->prev_time){
		    		$user->prev_time=$ctime;$user->save();
		    	}
		    	$ptime=$user->prev_time;

		    	$time_passed = $ctime- $ptime;

		    	$user->prev_time=$ctime;$user->save();
		    	// Session::put('prev_time',$ctime); //Do this in user DB

		    	if($char->le)$char->le =$char->le - $decay*$time_passed;

		    	$char->save();
		    	return $char->le;
		    }
   	  else return 0; //when user not exist, NOT GIVING ERROR ON AJAX SIDE ?
   	}


   }
#*** 		EACH FUNCTION SHALL ADD AN IF CONDN TO CHECK IF USER IS GOD/FARMER/INV
#*** Admin Panel REQUIRED => in case light goes off OR server stops respondin, the decay will still go on due to timestamp stuff. !


		    	// ->> CHANGED THIS to saving a column in user database.
#Target -
#ajax request - Done
// ->DISCARD # Sessions - done => stored user_id & active character
# decayHandle - Done
# thresholdHandle -Done
# makeInvestment
# >Make forms for Product class (exclude necessary paras)
# >Makeinvestment function.
# >Make similar for fruit price setting
# >Ajax for bid price !	
# > CHANGING CHARACTER THR CHeck   
# >Land mini game -> returning land blocks array.

    	// $k=Config::get('game.catTables');	 $le = $k[$active_cat]::where('user_id',$id)->first()->le;


    	// if(!Session::has('prev_time')){ //first time
		    	// 	Session::put('prev_time',$ctime);
		    	// }
		    	// $ptime = Session::get('prev_time');

		    	// Log::info($ptime); // <--- THIS  is not taking the updated time 