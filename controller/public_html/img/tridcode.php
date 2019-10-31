<?php
	require_once __DIR__ . "/../../vendor/autoload.php";
	$code = \App\Model\Utilities::data_filter($_GET['code']);
	if(strlen($code) != 64) {
		exit;
	}
	
	$tridCode = new \App\Model\TRIDcode($code);
	$tridCode->render(15, 0);
	