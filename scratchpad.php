<?php

namespace o;

include 'boot.php';


// $oArray = new oArray('PHP','love','I');
// echo $oArray->reverse()->implode(' ');
// I love PHP

/*
$oArray = new oArray('PHP','love','I');
$oArray->reverse();
foreach ($oArray as $foo=>$bar) {
	echo $bar;
}
*/

$database = o::init('Database');
$data = $database->query('SELECT * FROM table1');
prd($data);

