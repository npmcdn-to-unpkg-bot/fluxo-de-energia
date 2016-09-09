<?php
class IC extends \BaseController {

		//make Array of this later
	public function godProducts(){
		echo "All products being funded :";
		$gods= God::all();
		foreach ($gods as $god) {
			echo " God : ".$god->user->username."<br>";
			$products = $god->products->all(); // $products = Product::where('being_funded',1)->where('god_id',$god->id);// var_dump($products);
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
				echo " num_shares:";
				echo $product->num_shares; //currently Null
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
			}
		}
		echo "<br>";
	}
}

	//playing with pivot
public function testfn(){
	$inv = Auth::user()->get()->investor;
	if(!$inv){
		echo "User not yet investor! Choosing default. ";
		$inv = Investor::find(1);
	}
	$prod = $inv->products->first();

	$total_shares = $prod->total_shares;
	$prodname = $prod->category." ".$prod->id;
	echo $prodname." is first product invested by ".$inv->user->username;
	echo "<BR>Now lets get all investors who invested in this prod (same name can repeat if thee invested repeated)<BR>";
	$investors= $prod->investors;
	foreach ($investors as $inv) {
		$invname= $inv->user->username;
		echo "<BR><BR>".$invname." invested in ".$prodname." at  ";

		$num_shares = $inv->pivot->num_shares;
		$bid_price = $inv->pivot->bid_price;
		echo $num_shares." / ".$total_shares." -> ".$bid_price."<BR><BR>" ;
		
		echo "selfInvestments by ".$invname." are - <BR><BR>";
				$prods = $inv->products()->orderBy('id','asc')->get(); //where $inv = $prod->investors
				foreach ($prods as $p) {
					echo $p->category." ".$p->id." ----- ";
					$num_shares= $p->pivot->num_shares;
					$bid_price= $p->pivot->bid_price;
					echo $num_shares." shares at price ".$bid_price."<BR>";
				}			
				echo "<BR><BR>";

			}
		}

		public function testBid	(){
			$p=Product::find(132);
			$god=$p->god;
	$time_elapsed= (time()-strtotime($p->created_at))/60; //Minutes
	echo "time_elapsed(min) : ".$time_elapsed."<BR>";
	$loss=  $god->decay * $p->FT;
	echo "loss".$loss."<BR>";
	echo "num_units".$p->num_units."<BR>";
	echo "total_shares".$p->total_shares."<BR>";
	echo "total_cost".$p->total_cost."<BR>";
	
	$base_share=($p->total_cost/$p->total_shares)*(1-Config::get('game.godPercent'));

	$bid_price= $base_share + ($loss/$p->total_shares)*($time_elapsed/$p->FT);
	echo "BP: ".$bid_price;
}


public function calcFruitPrice($farmer,$fruit){
	$time_elapsed=time() - $fruit->ripe_time;
	$loss=  $farmer->decay * $fruit->ET;

	if($fruit->purchase->num_units)$sp=$fruit->unit_price + ($loss/$fruit->purchase->num_units)*($time_elapsed/$fruit->ET);
	else echo "No units left";
	$fruit->sell_price = $sp; $fruit->save();
	return  $sp;
}

public function buyFruit(){
		//fruit_id //buy_price //num_units
	if(Auth::user()->get()->category != 'investor')return "Not authorised";
	$investor = Auth::user()->get()->investor;

	$fruit = Fruit::find(Input::get('fruit'));
	if(!$fruit)echo "NO FRUIT ";
	if(!$fruit->in_progress){
		echo "NOT matured ";
		if($fruit->purchase)$farmer = $fruit->purchase->farmer;
		else echo "no purchase for fruit";
		Log::info($farmer);
			$sp = $this->calcFruitPrice($farmer,$fruit);  //find the selling price
			$farmer->user->le += $sp;									$farmer->user->save();
			$investor->user->le -= $sp;
			$investor->user->stored_le += $fruit->storage_le;			$investor->user->save();
			$fruit->delete();
			echo "succes";
		}
		else echo "Transaction failed";
	}

	public function bidHandle(){
		$id=(int)Input::get('product_id');
		$p = Product::find($id);
		if(!$p || !$p->being_funded || !$p->god)return array('bid_price'=>0,'RFT'=>0);;
		$bid_price= $this->calcBidPrice($p);
		$time_elapsed= (time()-strtotime($p->created_at))/60; //Minutes
		$RFT = $p->FT - $time_elapsed; //Minutes
		return array('bid_price'=>$bid_price,'RFT'=>$RFT);
	}

	public function calcBidPrice($p){
		if(!($p->FT>0 && $p->total_shares>0))return 0;
		$time_elapsed= (time()-strtotime($p->created_at))/60; //Minutes
	  	$RFT = $p->FT - $time_elapsed; //Minutes
	  	if($RFT<=0){
            $p->being_funded=0; $p->save(); //time is over now          <-- What's correct place to update this?
            return 0;
        }

        $loss=  $p->god->decay * $p->FT;
        $base_share=($p->total_cost/$p->total_shares)*(1-Config::get('game.godPercent'));
        $godReturns = Config::get('game.godReturns');
        $bid_price= $base_share + $godReturns*($loss/$p->total_shares)*($time_elapsed/$p->FT);

		$p->bid_price=$bid_price; $p->save(); //update bid price here ?!

		return  $bid_price;
	}
		//from POST request by Investor
	public function makeInvestment(){
		$flag =0;
		$input = Input::except('_token');
		if(!($input['num_shares']&& $input['product_id']))return "Input not read.";
		$num_shares = (int)($input['num_shares']);

		$user= Auth::user()->get();
		$LE=$user->le; echo "Current LE = ".$LE."<BR>";
		$p=Product::find($input['product_id']);

		if(!$p)return "prod not found";
		if(!$p->being_funded)return "(FT is over) Product is not being funded. <BR>";  

		if(!$p->avl_shares){
		$p->being_funded=0; $p->save(); //shares are over now          <-- What's correct place to update this?
		return "0 avl shares";
	}
		$god=$p->god; //accessed to increase god LE
		if(!$god)return "This product doesn't have an owner!"; //you may safe delete this products
		
		$bid_price=$this->calcBidPrice($p); //this also updates to latest bid price & being_funded.// RFT positive check here. 
		if($bid_price==0)"Err in bid price (0)!";

		// avl_shares check
		if($p->avl_shares < $num_shares){ 
			echo " Buying available shares(".$p->avl_shares.")"; 
			$num_shares = $p->avl_shares;
		}
		
		echo " bid_price ".$bid_price;
		$price= $num_shares * $bid_price;

		$total=Config::get('game.sysLE'); //CHECK IF THIS WORKS ALL TIME
		$THR= $total * Config::get('game.facFI'); //this factor may depend on number of users ?!
		//Life Energy price check /successful here.
		if($LE - $price > $THR)
		{
		// product's avl shares cut
			$p->avl_shares -=  $num_shares; 				
		//Investor's le cut
			$user->le -= $price;									$user->save();
		//GIVE BACK decay LE to God
			$base_share=($p->total_cost/$p->total_shares)*(1-Config::get('game.godPercent'));
			$excess_bid = $bid_price - $base_share; //changed to base_share from unit_price
			
			$god->user->le += $num_shares*$excess_bid; 					$god->user->save();$god->save();
			
			$p->save();
			$flag=1;
		}
		else return " Insufficient LE : ".$THR." >  ".$LE."-".$price;

		if($flag==1)
		{ //THIS WILL ALREADY REDUCE LE, make sure the investment gets Added
			$i=new Investment();
			$i->investor_id = $user->investor->id;
			$i->product_id = $p->id;
			$i->num_shares = $num_shares;
			$i->bid_price = $p->bid_price;
			$i->save();	
			echo $i->id." Success. Now LE = ".$user->le;
		}
		//never comes here? coz returns everywhere
		else echo "Unexpected?! <BR>Transaction failed. ";
	} 


}	

