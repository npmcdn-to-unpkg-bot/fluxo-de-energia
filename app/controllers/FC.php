	<?php
	

	class FC extends \BaseController {
		
	


	public function plantSeed(){
		$plant = new Fruit;
		$plant->purchase_id = Input::get('purchase_id');
		$plant->land_id = Input::get('land_id');
		$plant->fertilizer_id = Input::get('fertilizer_id');
		$plant->quality_factor = Input::get('quality_factor');
		$plant->ET = Input::get('ET');
		$plant->save();

		$seed = Purchase::find($plant->purchase_id);
		if(!$seed)
			return "Error in seeds";

		$plant->in_progress = 1;
		$plant->plant_time = time();
		$plant_product = $plant->purchase->product;
		$c1=Config::get('game.fruitC1');
		$c2=Config::get('game.fruitC2');

		$plant->unit_price = $plant->storage_le * ($c1*$plant->quality_factor + $c2*$plant->ET) ; //calculate the unit_price of fruit.

		$fertilizer = Purchase::find($plant->fertilizer_id);
		$plant->storage_le = $plant->unit_price * $plant_product->quality;
		$plant->growth_factor = 0;

		if($fertilizer){
			$plant->growth_factor = $fertilizer->product->quality;
			$fertilizer->num_units--;
			$fertilizer->save();
		}
		$plant->growth_factor +=  $land->product->quality; //why add ?
		$plant->save();
		
		$land = Purchase::find($plant->land_id);
		$land->num_units--;
		$land->save();

		$seed->num_units--;
		$seed->save();
	}





/** buy product here -***/

	//NOT TESTED - update wrt makeInvestment
		public function calcBuyPrice($p,$god,$input){

		$time_elapsed= (time()-strtotime($p->created_at))/60; //Minutes
		$loss=  $god->decay * $p->ET;
		$num=$p->num_units;
		$godRecovery=Config::get('game.godRecovery');
		$buy_price=$p->unit_price + $godRecovery*($loss)/($num)*($time_elapsed)/($p->ET);
		echo "Buy price = ".$buy_price." Num= ".$input['num_units']."<BR>";
	return  $buy_price;
}

public function check($input){
			//Life Energy price check
			// num_units check
			//investor id,buy_price, set at backend

	$user= Auth::user()->get(); 

	$farmer=$user->farmer;

	if(!($input['num_units']&& $input['product_id']))return false;

			// RET positive check
	$p=Product::find($input['product_id']); //other way is to reach thru saved $i
	if(!$p){echo "prod not found";return false;}
	if($p->being_funded){echo "Product is not being sold. <BR>";return false;}


	$timepassed=(time()-$p->launched_at)/60;
	Log::info($p->launched_at);
		$RET = $p->ET-$timepassed; //Minutes
		if($RET<=0){ 
			echo "RET is over. ".$p->ET." - ".$timepassed." = ".$RET."<BR>";
			return false;
		}

//CHECK
		if($p->num_units < (int)$input['num_units']){ 
			echo "Insufficient Products. Available products(".$p->num_units.")"; return false;
		}
		else {
			$p->num_units -= (int)($input['num_units']); 				$p->save();
			$god = $p->god;
			$buy_price= $this->calcBuyPrice($p,$god,$input);

			$price= (int)($input['num_units'])* $buy_price;

							//REAL TIME THR <- repeated use. 
				$total=Config::get('game.sysLE'); //CHECK IF THIS WORKS ALL TIME
				$facFI = Config::get('game.facFI');

				$LE=$farmer->user->le;
			$THR= $facFI* $total; //this factor may depend on number of users ?!

			if($LE - $price > $THR)
			{
				$farmer->user->le -= $price;
				$farmer->save();
				$farmer->user->save();
				return true;
			}
			else {echo $THR." >  ".$LE."-".$price." - > Insufficient LE. ";return false;}
		}
	}

	public function buyProduct(){
			//request comes here from listProducts-
		echo "lets go. ";
		$input = Input::all();
		$user = Auth::user()->get();

			$farmer = $user->farmer; // opens farmer cat of user, ADD CONDITION ABOVE
			echo "Current LE = ".$farmer->user->le."<BR>";


			if($this->check($input)){ //THIS WILL ALREADY REDUCE LE, make sure the product gets Added

				$prod=Product::find($input['product_id']);
				$god = $prod->god;
				$num = $input['num_units'];
				$buy_price = $this->calcBuyPrice($prod,$god,$input);
				$prod_price	=$num * $buy_price;


			//Notes this in purchases table
				$newPurchase = new Purchase();
				$newPurchase->farmer_id = $farmer->id;
				$newPurchase->product_id = $prod->id;
				$newPurchase->num_units = $num;
				$newPurchase->buy_price = $prod_price; //should be $buy_price !
				$newPurchase->save();

				$farmer->user->le -= $prod_price;						$farmer->save();$farmer->user->save();
				$prod->num_units -= $num; 									$prod->save();

			#Distribute the earnings among investors & gods
				$total_shares = $prod->total_shares;
				$investors= $prod->investors;
				foreach ($investors as $inv) {

					$num_shares = $inv->pivot->num_shares; //beware - same investor & same product can have multi investments
					//The above needs to be replaced with a loop on $investor->products()->where('id',product_id)

					$percentage = $num_shares/$total_shares;
					$inv->user->le+= $percentage * $prod_price;						$inv->save();$inv->user->save();
				}

				$god->user->le += Config::get('game.godPercent') * $prod_price;		$god->save();$god->user->save();

				echo "Success. Now LE = ".$farmer->user->le;
			}
			else echo "<BR>Transaction failed. ";
		}


	}
	/**
	Now notify God & Investor that they got profits from a purchase.
	You can update this in purchase DB which will be sent to them.
	//following will be in GC & IC.
	for god = god->products->purchases
	for inv = foreach (investors->products->purchase) // inform multi investors
	**/
