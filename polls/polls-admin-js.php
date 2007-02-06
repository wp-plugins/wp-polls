<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Polls 2.15										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Polls Admin Javascript File													|
|	- wp-content/plugins/polls/polls-admin-js.php							|
|																							|
+----------------------------------------------------------------+
*/


### Include wp-config.php
@require('../../../wp-config.php');
cache_javascript_headers();

### Determine polls-admin-ajax.php Path
$polls_admin_ajax_url = dirname($_SERVER['PHP_SELF']);
if(substr($polls_admin_ajax_url, -1) == '/') {
	$polls_admin_ajax_url  = substr($polls_admin_ajax_url, 0, -1);
}
?>
// Variables
var polls_admin_ajax_url = "<?php echo $polls_admin_ajax_url; ?>/polls-admin-ajax.php";
var polls_admin = new sack(polls_admin_ajax_url);
var global_poll_id = 0;


// Function: Delete Poll Message
function delete_poll_message() {
	document.getElementById('message').style.display = "block";
	Fat.fade_element("message", null, 3000, "#FFFF00");
	Fat.fade_element("poll-" + global_poll_id, null, 1000, "#FF3333");
	setTimeout("remove_poll()", 1000);
}

// Function: Remove Poll From Manage Poll
function remove_poll() {
	document.getElementById("manage_polls").removeChild(document.getElementById("poll-" + global_poll_id));
}

// Function: Delete Poll
function delete_poll(poll_id, poll_confirm) {
	delete_poll_confirm = confirm(poll_confirm);
	if(delete_poll_confirm) {
		global_poll_id = poll_id;
		polls_admin.reset();
		polls_admin.setVar("do", "<?php _e('Delete Poll', 'wp-polls'); ?>");
		polls_admin.setVar("pollq_id", poll_id);
		polls_admin.method = 'POST';
		polls_admin.element = 'message';
		polls_admin.onCompletion = delete_poll_message;
		polls_admin.runAJAX();
	}
}


// Function: Delete Poll Logs Message
function delete_poll_logs_message() {
	document.getElementById('message').style.display = "block";
	Fat.fade_element("message", null, 3000, "#FFFF00");
	alert("<?php _e('Polls logs processed. Please see the top of this page for the outcome.', 'wp-polls'); ?>");
}

// Function: Delete Poll Logs
function delete_poll_logs(poll_confirm) {
	delete_poll_logs_confirm = confirm(poll_confirm);
	if(delete_poll_logs_confirm) {
		if(document.getElementById("delete_logs_yes").checked == true) {
			polls_admin.reset();
			polls_admin.setVar("do", "<?php _e('Delete All Logs', 'wp-polls'); ?>");
			polls_admin.setVar("delete_logs_yes", "yes");
			polls_admin.method = 'POST';
			polls_admin.element = 'message';
			polls_admin.onCompletion = delete_poll_logs_message;
			polls_admin.runAJAX();
		} else {
			alert("<?php _e('Please check the \'Yes\' checkbox if you want to delete all logs.', 'wp-polls'); ?>");
		}
	}
}