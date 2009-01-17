/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.8 Plugin: WP-Polls 2.50										|
|	Copyright (c) 2009 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Polls Admin Javascript File													|
|	- wp-content/plugins/wp-polls/polls-admin-js.js	 						|
|																							|
+----------------------------------------------------------------+
*/


var global_poll_id = 0;
var global_poll_aid = 0;
var global_poll_aid_votes  = 0;

// Function: Delete Poll
function delete_poll(poll_id, poll_confirm) {
	delete_poll_confirm = confirm(poll_confirm);
	if(delete_poll_confirm) {
		global_poll_id = poll_id;
		jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_delete_poll + '&pollq_id=' + poll_id, cache: false, success: function (data) {
			jQuery('#message').html(data);
			jQuery('#message').show();
			jQuery('#poll-' + global_poll_id).remove();
		}});
	}
}

// Function: Delete Poll Logs
function delete_poll_logs(poll_confirm) {
	delete_poll_logs_confirm = confirm(poll_confirm);
	if(delete_poll_logs_confirm) {
		if(jQuery('#delete_logs_yes').is(':checked')) {
			jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_delete_all_logs + '&delete_logs_yes=yes', cache: false, success: function (data) {
				jQuery('#message').html(data);
				jQuery('#message').show();
				jQuery('#poll_logs').html(pollsAdminL10n.text_no_poll_logs);
			}});
		} else {
			alert(pollsAdminL10n.text_checkbox_delete_all_logs);
		}
	}
}

// Function: Delete Individual Poll Logs
function delete_this_poll_logs(poll_id, poll_confirm) {
	delete_poll_logs_confirm = confirm(poll_confirm);
	if(delete_poll_logs_confirm) {
		if(jQuery('#delete_logs_yes').is(':checked')) {
			global_poll_id = poll_id;
			jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_delete_poll_logs + '&pollq_id=' + poll_id + '&delete_logs_yes=yes', cache: false, success: function (data) {
				jQuery('#message').html(data);
				jQuery('#message').show();
				jQuery('#poll_logs').html(pollsAdminL10n.text_no_poll_logs);
				jQuery('#poll_logs_display').hide();
				jQuery('#poll_logs_display_none').show();
			}});
		} else {
			alert(pollsAdminL10n.text_checkbox_delete_poll_logs);
		}
	}
}

// Function: Delete Poll Answer
function delete_poll_ans(poll_id, poll_aid, poll_aid_vote, poll_confirm) {
	delete_poll_ans_confirm = confirm(poll_confirm);
	if(delete_poll_ans_confirm) {
		global_poll_id = poll_id;
		global_poll_aid = poll_aid;
		global_poll_aid_votes = poll_aid_vote;
		jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_delete_poll_ans + '&pollq_id=' + poll_id + '&polla_aid=' + poll_aid, cache: false, success: function (data) {
			jQuery('#message').html(data);
			jQuery('#message').show();
			jQuery('#poll_total_votes').html((parseInt(jQuery('#poll_total_votes').html()) - parseInt(global_poll_aid_votes)));
			poll_total_votes = parseInt(jQuery('#pollq_totalvotes').val());
			poll_answer_vote = parseInt(jQuery('#polla_votes-' + global_poll_aid).val());
			poll_total_votes_new = (poll_total_votes - poll_answer_vote);
			if(poll_total_votes_new < 0) {
				poll_total_votes_new = 0;
			}
			jQuery('#pollq_totalvotes').val(parseInt(poll_total_votes_new));		
			jQuery('#poll-answer-' + global_poll_aid).remove();
		}});
	}
}

// Function: Open Poll
function opening_poll(poll_id, poll_confirm) {
	open_poll_confirm = confirm(poll_confirm);
	if(open_poll_confirm) {
		global_poll_id = poll_id;
		jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_open_poll + '&pollq_id=' + poll_id, cache: false, success: function (data) {
			jQuery('#message').html(data);
			jQuery('#message').show();
			jQuery('#open_poll').hide();
			jQuery('#close_poll').show();
		}});
	}
}

// Function: Close Poll
function closing_poll(poll_id, poll_confirm) {
	close_poll_confirm = confirm(poll_confirm);
	if(close_poll_confirm) {
		global_poll_id = poll_id;
		jQuery.ajax({type: 'POST', url: pollsAdminL10n.admin_ajax_url, data: 'do=' + pollsAdminL10n.text_close_poll + '&pollq_id=' + poll_id, cache: false, success: function (data) {
			jQuery('#message').html(data);
			jQuery('#message').show();
			jQuery('#open_poll').show();
			jQuery('#close_poll').hide();
		}});
	}
}