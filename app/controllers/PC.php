<?php

class PC extends \BaseController {

	public function listFunds(){
    	$investors = Investor::all(); //investments function is defined in the model.
    	foreach ($investors as $investor) {
            // var_dump($investor->products);
            $name=$investor->user->username;
            echo "<br><br>"."Investor : ".$name."<br><br>";
            $inv_id=$investor->id;
    		$allinv=$investor->investments()->orderBy('product_id')->get();
    		foreach($allinv as $inv){
                $prod= $inv->product;
                echo "<BR> product ".$prod->id." ";
                $g=$prod->god;
                // var_dump($g);
                if($g){
                $name=$g->user->username;
    			echo "Owner : ".$name;
            }
                echo " ";
                echo $inv->product->category;
                echo " ";
                echo $inv->product->description;
                echo " ";
    			echo $inv->num_shares * $inv->bid_price;
                echo "<br>";
            }
			echo "<br>";
    		echo "<br>";
    		}
    	}

}
