<?php
// NOTE : config/session.php => CHANGED expire_on_close = false

//USER MIDDLEWARE for checking it is a farmer & all.


class admin extends \BaseController {
//CALL THIS FUNCTION EVERY 30MIN OR SO => To avoid Stucks
//MOVE THE FUNCTION BELOW TO SQL functions in Workbench
	public function boostLE(){
		$user=Auth::user()->get();
		if($user->id == 42){
			$boostG=1.2;
			$boostI=1.5;
			$boostF=1.8;
			$gods=God::all();
			$invs=Investor::all();
			$farmers=Farmer::all();
			foreach ($gods as $g) {
				$g->le *= $boostG;
				$g->save();
			}
			foreach ($invs as $i) {
				$i->le *= $boostI;
				$i->save();
			}
			foreach ($farmers as $f) {
				$f->le *= $boostF;
				$f->save();
			}
		}
	}

}

#*** 		EACH FUNCTION SHALL ADD AN IF CONDN TO CHECK IF USER IS GOD/FARMER/INV

//USER MIDDLEWARE for checking it is a farmer & all.

#*** Admin Panel REQUIRED  boostLE/reset FT(time_when_created+= time passed) => the decay/FT/ET will still go on due to timestamp stuff in case server stops !


		    	// ->> CHANGED THIS to saving a column in user database.
#Target -
// ->DISCARDED # Sessions - done => stored user_id & active character
# >Make forms for Product class  (exclude necessary paras) -Done

# >Makeinvestment function. - Done, but not debugged for complex

# >Make similar for fruit price setting
# > buy fruit

#ajax for Decay & LE  - Done => but add it to master.

# thresholdHandle -Done
# decayHandle - Done
# >Ajax for bid price? -> IMPLEMENT NOW 	
#> Ajax to update FT & GT. also do this inside makeInvestment
# >Ajax  CHANGING CHARACTER THR Check   


# >Land mini game -> returning land blocks array.

  // $k=Config::get('game.catTables');	 $le = $k[$active_cat]::where('user_id',$id)->first()->le;


    	// if(!Session::has('prev_time')){ //first time
		    	// 	Session::put('prev_time',$ctime);
		    	// }
		    	// $ptime = Session::get('prev_time');

		    	// Log::info($ptime); // <--- THIS  is not taking the updated time 