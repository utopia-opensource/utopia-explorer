<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	$handler->utopia_unit();
	
	$handler->render([
		'tag'   => 'stats',
		'title' => 'Statistics',
		'user'  => $handler->user->data,
		'stats'  => $handler->get_stats()
	]);
	