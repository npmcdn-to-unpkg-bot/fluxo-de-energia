	<?php
	// NOTE : config/session.php => CHANGED expire_on_close = false

	class FC extends \BaseController {
		
		public function testfn(){
			$inv = Investor::find(1);
			$prod = $inv->products->first();

			// var_dump($prods->first()->pivot);

			$investors= $prod->investors;
			$total_shares = $prod->total_shares;
			$prodname = $prod->category." ".$prod->id;
			echo $prodname." is first product invested by ".$inv->user->username;
			echo "<BR>Now lets get all investors who invested in this prod<BR>";
			foreach ($investors as $inv) {
				$invname= $inv->user->username;
				echo "<BR><BR>".$invname." investor in ".$prodname."<BR>";
				
				$num_shares = $inv->pivot->num_shares;
				$bid_price = $inv->pivot->bid_price;
				echo $num_shares." / ".$total_shares." -> ".$bid_price."<BR>" ;
				$prods = $inv->products;
				echo "All products invested in by ".$invname." for product ".$prodname;
				echo " are - <BR><BR>";

				foreach ($prods as $p) {
					echo $p->category." ".$p->id." -> ";
					$num_shares= $p->pivot->num_shares;
					$bid_price= $p->pivot->bid_price;
					echo $num_shares." ".$bid_price."<BR>";
				}			
				echo "<BR><BR><BR><BR>";

			}
		}



		public function check($input){
			//Life Energy price check
			// RFT positive check
			// avl_shares check
			//investor id,bid_price, set at backend

		$user= Auth::user()->get();  //THIS CHECK MAYBE NOT REQ.//Get user again in case someone goes back and resends form data-
		if($user->farmer && $user->category=='farmer'){ 

			$investor=$user->investor;

			if(!($input['num_shares']&& $input['product_id']))return false;

					$p=Product::find($input['product_id']); //other way is to reach thru saved $i
					if(!$p){echo "prod not found";return false;}
					if(!$p->being_funded){echo "Product is not being funded. <BR>";return false;}

					$ctime=time();
					$time_passed = ($ctime - $p->time_when_created)/60;
					
					//show RFT also
					$RFT = $p->FT - $time_passed; //Minutes
					echo "RFT ".$p->FT." - ".$time_passed." = ".$RFT."<BR>";

					if($RFT<=0){ //THIS SHOULD NOT COME
						echo "RFT is over. ".$p->FT." - ".$ctime." = ".$RFT."<BR>";

						return false;
					}


					if($p->avl_shares < (int)$input['num_shares']){ echo "Insufficient shares. Available shares(".$p->avl_shares.")"; return false;}
					else {
						$p->avl_shares -= (int)($input['num_shares']);
						$p->save();
						$price= (int)($input['num_shares'])* (int)($p->bid_price);

							//REAL TIME THR <- repeated use. 
						$total=Config::get('game.sysLE'); //CHECK IF THIS WORKS ALL TIME
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
		else echo "Don't play fool with me !"; //Should not reach here . 
	}



	public function buyProductForm(){
		$user= Auth::user()->get();
		if(!$user)return Config::get('debug.login');
		if($user->farmer && $user->category=='farmer'){
			return View::make('invest')->with('user',$user);
		}
		else return "You are not Farmer!";
	} 



	//USER MIDDLEWARE for checking it is a farmer & all.
	public function buyProduct(){
			//request comes here from listProducts-
		$input = Input::all();
		$user = Auth::user()->get();
			//CONDN
		if($user->farmer && $user->category=='farmer'){

			$farmer = $user->farmer; // opens farmer cat of user, ADD CONDITION ABOVE
			echo "Current LE = ".$farmer->le."<BR>";


			if($this->check($input)){ //THIS WILL ALREADY REDUCE LE, make sure the product gets Added
				$prod=Product::find($input['product_id']);
				$num = $input['num'];
				$curr_bid_price = $prod->bid_price;
				$prod_price	=$num * $curr_bid_price;

				$god = $prod->god;
			
			//Notes this in purchases table
				$newPurchase = new Purchase();
				$newPurchase->farmer_id = $farmer->id;
				$newPurchase->product_id = $prod->id;
				$newPurchase->num_units = $num;
				$newPurchase->buy_price = $prod_price;
				$newPurchase->save();

				$farmer->le -= $prod_price;									$farmer->save();
				$prod->num_units -= $num; 									$prod->save();

			#Distribute the earnings among investors & gods
				$investors= $prod->investors;
				$total_shares = $prod->total_shares;
				foreach ($investors as $inv) {

					$num_shares = $inv->pivot->num_shares;
					$percentage = $num_shares/$total_shares;
					$inv->le+= $percentage * $prod_price;						$inv->save();
				}

				$god->le += Config::get('game.godPercent') * $prod_price;		$god->save();

				echo "Success. Now LE = ".$farmer->le;
			}
			else echo "<BR>Transaction failed. ";
		}
		else echo " Not Investor. Don't play fool with me !";
		echo "You are not Farmer!".Config::get('debug.login');
	}


}
	/**
	Now notify God & Investor that they got profits from a purchase.
	You can update this in purchase DB which will be sent to them.
	//following will be in GC & IC.
	for god = god->products->purchases
	for inv = foreach (investors->products->purchase) // inform multi investors
	**/




	/**

	def isValidInvestment(current_product, user_inv,client_num_shares):
	    
	   	# Check 1: Also check whether the investment made has that much unit left.
	    #Check 2:: return true when the investment that is demanded by the investor is valid according to the formula
	    # Check 3: if the RFT remaining funding time > zero       // safe side check
	    CL=user_inv.player_LE
	    ET= current_product.product_ET
		RFT=getRFT(current_product)
		curr_decay=user_inv.player_decay
		curr_shares=current_product.avl_share_units
		BID =current_product.curr_bid_price
		inv_cost = client_num_shares*BID
		THRESHOLD = user_inv.thresholdFI 		    #thresholdGI / thresholdFI/ thresholdF
		condition1 = curr_shares > client_num_shares
		condition2 = RFT>0
	    condition3 = CL - (ET + RFT )*(curr_decay)- inv_cost > THRESHOLD
	    return condition1 && condition2 && condition3
	 
	 def makeInvestment(current_product, user_inv,client_num_shares):
	    #function to set the basic parameters in the product details
	   # var client_num_shares #from the client number of shares
	   	current_product.avl_share_units -=client_num_shares  #decrease the value of the remaining product
	    BID=current_product.curr_bid_price
	    price= client_num_shares* BID   
	    user_inv.player_LE-=price    #decrease the amount of life energy by the price invested.
	    COST= current_product.COST
	    current_product.total_amt_funded  -= client_num_shares*COST
	    current_product.owner_god.player_LE += client_num_shares*(BID-COST)  #immediately 
	    
	    #CAN FOREIGN KEY GIVE ACCESS TO ALBUM DATA FROM SONG > YES

	     
	     # FARMERS BUYING THE PRODUCT - F.looseLE, G.gainLE
	def isValideBuyProduct(user_farmer,selected_product, num_prods_bought):
		CL = user_farmer.player_LE
		price = selected_product.curr_sell_price * num_prods_bought
		THR=user_farmer.thresholdF
		return (CL-price)>THR

	def buyProduct(user_farmer,selected_product, num_prods_bought):
		if isValidBuyProduct(user_farmer,selected_product, num_prods_bought):
			selected_product.num_of_units -= num_prods_bought
		#CONFIRM SYNTAX
			user_farmer.inventory_set.add(selected_product)

			price = selected_product.curr_sell_price * num_prods_bought
			#reduce LE, add some conditions here later.
			user_farmer.looseLE(price)

			# payback to INVESTORS & GOD acc to their percents

		else :
			ERR("Transaction Failed");
	***/