<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Polls 2.14										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Manage Your Polls																|
|	- wp-content/plugins/polls/polls-manager.php							|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('polls/polls-manager.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$poll_id = intval($_GET['id']);
$poll_aid = intval($_GET['aid']);


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Add Poll
		case __('Add Poll', 'wp-polls'):
			// Add Poll Question
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			$pollq_timestamp = current_time('timestamp');
			$add_poll_question = $wpdb->query("INSERT INTO $wpdb->pollsq VALUES (0, '$pollq_question', '$pollq_timestamp', 0, 1)");
			if(!$add_poll_question) {
				$text .= '<font color="red">'.sprintf(__('Error In Adding Poll \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</font>';
			}
			// Add Poll Answers
			$polla_answers = $_POST['polla_answers'];
			$polla_qid = intval($wpdb->insert_id);
			foreach($polla_answers as $polla_answer) {
				$polla_answer = addslashes(trim($polla_answer));
				$add_poll_answers = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0, $polla_qid, '$polla_answer', 0)");
				if(!$add_poll_answers) {
					$text .= '<font color="red">'.sprintf(__('Error In Adding Poll\'s Answer \'%s\'', 'wp-polls'), stripslashes($polla_answer)).'</font>';
				}
			}
			// Update Lastest Poll ID To Poll Options
			$update_latestpoll = update_option('poll_latestpoll', $polla_qid);
			if(!$update_latestpoll) {
				$text .= "<font color=\"red\">".sprintf(__('There Is An Error Updating The Lastest Poll ID (%s) To The Poll Option', 'wp-polls'), $polla_qid)."</font>";
			}
			if(empty($text)) {
				$text = '<font color="green">'.__('Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\' '.__('Added Successfully', 'wp-polls').'</font>';
			}
			break;
		// Edit Poll
		case __('Edit Poll', 'wp-polls'):
			// Update Poll's Question
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_totalvotes = intval($_POST['pollq_totalvotes']);
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			$edit_polltimestamp = intval($_POST['edit_polltimestamp']);
			$timestamp_sql = '';
			if($edit_polltimestamp == 1) {
				$pollq_timestamp_day = intval($_POST['pollq_timestamp_day']);
				$pollq_timestamp_month = intval($_POST['pollq_timestamp_month']);
				$pollq_timestamp_year = intval($_POST['pollq_timestamp_year']);
				$pollq_timestamp_hour = intval($_POST['pollq_timestamp_hour']);
				$pollq_timestamp_minute = intval($_POST['pollq_timestamp_minute']);
				$pollq_timestamp_second = intval($_POST['pollq_timestamp_second']);
				$timestamp_sql = ", pollq_timestamp = '".gmmktime($pollq_timestamp_hour, $pollq_timestamp_minute, $pollq_timestamp_second, $pollq_timestamp_month, $pollq_timestamp_day, $pollq_timestamp_year)."'";
			}

			$edit_poll_question = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_question = '$pollq_question', pollq_totalvotes = $pollq_totalvotes $timestamp_sql WHERE pollq_id = $pollq_id");
			if(!$edit_poll_question) {
				$text = '<font color="blue">'.__('No Changes Had Been Made To Poll\'s Title', 'wp-polls').' \''.stripslashes($pollq_question).'\'</font>';
			}
			// Update Polls' Answers
			$polla_aids = array();
			$get_polla_aids = $wpdb->get_results("SELECT polla_aid FROM $wpdb->pollsa WHERE polla_qid = $pollq_id ORDER BY polla_aid ASC");
			if($get_polla_aids) {
				foreach($get_polla_aids as $get_polla_aid) {
						$polla_aids[] = intval($get_polla_aid->polla_aid);
				}
				foreach($polla_aids as $polla_aid) {
					$polla_answers = addslashes(trim($_POST['polla_aid-'.$polla_aid]));
					$polla_votes = intval($_POST['polla_votes-'.$polla_aid]);
					$edit_poll_answer = $wpdb->query("UPDATE $wpdb->pollsa SET polla_answers = '$polla_answers', polla_votes = $polla_votes WHERE polla_qid = $pollq_id AND polla_aid = $polla_aid");
					if(!$edit_poll_answer) {
						$text .= '<br /><font color="blue">'.__('No Changes Had Been Made To Poll\'s Answer', 'wp-polls').' \''.stripslashes($polla_answers).'\'</font>';
					}
				}
			} else {
				$text .= '<br /><font color="red">'.__('Invalid Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\'</font>';
			}
			if(empty($text)) {
				$text = '<font color="green">'.__('Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\' '.__('Edited Successfully', 'wp-polls').'</font>';
			}
			break;
		// Open Poll
		case __('Open Poll', 'wp-polls'):
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			$close_poll = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_active = 1 WHERE pollq_id = $pollq_id;");
			if($close_poll) {
				$text = '<font color="green">'.__('Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\' '.__('Is Now Opened', 'wp-polls').'</font>';
			} else {
				$text = '<font color="red">'.__('Error Opening Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\'</font>';
			}
			break;
		// Close Poll
		case __('Close Poll', 'wp-polls'):
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			$close_poll = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_active = 0 WHERE pollq_id = $pollq_id;");
			if($close_poll) {
				$text = '<font color="green">'.__('Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\' '.__('Is Now Closed', 'wp-polls').'</font>';
			} else {
				$text = '<font color="red">'.__('Error Closing Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\'</font>';
			}
			break;
		// Delete Poll
		case __('Delete Poll', 'wp-polls'):
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = trim($_POST['pollq_question']);
			$delete_poll_question = $wpdb->query("DELETE FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$delete_poll_answers =  $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_qid = $pollq_id");
			$delete_poll_ip = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id");
			$poll_option_lastestpoll = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'poll_latestpoll'");
			if(!$delete_poll_question) {
				$text = '<font color="red">'.sprintf(__('Error In Deleting Poll \'%s\' Question', 'wp-polls'), stripslashes($pollq_question)).'</font>';
			} 
			if(!$delete_poll_answers) {
				$text .= '<br /><font color="blue">'.sprintf(__('No Poll Answers For \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</font>';
			}
			if(!$delete_poll_ip) {
				$text .= '<br /><font color="blue">'.sprintf(__('No Voted IPs For \'%s\'', 'wp-polls'), stripslashes($pollq_question)).'</font>';
			}
			if(empty($text)) {
				if($poll_option_lastestpoll == $pollq_id) {
					$poll_lastestpoll = $wpdb->get_var("SELECT pollq_id FROM $wpdb->pollsq ORDER BY pollq_id DESC LIMIT 1");
					if($poll_lastestpoll) {
						$poll_lastestpoll = intval($poll_lastestpoll);
						update_option('poll_latestpoll', $poll_lastestpoll);
					}
				}
				$text = '<font color="green">'.__('Poll', 'wp-polls').' \''.stripslashes($pollq_question).'\' '.__('Deleted Successfully', 'wp-polls').'</font>';
			}
			break;
		// Add Poll's Answer
		case __('Add Answer', 'wp-polls'):
			$polla_qid  = intval($_POST['polla_qid']);
			$polla_answers = addslashes(trim($_POST['polla_answers']));
			$add_poll_question = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0, $polla_qid, '$polla_answers', 0)");
			if(!$add_poll_question) {
				$text = '<font color="red">'.sprintf(__('Error In Adding Poll Answer \'%s\'', 'wp-polls'), stripslashes($polla_answers)).'</font>';
			} else {
				$text = '<font color="green">'.__('Poll Answer', 'wp-polls').' \''.stripslashes($polla_answers).'\' '.__('Added Successfully', 'wp-polls').'</font>';
			}
			break;
		// Delete Polls Logs
		case __('Delete All Logs', 'wp-polls'):
			if(trim($_POST['delete_logs_yes']) == 'yes') {
				$delete_logs = $wpdb->query("DELETE FROM $wpdb->pollsip");
				if($delete_logs) {
					$text = '<font color="green">'.__('All Polls Logs Have Been Deleted.', 'wp-polls').'</font>';
				} else {
					$text = '<font color="red">'.__('An Error Has Occured While Deleting All Polls Logs.', 'wp-polls').'</font>';
				}
			}
			break;
		// Delete Poll Logs For Individual Poll
		case __('Delete Logs For This Poll Only', 'wp-polls'):
			$pollq_id  = intval($_POST['pollq_id']);
			if(trim($_POST['delete_logs_yes']) == 'yes') {
				$delete_logs = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id");
				if($delete_logs) {
					$text = '<font color="green">'.__('All Logs For This Poll Have Been Deleted.', 'wp-polls').'</font>';
				} else {
					$text = '<font color="red">'.__('An Error Has Occured While Deleting All Logs For This Poll.', 'wp-polls').'</font>';
				}
			}
			break;
		//  Uninstall WP-Polls (By: Philippe Corbes)
		case __('UNINSTALL Polls', 'wp-polls') :
			if(trim($_POST['uninstall_poll_yes']) == 'yes') {
				echo '<div id="message" class="updated fade"><p>';
				$polls_tables = array($wpdb->pollsq, $wpdb->pollsa, $wpdb->pollsip);
				foreach($polls_tables as $table) {
					$wpdb->query("DROP TABLE {$table}");
					echo '<font color="green">';
					printf(__('Table "%s" Has Been Dropped.', 'wp-polls'), "<strong><em>{$table}</em></strong>");
					echo '</font><br />';
				}
				$polls_settings = array('poll_template_voteheader', 'poll_template_votebody', 'poll_template_votefooter', 'poll_template_resultheader',
				'poll_template_resultbody', 'poll_template_resultbody2', 'poll_template_resultfooter', 'poll_template_resultfooter2', 
				'poll_template_disable', 'poll_template_error', 'poll_currentpoll', 'poll_latestpoll', 
				'poll_archive_perpage', 'poll_ans_sortby', 'poll_ans_sortorder', 'poll_ans_result_sortby', 
				'poll_ans_result_sortorder', 'poll_logging_method', 'poll_allowtovote', 'poll_archive_show',
				'poll_archive_url', 'poll_bar');
				foreach($polls_settings as $setting) {
					$delete_setting = delete_option($setting);
					if($delete_setting) {
						echo '<font color="green">';
						printf(__('Setting Key \'%s\' Has been Errased.', 'wp-polls'), "<strong><em>{$setting}</em></strong>");
					} else {
						echo '<font color="red">';
						printf(__('Error Deleting Setting Key \'%s\'.', 'wp-polls'), "<strong><em>{$setting}</em></strong>");
					}
					echo '</font><br />';
				}
				echo '</p></div>'; 
				$mode = 'end-UNINSTALL';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
	// Add A Poll
	case 'add':
?>
		<div class="wrap">
				<h2><?php _e('Add Poll', 'wp-polls'); ?></h2>
				<?php
				if(isset($_POST['addpollquestion'])) {
					$poll_noquestion = intval($_POST['poll_noquestion']);
					$pollq_question = stripslashes(trim($_POST['pollq_question']));	
				?>
				<form action="<?php echo $base_page; ?>" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left"><?php _e('Question', 'wp-polls') ?></th>
							<td><input type="text" size="50" maxlength="200" name="pollq_question" value="<?php echo htmlspecialchars($pollq_question); ?>" /></td>
						</tr>
						<?php
							for($i=1; $i<=$poll_noquestion; $i++) {
								echo "<tr>\n";
								echo "<th align=\"left\" scope=\"row\">Answers $i:</th>\n";
								echo "<td><input type=\"text\" size=\"30\" maxlength=\"200\" name=\"polla_answers[]\" /></td>\n";
								echo "</tr>\n";
							}
						?>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add Poll', 'wp-polls'); ?>"  class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-polls'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
						</tr>
					</table>
				</form>
				<?php } else {?>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;mode=add" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left"><?php _e('Question', 'wp-polls') ?></th>
							<td><input type="text" size="50" maxlength="200" name="pollq_question" /></td>
						</tr>
							<th align="left"><?php _e('No. Of Answers:', 'wp-polls') ?></th>
							<td>
									<select size="1" name="poll_noquestion">
											<?php
											for($i=2; $i <= 20; $i++) {
												echo "<option value=\"$i\">$i</option>";
											}
											?>
									</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="addpollquestion" value="<?php _e('Add Question', 'wp-polls'); ?>" class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-polls'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
						</tr>
					</table>
				</form>
				<?php } ?>
		</div>
<?php
		break;
	// Edit A Poll
	case 'edit':
		$poll_question = $wpdb->get_row("SELECT pollq_question, pollq_timestamp, pollq_totalvotes, pollq_active FROM $wpdb->pollsq WHERE pollq_id = $poll_id");
		$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_id ORDER BY polla_aid ASC");
		$poll_question_text = stripslashes($poll_question->pollq_question);
		$poll_totalvotes = intval($poll_question->pollq_totalvote);
		$poll_timestamp = $poll_question->pollq_timestamp;
		$poll_active = intval($poll_question->pollq_active);

		// Edit Timestamp Options
		function poll_timestamp($poll_timestamp) {
			global $month;
			$day = gmdate('j', $poll_timestamp);
			echo '<select name="pollq_timestamp_day" size="1">'."\n";
			for($i = 1; $i <=31; $i++) {
				if($day == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;&nbsp;'."\n";
			$month2 = gmdate('n', $poll_timestamp);
			echo '<select name="pollq_timestamp_month" size="1">'."\n";
			for($i = 1; $i <= 12; $i++) {
				if ($i < 10) {
					$ii = '0'.$i;
				} else {
					$ii = $i;
				}
				if($month2 == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$month[$ii]</option>\n";	
				} else {
					echo "<option value=\"$i\">$month[$ii]</option>\n";	
				}
			}
			echo '</select>&nbsp;&nbsp;'."\n";
			$year = gmdate('Y', $poll_timestamp);
			echo '<select name="pollq_timestamp_year" size="1">'."\n";
			for($i = 2000; $i <= gmdate('Y'); $i++) {
				if($year == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;@'."\n";
			$hour = gmdate('H', $poll_timestamp);
			echo '<select name="pollq_timestamp_hour" size="1">'."\n";
			for($i = 0; $i < 24; $i++) {
				if($hour == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;:'."\n";
			$minute = gmdate('i', $poll_timestamp);
			echo '<select name="pollq_timestamp_minute" size="1">'."\n";
			for($i = 0; $i < 60; $i++) {
				if($minute == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			
			echo '</select>&nbsp;:'."\n";
			$second = gmdate('s', $poll_timestamp);
			echo '<select name="pollq_timestamp_second" size="1">'."\n";
			for($i = 0; $i <= 60; $i++) {
				if($second == $i) {
					echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>'."\n";
		}
?>
		<script type="text/javascript">
			function check_totalvotes() {	
				var total_votes = 0;
				var temp_vote = 0;
				<?php
					foreach($poll_answers as $poll_answer) {
						$polla_aid = intval($poll_answer->polla_aid);
						echo "\t\t\t\ttemp_vote = parseInt(document.getElementById('polla_votes-$polla_aid').value);\n";
						echo "\t\t\t\tif(isNaN(temp_vote)) {\n";
						echo "\t\t\t\tdocument.getElementById('polla_votes-$polla_aid').value = 0;\n";
						echo "\t\t\t\ttemp_vote = 0;\n";
						echo "\t\t\t\t}\n";
						echo "\t\t\t\ttotal_votes += temp_vote;\n";
					}
				?>
				document.getElementById('pollq_totalvotes').value = parseInt(total_votes);
			}
		</script>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Edit Poll -->
		<div class="wrap">
			<h2><?php _e('Edit Poll', 'wp-polls'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<input type="hidden" name="pollq_id" value="<?php echo $poll_id; ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th colspan="2"><?php _e('Question', 'wp-polls') ?></th>
					</tr>
					<tr>
						<td align="center" colspan="2"><input type="text" size="70" maxlength="200" name="pollq_question" value="<?php echo htmlspecialchars($poll_question_text); ?>" /></td>
					</tr>
					<tr>
						<th align="left"><?php _e('Answers:', 'wp-polls') ?></th>
						<th align="right"><?php _e('No. Of Votes', 'wp-polls') ?></th>
					</tr>
					<?php
						$i=1;
						$poll_actual_totalvotes = 0;
						if($poll_answers) {
							$pollip_answers = array();
							$pollip_answers[0] = __('Null Votes', 'wp-polls'); 
							foreach($poll_answers as $poll_answer) {
								$polla_aid = intval($poll_answer->polla_aid);
								$polla_answers = stripslashes($poll_answer->polla_answers);
								$polla_votes = intval($poll_answer->polla_votes);
								$pollip_answers[$polla_aid] = $polla_answers;
								echo "<tr>\n";
								echo "<td align=\"left\">".__('Answer', 'wp-polls')." $i:&nbsp;&nbsp;&nbsp;<input type=\"text\" size=\"50\" maxlength=\"200\" name=\"polla_aid-$polla_aid\" value=\"".htmlspecialchars($polla_answers)."\" />&nbsp;&nbsp;&nbsp;";
								echo "<a href=\"$base_page&amp;mode=deleteans&amp;id=$poll_id&amp;aid=$polla_aid\" onclick=\"return confirm('".__('You Are About To Delete This Poll Answer:', 'wp-polls')." \'".addslashes(strip_tags($polla_answers))."\'\\n\\n".__('This Action Is Not Reversible. Are you sure?', 'wp-polls')."')\">".__('Delete')."</a></td>\n";
								echo "<td align=\"right\">$polla_votes&nbsp;&nbsp;&nbsp;<input type=\"text\" size=\"4\" maxlength=\"6\" id=\"polla_votes-$polla_aid\" name=\"polla_votes-$polla_aid\" value=\"$polla_votes\" onblur=\"check_totalvotes();\" /></td>\n</tr>\n";
								$poll_actual_totalvotes += $polla_votes;
								$i++;
							}
						}
					?>
					<tr>
						<td align="right" colspan="2"><strong><?php _e('Total Votes', 'wp-polls'); ?>: <?php echo $poll_actual_totalvotes; ?></strong>&nbsp;&nbsp;&nbsp;<input type="text" size="4" maxlength="4" id="pollq_totalvotes" name="pollq_totalvotes" value="<?php echo $poll_actual_totalvotes; ?>" onblur="check_totalvotes();" /></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php _e('Timestamp', 'wp-polls'); ?></strong>:</td>
					</tr>
					<tr>
						<td colspan="2"><input type="checkbox" name="edit_polltimestamp" value="1" /><?php _e('Edit Timestamp', 'wp-polls'); ?><br /><?php poll_timestamp($poll_timestamp); ?><br /><?php _e('Existing Timestamp:', 'wp-polls'); ?> <?php echo mysql2date(get_settings('date_format').' @ '.get_settings('time_format'), gmdate('Y-m-d H:i:s', $poll_timestamp)); ?></td>
					</tr>
					<tr>
						<td align="center" colspan="2"><input type="submit" name="do" value="<?php _e('Edit Poll', 'wp-polls'); ?>" class="button" />&nbsp;&nbsp;
						<?php if($poll_active == 1) { ?>
						<input type="submit" class="button" name="do" value="<?php _e('Close Poll', 'wp-polls'); ?>" alt="test" onclick="return confirm('<?php _e('You Are About To Close This Poll', 'wp-polls'); ?>.')" />
						<?php } else { ?>
						<input type="submit" class="button" name="do" value="<?php _e('Open Poll', 'wp-polls'); ?>" onclick="return confirm('<?php _e('You Are About To Open This Poll', 'wp-polls'); ?>.')" />
						<?php } ?>
						&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-polls'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Add Poll's Answer -->
		<div class="wrap">
			<h2><?php _e('Add Answer', 'wp-polls') ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>&amp;mode=edit&amp;id=<?php echo $poll_id; ?>" method="post">
				<input type="hidden" name="polla_qid" value="<?php echo $poll_id; ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td><strong><?php _e('Add Answer', 'wp-polls') ?></strong></td>
						<td><input type="text" size="50" maxlength="200" name="polla_answers" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add Answer', 'wp-polls'); ?>" class="button" /></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Users Voted For This Poll -->
		<?php
			$poll_ips = $wpdb->get_results("SELECT pollip_aid, pollip_ip, pollip_host, pollip_timestamp, pollip_user FROM $wpdb->pollsip WHERE pollip_qid = $poll_id ORDER BY pollip_aid ASC, pollip_user ASC");
		?>
		<div class="wrap">
			<h2><?php _e('Users Voted For This Poll', 'wp-polls') ?></h2>
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<?php
						if($poll_ips) {
							$k = 1;
							$poll_last_aid = -1;
							foreach($poll_ips as $poll_ip) {
								$pollip_aid = intval($poll_ip->pollip_aid);
								$pollip_user = stripslashes($poll_ip->pollip_user);
								$pollip_ip = $poll_ip->pollip_ip;
								$pollip_host = $poll_ip->pollip_host;
								$pollip_date = mysql2date(get_settings('date_format').' @ '.get_settings('time_format'), gmdate('Y-m-d H:i:s', $poll_ip->pollip_timestamp));
								if($pollip_aid != $poll_last_aid) {
									if($pollip_aid == 0) {
										echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><strong>$pollip_answers[$pollip_aid]</strong></td>\n</tr>\n";
									} else {
										echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><strong>".__('Answer', 'wp-polls')." $k: $pollip_answers[$pollip_aid]</strong></td>\n</tr>\n";
										$k++;
									}
									echo "<tr>\n";
									echo "<th scope=\"row\">".__('No.', 'wp-polls')."</th>\n";
									echo "<th scope=\"row\">".__('User', 'wp-polls')."</th>\n";
									echo "<th scope=\"row\">".__('IP/Host', 'wp-polls')."</th>\n";
									echo "<th scope=\"row\">".__('Date', 'wp-polls')."</th>\n";
									echo "</tr>\n";
									$i = 1;
								}
								if($i%2 == 0) {
									$style = 'style=\'background-color: none\'';
								}  else {
									$style = 'style=\'background-color: #eee\'';
								}
								echo "<tr $style>\n";
								echo "<td>$i</td>\n";
								echo "<td>$pollip_user</td>\n";
								echo "<td>$pollip_ip / $pollip_host</td>\n";
								echo "<td>$pollip_date</td>\n";
								echo "</tr>\n";
								$poll_last_aid = $pollip_aid;
								$i++;
							}
						} else {
							echo "<tr>\n<td colspan=\"4\" align=\"center\">".__('No IP Has Been Logged Yet.', 'wp-polls')."</td>\n</tr>\n";
						}
					?>
				</table>
		</div>
		<!-- Delete Poll Logs -->
		<div class="wrap">
			<h2><?php _e('Poll Logs', 'wp-polls'); ?></h2>
			<div align="center">
				<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
					<input type="hidden" name="pollq_id" value="<?php echo $poll_id; ?>" />
					<strong><?php _e('Are You Sure You Want To Delete Logs For This Poll Only?', 'wp-polls'); ?></strong><br /><br />
					<input type="checkbox" name="delete_logs_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-polls'); ?><br /><br />
					<input type="submit" name="do" value="<?php _e('Delete Logs For This Poll Only', 'wp-polls'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Delete Logs For This Poll Only.', 'wp-polls'); ?>\n\n<?php _e('This Action Is Not Reversible. Are you sure?', 'wp-polls'); ?>')" />
				</form>
			</div>
			<p><?php _e('Note: If your logging method is by IP and Cookie or by Cookie, users may still be unable to vote if they have voted before as the cookie is still stored in their computer.', 'wp-polls'); ?></p>
		</div>
<?php
		break;
	// Delete A Poll
	case 'delete':
		$poll_question = $wpdb->get_row("SELECT pollq_question, pollq_totalvotes, pollq_active FROM $wpdb->pollsq WHERE pollq_id = $poll_id");
		$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_id ORDER BY polla_answers");
		$poll_question_text = stripslashes($poll_question->pollq_question);
		$poll_totalvotes = intval($poll_question->pollq_totalvotes);
		$poll_active = intval($poll_question->pollq_active);
?>
		<!-- Delete Poll -->
		<div class="wrap">
			<h2><?php _e('Delete Poll', 'wp-polls') ?></h2>
			<form action="<?php echo $base_page; ?>" method="post"> 
				<input type="hidden" name="pollq_id" value="<?php echo $poll_id; ?>" />
				<input type="hidden" name="pollq_question" value="<?php echo htmlspecialchars($poll_question_text); ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th colspan="2"><?php _e('Question', 'wp-polls') ?></th>
					</tr>
					<tr>
						<td colspan="2" align="center"><?php echo $poll_question_text; ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('Answers', 'wp-polls') ?></th>
						<th><?php _e('No. Of Votes', 'wp-polls') ?></th>
					</tr>
					<?php
						$i=1;
						if($poll_answers) {
							foreach($poll_answers as $poll_answer) {
								$polla_answers = stripslashes($poll_answer->polla_answers);
								$polla_votes = intval($poll_answer->polla_votes);								
								echo "<tr>\n";
								echo "<td>".__('Answer', 'wp-polls')." $i:&nbsp;&nbsp;&nbsp;$polla_answers</td>\n";
								echo "<td align=\"center\">$polla_votes</td>\n</tr>\n";
								$i++;
							}
						}
					?>
					<tr>
						<th colspan="2"><?php _e('Total Votes', 'wp-polls'); ?>: <?php echo $poll_totalvotes; ?></th>
					</tr>
					<tr>
						<th colspan="2"><?php _e('Status', 'wp-polls'); ?>: <?php if($poll_active == 1) { _e('Open', 'wp-polls'); } else { _e('Closed', 'wp-polls'); } ?></th>
					</tr>
					<tr>
						<td align="center" colspan="2"><br /><p><strong><?php _e('You Are About To Delete This Poll', 'wp-polls'); ?> '<?php echo $poll_question_text; ?>'</strong></p>
						<input type="submit" class="button" name="do" value="<?php _e('Delete Poll', 'wp-polls'); ?>" onclick="return confirm('<?php _e('You Are About To Delete This Poll', 'wp-polls'); ?>.\n\n<?php _e('This Action Is Not Reversible. Are you sure?', 'wp-polls'); ?>')" />&nbsp;&nbsp;
						<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-polls'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete A Poll Answer
	case 'deleteans':
		$poll_answers = $wpdb->get_row("SELECT polla_votes, polla_answers FROM $wpdb->pollsa WHERE polla_aid = $poll_aid AND polla_qid = $poll_id");
		$polla_votes = intval($poll_answers->polla_votes);
		$polla_answers = stripslashes(trim($poll_answers->polla_answers));
		$delete_polla_answers = $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_aid = $poll_aid AND polla_qid = $poll_id");
		$delete_pollip = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_aid = $poll_aid");
		$update_pollq_totalvotes = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_totalvotes = (pollq_totalvotes-$polla_votes) WHERE pollq_id=$poll_id");
?>
		<!-- Delete Poll's Answer -->
		<div class="wrap">
			<h2><?php _e('Delete Poll\'s Answer', 'wp-polls') ?></h2>
			<?php
				if($delete_polla_answers) {
					echo "<font color=\"green\">".__('Poll Answer', 'wp-polls')." '$polla_answers' ".__('Deleted Successfully', 'wp-polls')."</font>";
				} else {
					echo "<font color=\"red\">".__('Error In Deleting Poll Answer', 'wp-polls')." '$polla_answers'</font>";
				}
				if($update_pollq_totalvotes) {
					echo "<br /><font color=\"green\">".__('Poll Question\'s Total Votes Updated Successfully', 'wp-polls')."</font>";
				} else {
					echo "<br /><font color=\"blue\">".__('No Changes Have Been Made To The Poll\'s Total Votes', 'wp-polls')."</font>";
				}
				if($delete_pollip) {
					echo "<br /><font color=\"green\">".__('Poll IP Logs Updated Successfully', 'wp-polls')."</font>";
				} else {
					echo "<br /><font color=\"blue\">".__('No Changes Have Been Made To The Poll IP Logs', 'wp-polls')."</font>";
				}
			?>
			<p><strong><a href="<?php echo $base_page; ?>&amp;mode=edit&amp;id=<?php echo $poll_id; ?>"><?php _e('Click here To Go Back To The Poll Edit Page', 'wp-polls'); ?></a>.</strong></p>
		</div>
<?php
		break;
		//  Deactivating WP-Polls (By: Philippe Corbes)
		case 'end-UNINSTALL':
			echo '<div class="wrap">';
			echo '<h2>'; _e('Uninstall Polls', 'wp-polls'); echo'</h2>';
			echo '<p><strong>';
			$deactivate_url = "plugins.php?action=deactivate&amp;plugin=polls/polls.php";
			if(function_exists('wp_nonce_url')) { 
				$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_polls/polls.php');
			}
			printf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And WP-Polls Will Be Deactivated Automatically.', 'wp-polls'), $deactivate_url);
			echo '</a>';
			echo '</strong></p>';
			echo '</div>';
			break;
	// Main Page
	default:
		$polls = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY pollq_id DESC");
		$total_ans =  $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->pollsa");
		$total_votes = 0;
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Manage Polls -->
		<div class="wrap">
		<h2><?php _e('Manage Polls', 'wp-polls'); ?></h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr class="thead">
				<th><?php _e('ID', 'wp-polls'); ?></th>
				<th><?php _e('Question', 'wp-polls'); ?></th>				
				<th><?php _e('Total Votes', 'wp-polls'); ?></th>
				<th><?php _e('Date Added', 'wp-polls'); ?></th>
				<th><?php _e('Status', 'wp-polls'); ?></th>
				<th colspan="2"><?php _e('Action', 'wp-polls'); ?></th>
			</tr>
			<?php
				if($polls) {
					$i = 0;
					$current_poll = intval(get_settings('poll_currentpoll'));
					foreach($polls as $poll) {
						$poll_id = intval($poll->pollq_id);
						$poll_question = stripslashes($poll->pollq_question);
						$poll_date = mysql2date(get_settings('date_format').' @ '.get_settings('time_format'), gmdate('Y-m-d H:i:s', $poll->pollq_timestamp));
						$poll_totalvotes = intval($poll->pollq_totalvotes);
						$poll_active = intval($poll->pollq_active);
						if($i%2 == 0) {
							$style = 'style=\'background-color: #eee\'';
						}  else {
							$style = 'style=\'background-color: none\'';
						}
						if($current_poll > 0) {
							if($current_poll == $poll_id) {
								$style = 'style=\'background-color: #b8d4ff\'';
							}
						} else {
							if($i == 0) {
								$style = 'style=\'background-color: #b8d4ff\'';
							}
						}
						echo "<tr $style>\n";
						echo "<td><strong>$poll_id</strong></td>\n";
						echo '<td>';
						if($current_poll > 0) {
							if($current_poll == $poll_id) {
								echo '<strong>'.__('Displayed:', 'wp-polls').'</strong> ';
							}
						} elseif($current_poll != -1) {
							if($i == 0) {
								echo '<strong>'.__('Displayed:', 'wp-polls').'</strong> ';
							}
						}
						echo "$poll_question</td>\n";						
						echo "<td>$poll_totalvotes</td>\n";
						echo "<td>$poll_date</td>\n";
						echo '<td>';
						if($poll_active == 1) {
							_e('Open', 'wp-polls');
						} else {
							_e('Closed', 'wp-polls');
						}
						echo "</td>\n";
						echo "<td><a href=\"$base_page&amp;mode=edit&amp;id=$poll_id\" class=\"edit\">".__('Edit')."</a></td>\n";
						echo "<td><a href=\"$base_page&amp;mode=delete&amp;id=$poll_id\" class=\"delete\">".__('Delete')."</a></td>\n";
						echo '</tr>';
						$i++;
						$total_votes+= $poll_totalvotes;
						
					}
				} else {
					echo '<tr><td colspan="7" align="center"><strong>'.__('No Polls Found', 'wp-polls').'</strong></td></tr>';
				}
			?>
			</table>
		</div>
		<!-- Add A Poll -->
		<div class="wrap">
			<h2><?php _e('Add A Poll', 'wp-polls'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>&amp;mode=add" method="post">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th align="left"><?php _e('Question', 'wp-polls') ?></th>
						<td><input type="text" size="50" maxlength="200" name="pollq_question" /></td>
					</tr>
					<tr>
						<th align="left"><?php _e('No. Of Answers:', 'wp-polls') ?></th>
						<td>
								<select size="1" name="poll_noquestion">
										<?php
										for($k=2; $k <= 20; $k++) {
											echo "<option value=\"$k\">$k</option>";
										}
										?>
								</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="addpollquestion" value="<?php _e('Add Question', 'wp-polls'); ?>" class="button" /></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Polls Stats -->
		<div class="wrap">
		<h2><?php _e('Polls Stats', 'wp-polls'); ?></h2>
			<table border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th align="left"><?php _e('Total Polls:', 'wp-polls'); ?></th>
				<td align="left"><?php echo $i; ?></td>
			</tr>
			<tr>
				<th align="left"><?php _e('Total Polls\' Answers:', 'wp-polls'); ?></th>
				<td align="left"><?php echo number_format($total_ans); ?></td>
			</tr>
			<tr>
				<th align="left"><?php _e('Total Votes Casted:', 'wp-polls'); ?></th>
				<td align="left"><?php echo number_format($total_votes); ?></td>
			</tr>
			</table>
		</div>
		<!-- Delete Polls Logs -->
		<div class="wrap">
			<h2><?php _e('Polls Logs', 'wp-polls'); ?></h2>
			<div align="center">
				<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
					<strong><?php _e('Are You Sure You Want To Delete All Polls Logs?', 'wp-polls'); ?></strong><br /><br />
					<input type="checkbox" name="delete_logs_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-polls'); ?><br /><br />
					<input type="submit" name="do" value="<?php _e('Delete All Logs', 'wp-polls'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Delete All Poll Logs.', 'wp-polls'); ?>\n\n<?php _e('This Action Is Not Reversible. Are you sure?', 'wp-polls'); ?>')" />
				</form>
			</div>
			<p style="text-align: left;"><?php _e('Note:<br />If your logging method is by IP and Cookie or by Cookie, users may still be unable to vote if they have voted before as the cookie is still stored in their computer.', 'wp-polls'); ?></p>
		</div>
		<!-- Uninstall WP-Polls (By: Philippe Corbes) -->
		<div class="wrap">
			<h2><?php _e('Uninstall Polls', 'wp-polls'); ?></h2>
			<div align="center">
				<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
					<p style="text-align: left;">
						<?php _e('Deactivating WP-Polls plugin does not remove any data that may have been created, such as the poll data and the poll\'s voting logs. To completely remove this plugin, you can uninstall it here.', 'wp-polls'); ?>
					</p>
					<p style="text-align: left; color: red">
						<?php 
							vprintf(__('<strong>WARNING:</strong><br />Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.  Your data is stored in the %1$s, %2$s, %3$s and %4$s tables.', 'wp-polls'), array("<strong><em>{$wpdb->pollsq}</em></strong>", "<strong><em>{$wpdb->pollsa}</em></strong>", "<strong><em>{$wpdb->pollsip}</em></strong>", "<strong><em>{$wpdb->options}</em></strong>")); ?>
					</p>
					<input type="checkbox" name="uninstall_poll_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-polls'); ?><br /><br />
					<input type="submit" name="do" value="<?php _e('UNINSTALL Polls', 'wp-polls'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Uninstall WP-Polls From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'wp-polls'); ?>')" />
				</form>
			</div>
		</div>
<?php
} // End switch($mode)
?>