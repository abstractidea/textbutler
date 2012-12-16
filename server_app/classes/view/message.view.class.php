<?php
	loadView();

	class message_view extends view {
		public function gen_content() {
			echo '
				<form action="?gen_message_from_http" method="post">
				<table>
					<tr>
						<td>User ID: </td>
						<td><input type="text" name="user_id" /></td>
					</tr>
					<tr>
						<td>Sender: </td>
						<td><input type="text" name="sent_from" /></td>
					</tr>
					<tr>
						<td>Message Body: </td>
						<td><textarea name="message"></textarea></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" value="Send Message" /></td>
					</tr>
				</table>
				</form>
			';
		}
	}
?>