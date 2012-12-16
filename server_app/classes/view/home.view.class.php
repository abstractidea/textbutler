<?php
	loadView();

	class home_view extends view {
		public function gen_content($auth_result='') {
			echo '
				<div>'.$auth_result->message.'</div>
			';
		}
	}
?>