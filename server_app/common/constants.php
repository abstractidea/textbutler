<?php
	// Developer Emails
	define('DEV_DAVID_M_EMAIL', 'david@serym.com');
	define('DEV_DAVID_B_EMAIL', '');
	define('DEV_MATTHEW_V_EMAIL', '');
	define('DEV_NATHAN_V', '');

	// Directory Structure - Server Constants
	define('CLASSES', ROOT.'classes/');
	define('LIBRARIES', ROOT.'libraries/');
	define('LOGS', ROOT.'logs/');

	// Directory Structure - Local Constants
	define('RESOURCES', WEB_ROOT.'resources/');

	// Database
	define('DB', 'palenous_hackathon_textbutler');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'palenous_hackath');
	define('DB_PASS', '4}Z}l$a0L^3v');
	define('DB_TABLE_USERS', 'users');
	define('DB_TABLE_ROLES', 'roles');
	define('DB_TABLE_USER_ROLES', 'user_roles');
	define('TOKEN_LENGTH', 32);

	// PHP Settings
	define('DATE_FORMAT', 'ymd');
	ini_set('error_log', LOGS.date(DATE_FORMAT).'_error.log');
	ini_set('log_errors', 1);
	ini_set('log_errors_max_len', 1024);

	// Resource Settings
	define('SERVER_URL', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

	// Session Settings
	session_set_cookie_params(604800, '/', '.serym.com');
	session_start();
	define('SESSION_ID_LENGTH', 16);
	define('LOGIN_SLEEP_TIME', 3);
	define('USERNAME_WHITELIST', '@.-_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
	define('USERNAME_MIN_LENGTH', 8);
	define('USERNAME_MAX_LENGTH', 64);
	define('PASSWORD_WHITELIST', '!@#$%^&*()._-+=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
	define('PASSWORD_SALT', 'B6t+bAy5@hfucTnvnW7Xt308#(rEFC=m');
	define('PASSWORD_MIN_LENGTH', 8);
	define('PASSWORD_MAX_LENGTH', 512);
?>