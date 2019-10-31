<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$query = \App\Model\Utilities::data_filter($_GET['search']);
	$query_type = $handler->logic->determineQueryType($query);
	
	switch($query_type) {
		default:
			$handler->user->redirect('/');
			break;
		case 1:
			//public key
			$handler->user->redirect('/pubkey/' . $query);
			break;
		case 2:
			//block index
			$handler->user->redirect('/block/' . $query);
			break;
		case 3:
			//channel id
			$handler->user->redirect('/channel/' . $query);
			break;
		case 4:
			//uNS record
			$handler->user->redirect('/uns/' . $query);
			break;
	}
	