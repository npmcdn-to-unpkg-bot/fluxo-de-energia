<?php


class GC extends \BaseController {
	//****  UPDATE THESE BELOW, ANALOGUS TO makeInvestment Check ****//

//More to come- sale stats
	public function selfProducts(){
		$user=Auth::user()->get();
		$products = $user->god->products;
		foreach ($products as $product) {

			if($product->being_funded == 1){

				echo $product->id;
				echo " name:";
				echo $product->name; //currently Null
				echo " ET:";
				echo $product->ET; //currently Null
				echo " FT:";
				echo $product->FT; //currently Null
				echo " num_units:";
				echo $product->num_units; //currently Null
				echo " quality:";
				echo $product->quality; //currently Null
				echo " total_cost:";
				echo $product->total_cost; //currently Null
				echo " being_funded:";
				echo $product->being_funded; //currently Null
				echo " description :";
				echo $product->description; //currently Null
				echo " category:";
				echo $product->category; //currently Null
				echo "<br>";


				echo "Total ".$product->total_shares." ";
				echo "Avl ".$product->avl_shares." ";
				$allinv=$product->investments()->orderBy('investor_id')->get();
				foreach($allinv as $invm){
					$invt= $invm->investor;
					if(!$invt->user){echo "nope "; continue;}
					echo $invt->user->username;
					echo "(";
					echo $invm->num_shares;
					echo " ";
					echo $invm->num_shares * $invm->bid_price;
					echo ")  ";
				}

				echo "<br>";
				echo "<br>";
			}

			else {
				echo "Funding over, show sell stats using purchases here.<br>";
			}

		}
	}
	public function check($user,$input){
		//FT ET positive check
		if(!($input['num_units']&&$input['category']&& $input['unit_price'] && $input['FT']>0 && $input['ET']>0 ))
		{
			echo "FT ,ET not ready";
			return false;
		}
		else {
			$category = $input['category'];
			// var_dump($category);
			if(!($category=="seed" ||$category=="fertilizer" ||$category=="land")){
				echo "Wrong category ";			return false;
			}
			$price= (int)($input['num_units'])* (int)($input['unit_price']);
			$LE=$user->le;

			$total=User::all()->sum('le');
			$facGI = Config::get('game.facGI');
			$THR= $facGI* $total; //this factor may depend on number of users ?!

		//Life Energy price check
			if($LE - $price > $THR)
			{
				$user->le -= $price;
				$user->save();
				return true;
			}
			else {
				echo "err".$THR;
				return false;}
			}
		}


		public function getBasePrice($quality,$FT,$ET,$Tol,$type){
	# quality, product_type, ini_sysLE
			$c1=Config::get('game.baseC1');
			$c2=Config::get('game.baseC2');
			$c3=Config::get('game.baseC3');
			$c4=Config::get('game.baseC4');
			$bps=Config::get('game.basePrices');
			$bp=$bps[$type];
			return $bp*($c1*$quality + $c2*$FT + $c3*$ET)*(1 + $c4*$Tol);
		}

		public function createProduct(){
		//from POST submit request by God
		$input = Input::except('_token','Tol'); //,'unit_price' too ! 
		$user= Auth::user()->get();		 
		echo "Current LE = ".$user->le."<BR>";

		if( $this->check($user,$input) ){ //THIS WILL ALREADY REDUCE GOD'S LE, make sure the product gets Added/
		$p = new Product();
		$p->god_id = $user->god->id;
		$p->being_funded=1;
		foreach(array_keys($input) as $field){
		$p->$field=$input[$field]; 				// if(property_exists($p,$field))
			echo $field." :".$input[$field];
			if($p->$field)echo " added.";echo "<br>";			
		}
		$p->unit_price = $this->getBasePrice($p->quality,$p->FT,$p->ET,Input::get('Tol'),$p->category);
		echo "Re: unit_price:".$p->unit_price."<BR>";
		$p->total_cost = $p->unit_price * $p->num_units;
		$p->avl_shares = $p->total_shares;
		$p->bid_price = $p->total_cost/$p->total_shares;
		$p->save();

		$user->save();
		echo "Now LE = ".$user->le."<BR>";

		$cat=$input['category'];
		if($cat == 'seed' ){
			$s=new Seed();
			$s->product_id = $p->id;
				//seed GT shall later also depend on its sub-type
			$s->GT = $input['quality']*Config::get('game.seedGT');

				//quality_factor moved to FRUIT !
				// $seedQual=Config::get('game.seedQual');
				// $s->quality_factor= $seedQual*$input['quality'];

			$s->save();
		}
		else if($cat == 'fertilizer' ){
			$s=new Fertilizer();
			$s->product_id = $p->id;
			$s->save();
		}
		else if($cat == 'land' ){
			$s=new Land();
			$s->product_id = $p->id;
			$s->save();
		}
		$p->save();

	}
} 

}
