<?php
	loadView();

	class home_view extends view {
		public function gen_content($messages='') {
			echo '
				<div id="messages">
			';
			for ($i=0; $i<count($messages); ++$i) {
				echo '
					<span class="b">Message From:</span> 	'.stripslashes($messages[$i]->sent_from).'<br />
					<span class="b">Message Body:</span> 	'.stripslashes($messages[$i]->message).'<br />
					<span class="b">Sent At:</span>		'.date('Y M D', ($messages[$i]->sms_message_timestamp/1000)).'<br /><br />
				';
			}
			echo '
				</div>
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
							document.getElementById(\'messages\').innerHTML=xmlHttp.responseText;
							setTimeout(\'Ajax()\',5000);
						}
					}
					xmlHttp.open("POST","'.WEB_ROOT.'?load_messages",true);
					xmlHttp.send(null);
					}

					window.onload=function(){
						setTimeout(\'Ajax()\',5000);
					}
				</script>
			';
		}
	}
?>