<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$pubkey = \App\Model\Utilities::data_filter($_GET['code']);
	if($pubkey == "") {
		$handler->user->redirect('/');
	}
	
	$handler->utopia_unit();
	
	$handler->render([
		'tag'   => 'pubkey',
		'title' => 'View pubkey owner',
		'user'  => $handler->user->data,
		'data'  => $handler->get_owner_info($pubkey)
	]);
	