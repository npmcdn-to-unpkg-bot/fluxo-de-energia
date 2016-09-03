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



//USER MIDDLEWARE for checking it is a farmer & all.
	public function buyProduct(){
		//request comes here from listProducts-
		$input = Input::all();
		$user = Auth::user()->get();
		//CONDN
		$farmer = $user->farmer; // opens farmer cat of user, ADD CONDITION ABOVE
		
		$prod=Product::find($input['product_id']);
		$num = $input['num'];

		$god = $prod->god;
		$curr_bid_price = $prod->bid_price;
		$prod_price	=$num * $curr_bid_price;


		$farmer->le -= $prod_price;
		$prod->num_units-=$num;
		$prod->save();
		#Distribute the earnings among investors & gods
		$investors= $prod->investors;
		$total_shares = $prod->total_shares;
		foreach ($investors as $inv) {
			
			$num_shares = $inv->pivot->num_shares;
			$percentage = $num_shares/$total_shares;
			$inv->le+= $percentage * $prod_price;
			$inv->save();
		}

		$god->le += Config::get('game.godPercent') * $prod_price;
		$god->save();


		
	} 
	
}