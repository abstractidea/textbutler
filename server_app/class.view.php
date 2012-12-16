<?php
	class page {
		private function gen_header() {
			echo '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
				<html>
					<head>
						<title>Serym Transfer Service</title>
			';
		}
		private function gen_precontent() {
			echo '
					</head>
					<body>
						<script type="text/javascript">
							function Ajax(){
							var xmlHttp;
								try{	
									xmlHttp=new XMLHttpRequest();// Firefox, Opera 8.0+, Safari
								}
								catch (e){
									try{
										xmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer
									}
									catch (e){
									    try{
											xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
										}
										catch (e){
											alert("No AJAX!?");
											return false;
										}
									}
								}

							xmlHttp.onreadystatechange=function(){
								if(xmlHttp.readyState==4){
									document.getElementById(\'onlineUsers\').innerHTML=xmlHttp.responseText;
									setTimeout(\'Ajax()\',2000);
								}
							}
							xmlHttp.open("POST","http://portfolio.serym.com/transfer/resources/index.php",true);
							xmlHttp.send(null);
							}

							window.onload=function(){
								setTimeout(\'Ajax()\',2000);
							}
						</script>
						<div id="wrapper">
			';
		}
		private function gen_content() {
			if (isset($_SESSION['username'])) {
				echo '<h3>You are currently logged in as: '.$_SESSION['username'].'</h3>';
			}
			else {
				echo '<h3>You are not currently logged in.</h3>';
			}
			echo '<h3>Logged in users:</h3>';
			echo '<div id="onlineUsers">';
			define('NUMBER_OF_URLS', 0);

			function scanForImages($dir) {
				$files = scandir($dir);
				$numOfFiles = 0;
				foreach($files as $file) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					else {
						$result[] = $file;
						$numOfFiles = $numOfFiles+1;
					}
				}
				return array($result, $numOfFiles);
			}
			list($urls, $numOfURLS) = scanForImages('resources/users/files/');

			if (NUMBER_OF_URLS>0) {
				$num = NUMBER_OF_URLS;
			}
			else {
				$num = $numOfURLS;
			}

			$i = 0;
			while ($i<$num) {
				echo $urls[$i].'<br />';
				++$i;
			}
			echo '
							</div>
							<br />
							<br />
							<form action="'.WEB_ROOT.'" method="post">
								<input type="hidden" name="login" value="TRUE" />
								<input type="text" name="username" />
								<input type="password" name="password" />
								<input type="submit" value="Login" />
							</form>
							<br />
							<br />
							<form action="'.WEB_ROOT.'" method="post">
								<input type="hidden" name="logout" value="TRUE" />
								<input type="submit" value="Logout" />
							</form>
			';
		}
		private function gen_footer() {
			echo '
						</div>
					</body>
				</html>
			';
		}
		public function gen_page() {
			$this->gen_header();
			$this->gen_precontent();
			$this->gen_content();
			$this->gen_footer();
		}
	}
?>