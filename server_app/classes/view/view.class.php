<?php
	abstract class view {
		private function gen_header() {
			echo '
				<!DOCTYPE html>
				<html>
					<head>
						<title>Project TextButler</title>

						'.loadCSS('main').'
			';
		}
		private function gen_precontent() {
			echo '
					</head>
					<body>
						<div id="wrapper">
							<div id="bar">
								<ul id="nav">
									<li><a href="'.WEB_ROOT.'">Project TextButler</a></li>
								</ul>
								<ul id="settings">
									'.loadSettings().'
								</ul>
							</div>
							<div id="content">
			';
		}
		
		abstract function gen_content();

		private function gen_footer() {
			echo '
							</div>
						</div>
						<div id="shield">
						</div>
						'.loadJS('man').'
					</body>
				</html>
			';
		}
		public function gen_view($result='') {
			$this->gen_header();
			$this->gen_precontent();
			$this->gen_content($result);
			$this->gen_footer();
		}
	}
?>