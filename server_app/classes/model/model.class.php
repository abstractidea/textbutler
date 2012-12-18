<?php
	require_once(LIBRARIES.'google-api-php-client/src/Google_Client.php');
	require_once(LIBRARIES.'google-api-php-client/src/contrib/Google_Oauth2Service.php');
	class model {
		public function gen_token($length=16) {
			$char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';    
			$string = '';

			for ($i=0; $i<$length; $i++) {
				$string .= $char{mt_rand(0, strlen($char)-1)};
			}

			return $string;
		}
		private function parse_json($data='') {
			if ($data=='') {
				return FALSE;
			}
			else {
				$jsonArray = json_decode($data);

				return $jsonArray;
			}
		}
		public function collect_json() {
			$contents = file_get_contents('php://input');
			$json = $this->parse_json($contents);
			
			return $json;
		}
		public function send_gcm_message($request='') {
			$client_id = $request['device_id'];
			$authorization = $request['authorized'];
			$communication_fields = array(
				'registration_ids' => array($client_id),
				'data' => array(
					'device_registration_id'=>$client_id,
					'user_id'=>'temp_user_id_0123',
					'authorization'=>$authorization
				)
			);
			$headers = array(
				'Authorization: key='.GCM_API_KEY,
				'Content-Type: application/json'
			);
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, GCM_SEND_URL);
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($communication_fields));

			$result = curl_exec($curl);
			curl_close($curl);
		}
		public function gen_new_message_from_http($message='') {
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);

			$id = $db->real_escape_string(htmlspecialchars($message['user_id']));
			$sender = $db->real_escape_string(htmlspecialchars($message['sent_from']));
			$message = $db->real_escape_string(htmlspecialchars($message['message']));

			$query = "INSERT INTO text (user_id, sent_from, message) VALUES ('$id', '$sender', '$message')";
			$db->query($query);

			$db->close();
		}
		public function gen_new_message_from_device($raw) {
			$json = json_decode($raw);

			$sms_sender = $json->sms_sender;
			$sms_message = $json->sms_message;
			$auth_token = $json->auth_token;
			$user_google_id = $json->user_google_id;
			$sms_message_timestamp = $json->sms_message_timestamp;

			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);

			$sms_sender = $db->real_escape_string(htmlspecialchars($sms_sender));
			$sms_message = $db->real_escape_string(htmlspecialchars($sms_message));
			$user_google_id = $db->real_escape_string(htmlspecialchars($user_google_id));

			$query = "INSERT INTO text (user_id, sent_from, message, sms_message_timestamp) VALUES ('$user_google_id', '$sms_sender', '$sms_message', '$sms_message_timestamp')";
			$db->query($query);
			$row_id = $db->insert_id;

			$query = "SELECT * FROM text WHERE id='$row_id' && user_id='$user_google_id' LIMIT 1";
			$query_result = $db->query($query);
			$message_check = $query_result->fetch_object();

			if ($message_check) {
				$response = 'Success';

				echo json_encode($response);
			}
			else {
				$response = 'Failure';

				echo json_encode($response);
			}

			$query_result->close();
			$db->close();
		}
		public function check_existing_user($id='') {
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$query = "SELECT * FROM users WHERE user_id='$id' LIMIT 1";

			$query_result = $db->query($query);
			$user = $query_result->fetch_object();
			$query_result->close();
			$db->close();

			if (isset($user->id)) {
				return true;
			}
			else {
				return false;
			}
		}
		public function update_existing_user($user_info='', $tokens='') {
			$user_id = $user_info['id'];
			$r_token = $tokens->refresh_token;
			$a_token = $tokens->access_token;
			$email = $user_info['email'];
			$verified_email = $user_info['verified_email'];
			$full_name = $user_info['name'];
			$given_name = $user_info['given_name'];
			$family_name = $user_info['family_name'];
			$profile_link = $user_info['link'];
			$image_link = $user_info['picture'];
			$gender = $user_info['gender'];
			$birthday = $user_info['birthday'];
			$locale = $user_info['locale'];

			if ($verified_email) {
				$verified_email = 1;
			}
			else {
				$verified_email = 0;
			}
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$query = "UPDATE users SET refresh_token='$r_token', access_token='$a_token', email='$email', verified_email='$verified_email', full_name='$full_name', given_name='$given_name', family_name='$family_name', profile_link='$profile_link', image_link='$image_link', gender='$gender', birthday='$birthday', locale='$locale' WHERE user_id='$user_id'";
			$db->query($query);
			$db->close();
		}
		public function create_new_user($user_info='', $tokens='') {
			$user_id = $user_info['id'];
			$r_token = $tokens->refresh_token;
			$a_token = $tokens->access_token;
			$email = $user_info['email'];
			$verified_email = $user_info['verified_email'];
			$full_name = $user_info['name'];
			$given_name = $user_info['given_name'];
			$family_name = $user_info['family_name'];
			$profile_link = $user_info['link'];
			$image_link = $user_info['picture'];
			$gender = $user_info['gender'];
			$birthday = $user_info['birthday'];
			$locale = $user_info['locale'];

			if ($verified_email) {
				$verified_email = 1;
			}
			else {
				$verified_email = 0;
			}
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$query = "INSERT INTO users (user_id, refresh_token, access_token, email, verified_email, full_name, given_name, family_name, profile_link, image_link, gender, birthday, locale) VALUES ('$user_id', '$r_token', '$a_token', '$email', '$verified_email', '$full_name', '$given_name', '$family_name', '$profile_link', '$image_link', '$gender', '$birthday', '$locale')";
			$db->query($query);
			$db->close();
		}
		public function gen_goa_url() {
			$client = new Google_Client();
			$client->setClientId(OAUTH_CLIENT_ID);
			$client->setClientSecret(OAUTH_CLIENT_SECRET);
			$client->setRedirectUri(OAUTH_REDIRECT_URI);
			$client->setDeveloperKey(OAUTH_API_KEY);
			$oauth2 = new Google_Oauth2Service($client);

			return $client->createAuthUrl();
		}
		public function gen_messages($id='') {
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$query = "SELECT * FROM text WHERE user_id='$id' ORDER BY sms_message_timestamp DESC";
			$query_result = $db->query($query);

			while ($message_row = $query_result->fetch_object()) {
				$messages[] = $message_row;
			}

			$query_result->close();
			$db->close();

			return $messages;
		}
		public function logout() {
			$client = new Google_Client();
			$client->setClientId(OAUTH_CLIENT_ID);
			$client->setClientSecret(OAUTH_CLIENT_SECRET);
			$client->setRedirectUri(OAUTH_REDIRECT_URI);
			$client->setDeveloperKey(OAUTH_API_KEY);

			session_unset();
			//$client->revokeToken();
		}
		public function authenticate_goa($data='') {
			$client = new Google_Client();
			$client->setClientId(OAUTH_CLIENT_ID);
			$client->setClientSecret(OAUTH_CLIENT_SECRET);
			$client->setRedirectUri(OAUTH_REDIRECT_URI);
			$client->setDeveloperKey(OAUTH_API_KEY);

			$oauth2 = new Google_Oauth2Service($client);

			if (isset($_GET['code'])) {
				$client->authenticate($_GET['code']);
				$_SESSION['token'] = $client->getAccessToken();
			}

			if (isset($_SESSION['token'])) {
				$client->setAccessToken($_SESSION['token']);
			}

			if ($client->getAccessToken()) {
				$user = $oauth2->userinfo->get();

				// These fields are currently filtered through the PHP sanitize filters.
				// See http://www.php.net/manual/en/filter.filters.sanitize.php
				$_SESSION['user_email'] = $user['email'];
				$_SESSION['user_image'] = $user['picture'];
				$_SESSION['user_id'] = $user['id'];

				// The access token may have been updated lazily.
				$_SESSION['token'] = $client->getAccessToken();
			}

			$tokens = json_decode($_SESSION['token']);
			$user_exists = $this->check_existing_user($user['id']);

			if ($user_exists) {
				$this->update_existing_user($user, $tokens);
			}
			else {
				$this->create_new_user($user, $tokens);
			}

			

			return $result;
		}
		public function authenticate_client($client_info='') {
			$token = $client_info->token;
			$event_whitelist = array();

			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$query = "SELECT * FROM users_information WHERE token='$token' LIMIT 1";
			$result = $db->query($query);
			$username = $result->fetch_object();
			$username = $username->username;
			$result->close();

			$query = "SELECT * FROM whitelist WHERE event_id='$client_info->event_id'";
			$result = $db->query($query);

			while ($row = $result->fetch_object()) {
				$event_whitelist[] = $row->username;
			}
			$result->close();
			$db->close();
			
			if (in_array($username, $event_whitelist)) {
				return $username;
			}
			else {
				return FALSE;
			}
		}
		private function dbEncode($input='') {
			$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB);
			$input = htmlspecialchars($input);
			$input = $db->real_escape_string($input);
			$db->close();

			return $input;
		}
		private function dbDecode($input='') {
			$input = stripslashes($input);

			return $input;
		}
	}
?>