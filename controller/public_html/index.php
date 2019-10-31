<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	$handler->utopia_unit();
	
	$handler->render([
		'tag'   => 'home',
		'title' => 'Main',
		'user'  => $handler->user->data,
		'data'  => [
			'last_blocks' => $handler->get_last_blocks(),
			'summary'     => $handler->get_summary()
		]
	]);
	