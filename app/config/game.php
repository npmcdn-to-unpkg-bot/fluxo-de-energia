<?php
return [
'catTables' => array("god" => "God","investor" => "Investor","farmer" => "Farmer"),
'facGI' => 0.15,
'facFI' => 0.005,
'sysLE'=>God::all()->sum('le') + Investor::all()->sum('le') + Farmer::all()->sum('le'),

'godPercent'=>0.51,
];
?>