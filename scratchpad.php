<?php

namespace i;

include '../boot.php';


$iArray = new iArray('PHP','love','I');
echo $iArray->reverse()->implode(' ');
// I love PHP