<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 1.5 Plugin: WP-Polls 2.02										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Configure Poll Options															|
|	- wp-admin/polls-options.php													|
|																							|
+----------------------------------------------------------------+
*/


### Require Admin
require_once('admin.php');

### Variables Variables Variables
$title = __('Poll Options');
$this_file = $parent_file = 'polls-options.php';
$id = intval($_GET['id']);
$standalone = 0;

### Require Admin Header
require("./admin-header.php");

### If User Less Than 8, Don't Let Him Pass
if ($user_level < 8) {
	die(__('Access Denied: Insufficient Access'));
}

### Magic Quotes GPC
if (get_magic_quotes_gpc()) {
   function traverse(&$arr) {
       if(!is_array($arr))
           return;
       foreach($arr as $key => $val)
           is_array($arr[$key]) ? traverse($arr[$key]) : ($arr[$key] = stripslashes($arr[$key]));
   }
   $gpc = array(&$_GET, &$_POST, &$_COOKIE);
   traverse($gpc);
}

### If Form Is Submitted
if($_POST['Submit']) {
	$poll_ans_sortby = addslashes(strip_tags(trim($_POST['poll_ans_sortby'])));
	$poll_ans_sortorder = addslashes(strip_tags(trim($_POST['poll_ans_sortorder'])));
	$poll_ans_result_sortby = addslashes(strip_tags(trim($_POST['poll_ans_result_sortby'])));
	$poll_ans_result_sortorder = addslashes(strip_tags(trim($_POST['poll_ans_result_sortorder'])));
	$poll_template_voteheader =addslashes(trim($_POST['poll_template_voteheader']));
	$poll_template_votebody = addslashes(trim($_POST['poll_template_votebody']));
	$poll_template_votefooter = addslashes(trim($_POST['poll_template_votefooter']));
	$poll_template_resultheader = addslashes(trim($_POST['poll_template_resultheader']));
	$poll_template_resultbody = addslashes(trim($_POST['poll_template_resultbody']));
	$poll_template_resultbody2 = addslashes(trim($_POST['poll_template_resultbody2']));
	$poll_template_resultfooter = addslashes(trim($_POST['poll_template_resultfooter']));
	$poll_template_disable =addslashes( trim($_POST['poll_template_disable']));
	$poll_template_error =addslashes( trim($_POST['poll_template_error']));
	$poll_archive_perpage = intval($_POST['poll_archive_perpage']);
	$poll_currentpoll = intval($_POST['poll_currentpoll']);
	$update_poll_queries = array();
	$update_poll_text = array();
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_ans_sortby' WHERE option_name = 'poll_ans_sortby'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_ans_sortorder' WHERE option_name = 'poll_ans_sortorder'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_ans_result_sortby' WHERE option_name = 'poll_ans_result_sortby'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_ans_result_sortorder' WHERE option_name = 'poll_ans_result_sortorder'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_voteheader' WHERE option_name = 'poll_template_voteheader'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_votebody' WHERE option_name = 'poll_template_votebody'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_votefooter' WHERE option_name = 'poll_template_votefooter'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_resultheader' WHERE option_name = 'poll_template_resultheader'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_resultbody' WHERE option_name = 'poll_template_resultbody'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_resultbody2' WHERE option_name = 'poll_template_resultbody2'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_resultfooter' WHERE option_name = 'poll_template_resultfooter'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_disable' WHERE option_name = 'poll_template_disable'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_template_error' WHERE option_name = 'poll_template_error'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_archive_perpage' WHERE option_name = 'poll_archive_perpage'";
	$update_poll_queries[] = "UPDATE $wpdb->options SET option_value = '$poll_currentpoll' WHERE option_name = 'poll_currentpoll'";
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
		$updating = $wpdb->query($update_poll_query);
		if($updating) {
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
			default_template = "<table width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n<tr>\n<td align=\"center\"><b>%POLL_QUESTION%</b></td>\n</tr>";
			break;
		case "votebody":
			default_template = "<tr>\n<td align=\"left\"><input type=\"radio\" name=\"poll-%POLL_ID%\" value=\"%POLL_ANSWER_ID%\" />&nbsp;%POLL_ANSWER%</td>\n</tr>";
			break;
		case "votefooter":
			default_template = "<tr>\n<td align=\"center\"><input type=\"submit\" name=\"vote\" value=\"   Vote   \" class=\"Buttons\" /><br /><a href=\"index.php?pollresult=1\">View Results</a></td>\n</tr>\n</table>";
			break;
		case "resultheader":
			default_template = "<table width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n<tr>\n<td colspan=\"2\" align=\"center\"><b>%POLL_QUESTION%</b></td>\n</tr>";
			break;
		case "resultbody":
			default_template = "<tr>\n<td align=\"left\" width=\"70%\">%POLL_ANSWER%<br /><img src=\"wp-images/pollbar.gif\" height=\"5\" width=\"%POLL_ANSWER_IMAGEWIDTH%\" alt=\"%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" /></td>\n<td align=\"right\" width=\"30%\"><b>%POLL_ANSWER_PERCENTAGE%%</b></td>\n</tr>";
			break;
		case "resultbody2":
			default_template = "<tr>\n<td align=\"left\" width=\"70%\"><i>%POLL_ANSWER%</i><br /><img src=\"wp-images/pollbar.gif\" height=\"5\" width=\"%POLL_ANSWER_IMAGEWIDTH%\" alt=\"You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\" /></td>\n<td align=\"right\" width=\"30%\"><i><b>%POLL_ANSWER_PERCENTAGE%%</b></i></td>\n</tr>";
			break;
		case "resultfooter":
			default_template = "<tr>\n<td colspan=\"2\" align=\"center\">Total Votes: <b>%POLL_TOTALVOTES%</b><td>\n</tr>\n</table>";
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
<ul id="adminmenu2"> 
	<li><a href="polls-manager.php"><?php _e('Manage Polls'); ?></a></li> 
	<li><a href="polls-manager.php?mode=add"><?php _e('Add Poll'); ?></a></li>
	<li class="last"><a href="polls-options.php"  class="current"><?php _e('Poll Options'); ?></a></li>
</ul>
<?php if(!empty($text)) { echo '<!-- Last Action --><div class="wrap"><h2>'.__('Last Action').'</h2>'.$text.'</div>'; } ?>
<div class="wrap"> 
	<h2><?php echo $title; ?></h2> 
	<form name="polls_options" method="post" action="polls-options.php"> 
		<fieldset class="options">
			<legend><?php _e('Sorting Of Poll Answers'); ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<th scope="row" width="40%"><?php _e('Sort Poll Answers By:'); ?></th>
					 <td>
						<select name="poll_ans_sortby" size="1">
							<option value="polla_aid"<?php selected('polla_aid', get_settings('poll_ans_sortby')); ?>><?php _e('Exact Order'); ?></option>
							<option value="polla_answers"<?php selected('polla_answers', get_settings('poll_ans_sortby')); ?>><?php _e('Alphabetical Order'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row" width="40%"><?php _e('Sort Order Of Poll Answers:'); ?></th>
					<td>
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
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<th scope="row" width="40%"><?php _e('Sort Poll Results By:'); ?></th>
					 <td>
						<select name="poll_ans_result_sortby" size="1">
							<option value="polla_votes"<?php selected('polla_votes', get_settings('poll_ans_result_sortby')); ?>><?php _e('Votes'); ?></option>
							<option value="polla_aid"<?php selected('polla_aid', get_settings('poll_ans_result_sortby')); ?>><?php _e('Exact Order'); ?></option>
							<option value="polla_answers"<?php selected('polla_answers', get_settings('poll_ans_result_sortby')); ?>><?php _e('Alphabetical Order'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row" width="40%"><?php _e('Sort Order Of Poll Results:'); ?></th>
					<td>
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
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<th scope="row" width="40%"><?php _e('Polls Per Page:'); ?></th>
					 <td><input type="text" name="poll_archive_perpage" value="<?php form_option('poll_archive_perpage'); ?>" size="2" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Current Active Poll'); ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<th scope="row" width="40%"><?php _e('Current Active Poll:'); ?></th>
					 <td>
						<select name="poll_currentpoll" size="1">
							<option value="-1"<?php selected('-1', get_settings('poll_currentpoll')); ?>><?php _e('Do NOT Display Poll (Disable)'); ?></option>
							<option value="0"<?php selected('0', get_settings('poll_currentpoll')); ?>><?php _e('Display Latest Poll'); ?></option>
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
			<table width="100%" cellspacing="2" cellpadding="5" align="center"> 
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
					<td>&nbsp;</td>
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
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Voting Form Header:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ID%<br />
						- %POLL_QUESTION%<br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('voteheader');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_voteheader" name="poll_template_voteheader"><?php echo stripslashes(get_settings('poll_template_voteheader')); ?></textarea></td>
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
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_votebody" name="poll_template_votebody"><?php echo stripslashes(get_settings('poll_template_votebody')); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Voting Form Footer:'); ?></b><br /><br /><br />
							<?php _e('Allowed Variables:'); ?><br />
							- N/A<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('votefooter');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_votefooter" name="poll_template_votefooter"><?php echo stripslashes(get_settings('poll_template_votefooter')); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Result Templates'); ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Result Header:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_ID%<br />
						- %POLL_QUESTION%<br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultheader');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_resultheader" name="poll_template_resultheader"><?php echo stripslashes(get_settings('poll_template_resultheader')); ?></textarea></td>
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
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_resultbody" name="poll_template_resultbody"><?php echo stripslashes(get_settings('poll_template_resultbody')); ?></textarea></td> 
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
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_resultbody2" name="poll_template_resultbody2"><?php echo stripslashes(get_settings('poll_template_resultbody2')); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<b><?php _e('Result Footer:'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- %POLL_TOTALVOTES%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('resultfooter');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_resultfooter" name="poll_template_resultfooter"><?php echo stripslashes(get_settings('poll_template_resultfooter')); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Poll Misc Templates'); ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				 <tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Poll Disabled'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- N/A<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('disable');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_disable" name="poll_template_disable"><?php echo stripslashes(get_settings('poll_template_disable')); ?></textarea></td>
				</tr>
				<tr valign="top">
					<td width="30%" align="left">
						<b><?php _e('Poll Error'); ?></b><br /><br /><br />
						<?php _e('Allowed Variables:'); ?><br />
						- N/A<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template'); ?>" onclick="javascript: poll_default_templates('error');" class="button" />
					</td>
					<td width="70%" align="right"><textarea cols="100" rows="10" id="poll_template_error" name="poll_template_error"><?php echo stripslashes(get_settings('poll_template_error')); ?></textarea></td>
				</tr>
			</table>
		</fieldset>
		<p class="submit"> 
			<input type="submit" name="Submit" value="<?php _e('Update Options'); ?> &raquo;" /> 
		</p>
	</form> 
</div> 
<?php include('./admin-footer.php') ?>