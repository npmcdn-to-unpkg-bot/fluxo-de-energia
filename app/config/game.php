<?php
return [
'facDecay' => array("god" => 0.1*0.1/60,"investor" =>0.1*0.01/60,"farmer" => 0.1*0.001/60),
'catTables' => array("god" => "God","investor" => "Investor","farmer" => "Farmer"),
'basePrices' => array("seed" => 500,"fertilizer" => 2000,"land" => 7000),

'facGI' => 0.0005,
'facFI' => 0.00005,
'facF' => 0.0000005,
'sysLE'=>User::all()->sum('le'),

'godPercent'=>0.51,
'godReturns'=>0.81, //return of decay loss while funding
'godRecovery'=>0.1, // return of decay loss while ET


'baseC1'=>0.002,  //quality for product
'baseC2'=>0.005, //FT
'baseC3'=>0.002,  //ET
'baseC4'=>0.003,  //TOL

'fruitC1'=>0.003,  //quality for fruit
'fruitC2'=>0.002,  //ET

'seedGT'=>5/20,	//quality ranges from 1 to 100.
'baseQual'=>50,
'seedQual'=>0.8,

];
?>