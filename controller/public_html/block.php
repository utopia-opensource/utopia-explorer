<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	$render_data = [
		'tag'   => 'block',
		'title' => 'Block',
		'user'  => $handler->user->data
	];
	
	$block_index = \App\Model\Utilities::checkINT($_GET['code']);
	if($block_index <= 0) {
		$handler->render($render_data); exit;
	}
	
	$handler->utopia_unit();
	
	$render_data['title'] = 'Block #' . $block_index;
	$render_data['data'] = [
		'block' => $handler->get_block($block_index)
	];
	
	$handler->render($render_data);
	