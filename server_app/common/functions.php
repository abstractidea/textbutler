<?php
	// Global Functions
	function loadModel($name='') {
		if ($name=='') {
			require_once(CLASSES.'model/model.class.php');
		}
		else {
			require_once(CLASSES.'model/'.$name.'.model.class.php');
		}
	}
	function loadView($name='') {
		if ($name=='') {
			require_once(CLASSES.'view/view.class.php');
		}
		else {
			require_once(CLASSES.'view/'.$name.'.view.class.php');
		}
	}
	function loadCSS($name='') {
		return '<link rel="stylesheet" type="text/css" href="'.RESOURCES.'css/'.$name.'.css" />';
	}
	function loadJS($name='') {
		return '<script type="text/javascript" src="'.RESOURCES.'js/'.$name.'.js"></script>';
	}
	function loadSettings() {
		loadModel();
		loadModel('session');
		$model = new model;
		$session = new session;
		if ($session->session_verify()) {
			return '
				<li><a href="'.WEB_ROOT.'tools">Tools</a></li>
				<li><a href="'.WEB_ROOT.'logout">Logout</a></li>
			';
		}
		else {
			return '
				<li id="login"><a id="login_link" href="'.$model->gen_goa_url().'">Login</a></li>
			';
		}
	}
	function authorized($allowedRole=900) {
		loadModel('session');
		$model = new session;
		if ($model->session_verify()) {
			if ($_SESSION['ROLE_ID']>=$allowedRole) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
?>