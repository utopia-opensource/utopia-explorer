<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$channel_ID = \App\Model\Utilities::data_filter($_GET['code']);
	if($channel_ID == "") {
		$handler->user->redirect('/');
	}
	
	$handler->utopia_unit();
	
	$channel_data = $handler->get_channel($channel_ID);
	
	$handler->render([
		'tag'   => 'channel',
		'title' => $channel_data['title'] . " channel",
		'user'  => $handler->user->data,
		'data'  => $channel_data
	]);
	