<?php
	require_once('common/config.php');

	if (isset($_GET['code'])) {
		loadModel();

		$model = new model;
		$result = $model->authenticate_goa($_GET['code']);

		header('Location: '.WEB_ROOT);
	}
	else if (isset($_GET['gen_message_from_http'])) {
		loadModel();

		$model = new model;
		$model->gen_new_message_from_http($_POST);

		header('Location: '.WEB_ROOT);
	}
	else if (isset($_GET['gen_message'])) {
		loadModel();

		$model = new model;
		$model->gen_new_message_from_device(file_get_contents('php://input'));
	}
	else if (isset($_GET['message'])) {
		loadView('message');

		$view = new message_view;
		$view->gen_view();
	}
	else if (isset($_GET['logout'])) {
		loadModel();

		$model = new model;
		$result = $model->logout();

		header('Location: '.WEB_ROOT);
	}
	else if (isset($_GET['load_messages'])) {
		loadView('home');
		loadModel();

		$model = new model;
		$messages = $model->gen_messages($_SESSION['user_id']);

		$view = new home_view;
		$view->gen_content($messages);
	}
	else {
		loadView('home');
		loadModel();

		$model = new model;
		$messages = $model->gen_messages($_SESSION['user_id']);

		$view = new home_view;
		$view->gen_view($messages);
	}
?>