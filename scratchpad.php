<?php

namespace o;

include 'boot.php';


$App = new oApp();


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

/**
 * @var $database oDatabaseMysql
 */
$database = o::init('Database');
$data = $database->query('SELECT * FROM table1');
$data = $data->findBy(null,'1');
prd($data);

