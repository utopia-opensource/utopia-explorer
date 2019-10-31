<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$record_name = \App\Model\Utilities::data_filter($_GET['code']);
	if($record_name == "") {
		$handler->user->redirect('/');
	}
	
	$handler->utopia_unit();
	
	$handler->render([
		'tag'   => 'uns',
		'title' => 'uNS record',
		'user'  => $handler->user->data,
		'data'  => $handler->get_uns($record_name)
	]);
	