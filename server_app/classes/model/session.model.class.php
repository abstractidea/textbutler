<?php
	class session {
		public function id_gen($length=SESSION_ID_LENGTH) {
			$char = '!@#$%^&*()_-+=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$id = '';

			for ($i=0; $i<$length; ++$i) {
				$id .= $char{mt_rand(0, strlen($char)-1)};
			}

			return $id;
		}
		// Usernames may consist of Numbers, Letters, and non-adjacent underscores
		public function username_verify($username='') {
			if ($username=='') {
				return FALSE;
			}
			for ($i=0; $i<strlen($username); ++$i) {
				if (strstr(USERNAME_WHITELIST, $username{$i})) {
					continue;
				}
				else {
					return FALSE;
				}
			}
			if ((strstr($username, '__'))||(strstr($username, '--'))||(strstr($username, '_-'))||(strstr($username, '-_'))) {
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
		public function password_verify($password=FALSE) {
			if ($password==NULL) {
				log("NULL Password Received: ".$password);

				return FALSE;
			}
			else if (($password)&&(is_string($password))&&(strlen($password)>=PASSWORD_MIN_LENGTH)&&(strlen($password)<=PASSWORD_MAX_LENGTH)) {
				for ($i=0; $i<strlen($password); ++$i) {
					if (strstr(PASSWORD_WHITELIST, $password{$i})) {
						continue;
					}
					else {
						return FALSE;
					}
				}

				return TRUE;
			}
			else {
				return FALSE;
			}
			
		}
		public function password_hash($password=FALSE) {
			if ($this->password_verify($password)) {
				$salt = PASSWORD_SALT;
				$pass_salted = hash('sha1', $salt.$password.$salt);
				
				return $pass_salted;
			}
			else {
				return FALSE;
			}
		}
		public function password_check($username='', $password=FALSE) {
			if ($password) {
				$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);

				$query = "SELECT * FROM ".DB_TABLE_USERS." WHERE username='$username' LIMIT 1";
				$queryResult = $db->query($query);
				$users = $queryResult->fetch_object();
				$queryResult->close();

				$db->close();

				if ($password==$users->password) {
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
		public function password_update($new_password='') {
			if ($new_password!='') {
				$username = $_SESSION['USERNAME'];
				$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);

				$query = "UPDATE ".DB_TABLE_USERS." SET password='$new_password' WHERE username='$username'";
				$db->query($query);

				$db->close();
			}
		}
		public function authenticate($username='', $password='') {
			$password = $this->password_hash($password);
			if (($this->username_verify($username))&&($password)) {
				$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);

				$query = "SELECT * FROM ".DB_TABLE_USERS." WHERE username='$username' LIMIT 1";
				$queryResult = $db->query($query);
				$users = $queryResult->fetch_object();
				$queryResult->close();

				$query = "SELECT * FROM ".DB_TABLE_USER_ROLES." WHERE user_id='$users->id' LIMIT 1";
				$queryResult = $db->query($query);
				$user_roles = $queryResult->fetch_object();
				$queryResult->close();

				$query = "SELECT * FROM ".DB_TABLE_ROLES." WHERE id='$user_roles->role_id' LIMIT 1";
				$queryResult = $db->query($query);
				$roles = $queryResult->fetch_object();
				$queryResult->close();

				$db->close();

				// Check password
				if ($password==$users->password) {
					$_SESSION['USERNAME'] = $username;
					$_SESSION['SESSION_ID'] = $this->id_gen();
					$_SESSION['ROLE'] = $roles->rolename;
					$_SESSION['ROLE_ID'] = $user_roles->role_id;

					// Security Check
					$_SESSION['BROWSER'] = '';
					$_SESSION['OS'] = '';

					return TRUE;
				}
				else { // Optional: Make server sleep to protect against Brute Force Attacks.
					sleep(LOGIN_SLEEP_TIME);
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		public function deauthenticate() {
			session_unset();
			session_destroy();
		}
		public function session_verify() {
			if (isset($_SESSION['USERNAME'])&&isset($_SESSION['SESSION_ID'])&&isset($_SESSION['ROLE'])) {
				return TRUE;
			}
			else {
				// This will make sure no session variables are still storing information.
				session_unset();

				return FALSE;
			}
		}
	}
?>