<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Polls 2.06										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Configure Poll Options															|
|	- wp-content/plugins/polls/polls-options.php								|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('polls/polls-options.php');
$base_page = 'admin.php?page='.$base_name;
$id = intval($_GET['id']);


### If Form Is Submitted
if($_POST['Submit']) {
	$poll_ans_sortby = strip_tags(trim($_POST['poll_ans_sortby']));
	$poll_ans_sortorder = strip_tags(trim($_POST['poll_ans_sortorder']));
	$poll_ans_result_sortby = strip_tags(trim($_POST['poll_ans_result_sortby']));
	$poll_ans_result_sortorder = strip_tags(trim($_POST['poll_ans_result_sortorder']));
	$poll_template_voteheader =trim($_POST['poll_template_voteheader']);
	$poll_template_votebody = trim($_POST['poll_template_votebody']);
	$poll_template_votefooter = trim($_POST['poll_template_votefooter']);
	$poll_template_resultheader = trim($_POST['poll_template_resultheader']);
	$poll_template_resultbody = trim($_POST['poll_template_resultbody']);
	$poll_template_resultbody2 = trim($_POST['poll_template_resultbody2']);
	$poll_template_resultfooter = trim($_POST['poll_template_resultfooter']);
	$poll_template_disable = trim($_POST['poll_template_disable']);
	$poll_template_error = trim($_POST['poll_template_error']);
	$poll_archive_perpage = intval($_POST['poll_archive_perpage']);
	$poll_currentpoll = intval($_POST['poll_currentpoll']);
	$update_poll_queries = array();
	$update_poll_text = array();
	$update_poll_queries[] = update_option('poll_ans_sortby', $poll_ans_sortby);
	$update_poll_queries[] = update_option('poll_ans_sortorder', $poll_ans_sortorder);
	$update_poll_queries[] = update_option('poll_ans_result_sortby', $poll_ans_result_sortby);
	$update_poll_queries[] = update_option('poll_ans_result_sortorder', $poll_ans_result_sortorder);
	$update_poll_queries[] = update_option('poll_template_voteheader', $poll_template_voteheader);
	$update_poll_queries[] = update_option('poll_template_votebody', $poll_template_votebody);
	$update_poll_queries[] = update_option('poll_template_votefooter', $poll_template_votefooter);
	$update_poll_queries[] = update_option('poll_template_resultheader', $poll_template_resultheader);
	$update_poll_queries[] = update_option('poll_template_resultbody', $poll_template_resultbody);
	$update_poll_queries[] = update_option('poll_template_resultbody2', $poll_template_resultbody2);
	$update_poll_queries[] = update_option('poll_template_resultfooter', $poll_template_resultfooter);
	$update_poll_queries[] = update_option('poll_template_disable', $poll_template_disable);
	$update_poll_queries[] = update_option('poll_template_error', $poll_template_error);
	$update_poll_queries[] = update_option('poll_archive_perpage', $poll_archive_perpage);
	$update_poll_queries[] = update_option('poll_currentpoll', $poll_currentpoll);
	$update_poll_text[] = __('Sort Poll Answers By Option');
	$update_poll_text[] = __('Sort Order Of Poll Answers Option');
	$update_poll_text[] = __('Sort Poll Results By Option');
	$update_poll_text[] = __('Sort Order Of Poll Results Option');
	$update_poll_text[] = __('Voting Form Header Template');
	$update_poll_text[] = __('Voting Form Body Template');
	$update_poll_text[] = __('Voting Form Footer Template');
	$update_poll_text[] = __('Result Header Template');
	$update_poll_text[] = __('Result Body Template');
	$update_poll_text[] = __('Result Body2 Template');
	$update_poll_text[] = __('Result Footer Template');
	$update_poll_text[] = __('Poll Disabled Template');
	$update_poll_text[] = __('Poll Error Template');
	$update_poll_text[] = __('Archive Polls Per Page Option');
	$update_poll_text[] = __('Current Active Poll Option');
	$i=0;
	$text = '';
	foreach($update_poll_queries as $update_poll_query) {
		if($update_poll_query) {
			$text .= '<font color="green">'.$update_poll_text[$i].' '.__('Updated').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Poll Option Updated').'</font>';
	}
}

?>
<script language="JavaScript" type="text/javascript">
function poll_default_templates(template) {
	var default_template;
	switch(template) {
		case "voteheader":
			default_template = "<p align=\"center\"><b>%POLL_QUESTION%</b></p>\n<ul>";
			break;
		case "votebody":
			default_template = "<li><label for=\"poll-answer-%POLL_ANSWER_ID%\"><input type=\"radio\" id=\"poll-answer-%POLL_ANSWER_ID%\" name=\"poll-%POLL_ID%\" value=\"%POLL_ANSWER_ID%\" /> %POLL_ANSWER%</label></li>";
			break;
		case "votefooter":
			default_template = "</ul>\n<p align=\"center\"><input type=\"submit\" name=\"vote\" value=\"   Vote   \" class=\"Buttons\" /><br /><a href=\"%POLL_RESULT_URL%\">View Results</a></p>";
			break;
		case "resultheader":
			default_template = "<p align=\"center\"><b>%POLL_QUESTION%</b></p>\n<ul>";
			break;
		case "resultbody":
			default_template = "<li>%POLL_ANSWER% <small>(%POLL_ANSWER_PERCENTAGE%%)</small><br /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollstart.gif\" height=\"10\" width=\"2\" /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollbar.gif\" height=\"10\" width=\"%POLL_ANSWER_IMAGEWIDTH%\" alt=\"%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" title=\"%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollend.gif\" height=\"10\" width=\"2\" /></li>";
			break;
		case "resultbody2":
			default_template = "<li><b><i>%POLL_ANSWER% <small>(%POLL_ANSWER_PERCENTAGE%%)</small></i></b><br /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollstart.gif\" height=\"10\" width=\"2\" /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollbar.gif\" height=\"10\" width=\"%POLL_ANSWER_IMAGEWIDTH%\" alt=\"You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" title=\"You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" /><img src=\"<?php echo get_settings('siteurl'); ?>/wp-content/plugins/polls/images/pollend.gif\" height=\"10\" width=\"2\" /></li>";
			break;
		case "resultfooter":
			default_template = "</ul>\n<p align=\"center\">Total Votes: <b>%POLL_TOTALVOTES%</b></p>";
			break;
		case "disable":
			default_template = 'Sorry, there are no polls available at the moment.';
			break;
		case "error":
			default_template = 'An error has occurred when processing your poll.';
			break;
	}
	document.getElementById("poll_template_" + template).value = default_template;
}

</script>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap"> 
	<h2><?php _e('Poll Options'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
		<fieldset class="options">
			<legend><?php _e('Sorting Of Poll Answers'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Sort Poll Answers By:'); ?></th>
					<td align="left">
						<select name="poll_ans_sortby" size="1">
							<option value="polla_aid"<?php selected('polla_aid', get_settings('poll_ans_sortby')); ?>><?php _e('Exact Order'); ?></option>
							<option value="polla_answers"<?php selected('polla_answers', get_settings('poll_ans_sortby')); ?>><?php _e('Alphabetical Order'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top"> 
					<th align="left" width="30%"><?php _e('Sort Order Of Poll Answers:'); ?></th>
					<td align="left">
						<select name="poll_ans_sortorder" size="1">
							<option value="asc"<?php selected('asc', get_settings('poll_ans_sortorder')); ?>><?php _e('Ascending'); ?></option>
							<option value="desc"<?php selected('desc', get_settings('poll_ans_sortorder')); ?>><?php _e('Descending'); ?></option>
						</select>
					</td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Sorting Of Poll Results'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Sort Poll Results By:'); ?></th>
					<td align="left">
						<select name="poll_ans_result_sortby" size="1">
							<option value="polla_votes"<?php selected('polla_votes', get_settings('poll_ans_result_sortby')); ?>><?php _e('Votes'); ?></option>
							<option value="polla_aid"<?php selected('polla_aid', get_settings('poll_ans_result_sortby')); ?>><?php _e('Exact Order'); ?></option>
							<option value="polla_answers"<?php selected('polla_answers', get_settings('poll_ans_result_sortby')); ?>><?php _e('Alphabetical Order'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top"> 
					<th align="left" width="30%"><?php _e('Sort Order Of Poll Results:'); ?></th>
					<td align="left">
						<select name="poll_ans_result_sortorder" size="1">
							<option value="asc"<?php selected('asc', get_settings('poll_ans_result_sortorder')); ?>><?php _e('Ascending'); ?></option>
							<option value="desc"<?php selected('desc', get_settings('poll_ans_result_sortorder')); ?>><?php _e('Descending'); ?></option>
						</select>
					</td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Archive'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Polls Per Page:'); ?></th>
					<td align="left"><input type="text" name="poll_archive_perpage" value="<?php echo intval(get_settings('poll_archive_perpage')); ?>" size="2" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Current Active Poll'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Current Active Poll:'); ?></th>
					<td align="left">
						<select name="poll_currentpoll" size="1">
							<option value="-1"<?php selected(-1, get_settings('poll_currentpoll')); ?>><?php _e('Do NOT Display Poll (Disable)'); ?></option>
							<option value="0"<?php selected(0, get_settings('poll_currentpoll')); ?>><?php _e('Display Latest Poll'); ?></option>
							<option value="0"></option>
							<?php
								$polls = $wpdb->get_results("SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC");
								if($polls) {
									foreach($polls as $poll) {
										$poll_question = stripslashes($poll->pollq_question);
										$poll_id = intval($poll->pollq_id);
										if($poll_id == intval(get_settings('poll_currentpoll'))) {
											echo "<option value=\"$poll_id\" selected=\"selected\">$poll_question</option>\n";
										} else {
											echo "<option value=\"$poll_id\">$poll_question</option>\n";
										}
									}
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Template Variables'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				<tr>
					<td><b>%POLL_ID%</b> - <?php _e('Display the poll\'s ID'); ?></td>
					<td><b>%POLL_ANSWER_ID%</b> - <?php _e('Display the poll\'s answer ID'); ?></td>
				</tr>
				<tr>
					<td><b>%POLL_QUESTION%</b> - <?php _e('Display the poll\'s question'); ?></td>
					<td><b>%POLL_ANSWER%</b> - <?php _e('Display the poll\'s answer'); ?></td>
				</tr>
				<tr>
					<td><b>%POLL_TOTALVOTES%</b> - <?php _e('Display the poll\'s total votes'); ?></td>
					<td><b>%POLL_ANSWER_VOTES%</b> - <?php _e('Display the poll\'s answer votes'); ?></td>
				</tr>
				<tr>
					<td><b>%POLL_RESULT_URL%</b> - <?php _e('Displays URL to poll\'s result'); ?></td>
					<td><b>%POLL_ANSWER_PERCENTAGE%</b> - <?php _e('Display the poll\'s answer percentage'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><b>"%POLL_ANSWER_IMAGEWIDTH%</b> - <?php _e('Display the poll\'s answer image width'); ?></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Voting Form Templates'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Voting Form Header:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ID%<br />
						- %POLL_QUESTION%<br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('voteheader');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_voteheader" name="poll_template_voteheader"><?php echo stripslashes(get_settings('poll_template_voteheader')); ?></textarea></td>
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Voting Form Body:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ID%<br />
						- %POLL_ANSWER_ID%<br />
						- %POLL_ANSWER%<br />
						- %POLL_ANSWER_VOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('votebody');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_votebody" name="poll_template_votebody"><?php echo stripslashes(get_settings('poll_template_votebody')); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Voting Form Footer:'); ?></b><br /><br /><br />
							<?php _e('Allowed Variables:'); ?><br />
							- %POLL_RESULT_URL%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('votefooter');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_votefooter" name="poll_template_votefooter"><?php echo stripslashes(get_settings('poll_template_votefooter')); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Result Templates'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3"> 
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Result Header:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ID%<br />
						- %POLL_QUESTION%<br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultheader');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_resultheader" name="poll_template_resultheader"><?php echo stripslashes(get_settings('poll_template_resultheader')); ?></textarea></td>
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Result Body:'); ?></b><br /><?php _e('Normal'); ?><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ANSWER_ID%<br />
						- %POLL_ANSWER%<br />
						- %POLL_ANSWER_VOTES%<br />
						- %POLL_ANSWER_PERCENTAGE%<br />
						- %POLL_ANSWER_IMAGEWIDTH%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultbody');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_resultbody" name="poll_template_resultbody"><?php echo stripslashes(get_settings('poll_template_resultbody')); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Result Body:'); ?></b><br /><?php _e('Displaying Of User\'s Voted Answer'); ?><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ANSWER_ID%<br />
						- %POLL_ANSWER%<br />
						- %POLL_ANSWER_VOTES%<br />
						-  %POLL_ANSWER_PERCENTAGE%<br />
						- %POLL_ANSWER_IMAGEWIDTH%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultbody2');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_resultbody2" name="poll_template_resultbody2"><?php echo stripslashes(get_settings('poll_template_resultbody2')); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Result Footer:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultfooter');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_resultfooter" name="poll_template_resultfooter"><?php echo stripslashes(get_settings('poll_template_resultfooter')); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Misc Templates'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3"> 
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Poll Disabled'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- N/A<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('disable');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_disable" name="poll_template_disable"><?php echo stripslashes(get_settings('poll_template_disable')); ?></textarea></td>
				</tr>
				<tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Poll Error'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- N/A<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('error');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="10" id="poll_template_error" name="poll_template_error"><?php echo stripslashes(get_settings('poll_template_error')); ?></textarea></td>
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" Value="Cancel" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 