<?php
return [
'catTables' => array("god" => "God","investor" => "Investor","farmer" => "Farmer"),
'basePrice' => array("seed" => 500,"fertilizer" => 2000,"land" => 7000),
'facGI' => 0.05,
'facFI' => 0.005,
'sysLE'=>God::all()->sum('le') + Investor::all()->sum('le') + Farmer::all()->sum('le'),

'godPercent'=>0.51,
'godReturns'=>0.81,

'baseC1'=>1.2,  //quality
'baseC2'=>0.01, //FT
'baseC3'=>0.1,  //ET
'baseC4'=>0.7,  //TOL
];
?>