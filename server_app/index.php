<?php
	require_once('common/config.php');

	if (isset($_GET['test_goa'])||isset($_REQUEST['code'])) {
		loadModel();

		$model = new model;
		$result = $model->authenticate_goa($_REQUEST['code']);

		if (isset($result['person_markup'])) {
			echo 'Person Markup: '.$result['person_markup'].'<br />';
		}
		if (isset($result['oauth_url'])) {
			echo '<a href="'.$result['oauth_url'].'">Connect!</a>';
		}
		else {
			echo '<a href="'.WEB_ROOT.'?logout">Logout</a>';
		}
	}
	else if (isset($_GET['logout'])) {
		loadModel();

		$model = new model;
		$result = $model->authenticate_goa('logout');

		header('Location: '.WEB_ROOT);
	}
	else {
		loadView('home');

		$view = new home_view;
		$view->gen_view();
	}
?>