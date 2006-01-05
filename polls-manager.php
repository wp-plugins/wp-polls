<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Polls 2.04										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Manage Your Polls																|
|	- wp-admin/polls-manager.php												|
|																							|
+----------------------------------------------------------------+
*/


### Require Admin
require_once('admin.php');

### Variables Variables Variables
$title = __('Manage Polls');
$this_file = 'polls-manager.php';
$parent_file = 'polls-manager.php';
$mode = trim($_GET['mode']);
$poll_id = intval($_GET['id']);
$poll_aid = intval($_GET['aid']);

### Cancel
if(isset($_POST['cancel'])) {
	Header('Location: polls-manager.php');
	exit();
}

### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Add Poll
		case 'Add Poll':
			// Add Poll Question
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			$pollq_timestamp = current_time('timestamp');
			$add_poll_question = $wpdb->query("INSERT INTO $wpdb->pollsq VALUES (0, '$pollq_question', '$pollq_timestamp', 0)");
			if(!$add_poll_question) {
				$text .= '<font color="red">Error In Adding Poll \''.stripslashes($pollq_question).'\'</font>';
			}
			// Add Poll Answers
			$polla_answers = $_POST['polla_answers'];
			$polla_qid = intval($wpdb->insert_id);
			foreach($polla_answers as $polla_answer) {
				$polla_answer = addslashes(trim($polla_answer));
				$add_poll_answers = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0, $polla_qid, '$polla_answer', 0)");
				if(!$add_poll_answers) {
					$text .= '<font color="red">Error In Adding Poll\'s Answer \''.stripslashes($polla_answer).'\'</font>';
				}
			}
			// Update Lastest Poll ID To Poll Options
			$update_latestpoll = $wpdb->query("UPDATE $wpdb->options SET option_value = $polla_qid WHERE option_name = 'poll_latestpoll'");
			if(!$update_latestpoll) {
				$text .= "<font color=\"red\">There Is An Error Updating The Lastest Poll ID ($polla_qid) To The Poll Option</font>";
			}
			if(empty($text)) {
				$text = '<font color="green">Poll \''.stripslashes($pollq_question).'\' Added Successfully</font>';
				wp_cache_flush();
			}
			break;
		// Edit Poll
		case 'Edit Poll':
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
				$text = '<font color="blue">No Changes Had Been Made To \''.stripslashes($pollq_question).'\'</font>';
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
						$text .= '<br /><font color="blue">No Changes Had Been Made To Poll\'s Answer \''.stripslashes($polla_answers).'\'</font>';
					}
				}
			} else {
				$text .= '<br /><font color="red">Invalid Poll \''.stripslashes($pollq_question).'\'</font>';
			}
			if(empty($text)) {
				$text = '<font color="green">Poll \''.stripslashes($pollq_question).'\' Edited Successfully</font>';
			}
			break;
		// Delete Poll
		case 'Delete Poll':
			$pollq_id  = intval($_POST['pollq_id']);
			$pollq_question = trim($_POST['pollq_question']);
			$delete_poll_question = $wpdb->query("DELETE FROM $wpdb->pollsq WHERE pollq_id = $pollq_id");
			$delete_poll_answers =  $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_qid = $pollq_id");
			$delete_poll_ip = $wpdb->query("DELETE FROM $wpdb->pollsip WHERE pollip_qid = $pollq_id");
			$poll_option_lastestpoll = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'poll_latestpoll'");
			if(!$delete_poll_question) {
				$text = '<font color="red">Error In Deleting Poll \''.stripslashes($pollq_question).'\' Question</font>';
			} 
			if(!$delete_poll_answers) {
				$text .= '<br /><font color="red">Error In Deleting Poll Answers For \''.stripslashes($pollq_question).'\'</font>';
			}
			if(!$delete_poll_ip) {
				$text .= '<br /><font color="red">Error In Deleting Voted IPs For \''.stripslashes($pollq_question).'\'</font>';
			}
			if(empty($text)) {
				if($poll_option_lastestpoll == $pollq_id) {
					$poll_lastestpoll = $wpdb->get_var("SELECT pollq_id FROM $wpdb->pollsq ORDER BY pollq_id DESC LIMIT 1");
					if($poll_lastestpoll) {
						$poll_lastestpoll = intval($poll_lastestpoll);
						$update_latestpoll = $wpdb->query("UPDATE $wpdb->options SET option_value = $poll_lastestpoll WHERE option_name = 'poll_latestpoll'");
					}
				}
				$text = '<font color="green">Poll \''.stripslashes($pollq_question).'\' Deleted Successfully</font>';
			}
			break;
		// Add Poll's Answer
		case 'Add Answer':
			$polla_qid  = intval($_POST['polla_qid']);
			$polla_answers = addslashes(trim($_POST['polla_answers']));
			$add_poll_question = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0, $polla_qid, '$polla_answers', 0)");
			if(!$add_poll_question) {
				$text = '<font color="red">Error In Adding Poll Answer \''.stripslashes($polla_answers).'\'</font>';
			} else {
				$text = '<font color="green">Poll Answer \''.stripslashes($polla_answers).'\' Added Successfully</font>';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
	// Add A Poll
	case 'add':
		$title = __('Add Poll');
		require("./admin-header.php");
?>
		<div class="wrap">
				<h2>Add Poll</h2>
				<?php
				if(isset($_POST['addpollquestion'])) {
					$poll_noquestion = intval($_POST['poll_noquestion']);
					$pollq_question = stripslashes(trim($_POST['pollq_question']));	
				?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left" scope="row"><?php _e('Question') ?></th>
							<td><input type="text" size="50" maxlength="200" name="pollq_question" value="<?php echo $pollq_question; ?>"></td>
								<?php
									for($i=1; $i<=$poll_noquestion; $i++) {
										echo "<tr>\n";
										echo "<th align=\"left\" scope=\"row\">Answers $i:</th>\n";
										echo "<td><input type=\"text\" size=\"30\" maxlength=\"200\" name=\"polla_answers[]\"></td>\n";
										echo "</tr>\n";
									}
								?>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add Poll'); ?>"  class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="<?php _e('Cancel'); ?>" class="button"></td>
						</tr>
					</table>
				</form>
				<?php } else {?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=add" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left" scope="row"><?php _e('Question') ?></th>
							<td><input type="text" size="50" maxlength="200" name="pollq_question"></td>
						</tr>
							<th align="left" scope="row"><?php _e('No. Of Answers:') ?></th>
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
							<td colspan="2" align="center"><input type="submit" name="addpollquestion" value="<?php _e('Add Question'); ?>" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="<?php _e('Cancel'); ?>" class="button"></td>
						</tr>
					</table>
				</form>
				<?php } ?>
		</div>
<?php
		break;
	// Edit A Poll
	case 'edit':
		$title = __('Edit Poll');
		require("./admin-header.php");
		$poll_question = $wpdb->get_row("SELECT pollq_question, pollq_timestamp, pollq_totalvotes FROM $wpdb->pollsq WHERE pollq_id = $poll_id");
		$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_id ORDER BY polla_aid ASC");
		$poll_question_text = stripslashes($poll_question->pollq_question);
		$poll_totalvotes = intval($poll_question->pollq_totalvote);
		$poll_timestamp = $poll_question->pollq_timestamp;

		// Edit Timestamp Options
		function poll_timestamp($poll_timestamp) {
			global $month;
			$day = gmdate('j', $poll_timestamp);
			echo '<select name="pollq_timestamp_day" size="1">"'."\n";
			for($i = 1; $i <=31; $i++) {
				if($day == $i) {
					echo "<option value=\"$i\" selected=\"true\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;&nbsp;'."\n";
			$month2 = gmdate('n', $poll_timestamp);
			echo '<select name="pollq_timestamp_month" size="1">"'."\n";
			for($i = 1; $i <= 12; $i++) {
				if ($i < 10) {
					$ii = '0'.$i;
				} else {
					$ii = $i;
				}
				if($month2 == $i) {
					echo "<option value=\"$i\" selected=\"true\">$month[$ii]</option>\n";	
				} else {
					echo "<option value=\"$i\">$month[$ii]</option>\n";	
				}
			}
			echo '</select>&nbsp;&nbsp;'."\n";
			$year = gmdate('Y', $poll_timestamp);
			echo '<select name="pollq_timestamp_year" size="1">"'."\n";
			for($i = 2000; $i <= gmdate('Y'); $i++) {
				if($year == $i) {
					echo "<option value=\"$i\" selected=\"true\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;@'."\n";
			$hour = gmdate('H', $poll_timestamp);
			echo '<select name="pollq_timestamp_hour" size="1">"'."\n";
			for($i = 0; $i < 24; $i++) {
				if($hour == $i) {
					echo "<option value=\"$i\" selected=\"true\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>&nbsp;:'."\n";
			$minute = gmdate('i', $poll_timestamp);
			echo '<select name="pollq_timestamp_minute" size="1">"'."\n";
			for($i = 0; $i < 60; $i++) {
				if($minute == $i) {
					echo "<option value=\"$i\" selected=\"true\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			
			echo '</select>&nbsp;:'."\n";
			$second = gmdate('s', $poll_timestamp);
			echo '<select name="pollq_timestamp_second" size="1">"'."\n";
			for($i = 0; $i <= 60; $i++) {
				if($second == $i) {
					echo "<option value=\"$i\" selected=\"true\">$i</option>\n";	
				} else {
					echo "<option value=\"$i\">$i</option>\n";	
				}
			}
			echo '</select>'."\n";
		}
?>
		<script language="Javascript" type="text/javascript">
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
		<!-- Edit Poll -->
		<div class="wrap">
			<h2><?php _e('Edit Poll'); ?></h2>
			<form name="edit_poll" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="hidden" name="pollq_id" value="<?php echo $poll_id; ?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th scope="row" colspan="2"><?php _e('Question') ?></th>
					</tr>
					<tr>
						<td align="center" colspan="2"><input type="text" size="70" maxlength="200" name="pollq_question" value="<?php echo $poll_question_text; ?>" /></td>
					</tr>
					<tr>
						<th align="left" scope="row"><?php _e('Answers:') ?></th>
						<th align="right" scope="row"><?php _e('No. Of Votes') ?></th>
					</tr>
					<?php
						$i=1;
						$poll_actual_totalvotes = 0;
						if($poll_answers) {
							$pollip_answers = array();
							$pollip_answers[0] = __('Null Votes'); 
							foreach($poll_answers as $poll_answer) {
								$polla_aid = intval($poll_answer->polla_aid);
								$polla_answers = stripslashes($poll_answer->polla_answers);
								$polla_votes = intval($poll_answer->polla_votes);
								$pollip_answers[$polla_aid] = $polla_answers;
								echo "<tr>\n";
								echo "<td align=\"left\">".__('Answer')." $i:&nbsp;&nbsp;&nbsp;<input type=\"text\" size=\"50\" maxlength=\"200\" name=\"polla_aid-$polla_aid\" value=\"$polla_answers\" />&nbsp;&nbsp;&nbsp;";
								echo "<a href=\"polls-manager.php?mode=deleteans&id=$poll_id&aid=$polla_aid\" onclick=\"return confirm('You Are About To Delete This Poll Answer \'$polla_answers\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a></td>\n";
								echo "<td align=\"right\">$polla_votes&nbsp;&nbsp;&nbsp;<input type=\"text\" size=\"4\" maxlength=\"6\" id=\"polla_votes-$polla_aid\" name=\"polla_votes-$polla_aid\" value=\"$polla_votes\" onblur=\"check_totalvotes();\" /></td>\n</tr>\n";
								$poll_actual_totalvotes += $polla_votes;
								$i++;
							}
						}
					?>
					</tr>
					<tr>
						<td align="right" colspan="2"><b><?php _e('Total Votes'); ?>: <?php echo $poll_actual_totalvotes; ?></b>&nbsp;&nbsp;&nbsp;<input type="text" size="4" maxlength="4" id="pollq_totalvotes" name="pollq_totalvotes" value="<?php echo $poll_actual_totalvotes; ?>" onblur="check_totalvotes();" /></td>
					</tr>
					<tr>
						<td colspan="2"><b><?php _e('Timestamp'); ?></b>:</td>
					</tr>
					<tr>
						<td colspan="2"><input type="checkbox" name="edit_polltimestamp" value="1" />Edit Timestamp<br /><?php poll_timestamp($poll_timestamp); ?><br />Existing Timestamp: <?php echo gmdate('jS F Y @ H:i:s', $poll_timestamp); ?></td>
					</tr>
					<tr>
						<td align="center" colspan="2"><input type="submit" name="do" value="<?php _e('Edit Poll'); ?>" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="<?php _e('Cancel'); ?>" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Add Poll's Answer -->
		<div class="wrap">
			<h2><?php _e('Add Answer') ?></h2>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=edit&id=<?php echo $poll_id; ?>" method="post">
				<input type="hidden" name="polla_qid" value="<?php echo $poll_id; ?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td><b><?php _e('Add Answer') ?></b></td>
						<td><input type="text" size="50" maxlength="200" name="polla_answers"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add Answer'); ?>" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Users Voted For This Poll -->
		<?php
			$poll_ips = $wpdb->get_results("SELECT pollip_aid, pollip_ip, pollip_host, pollip_timestamp, pollip_user FROM $wpdb->pollsip WHERE pollip_qid = $poll_id ORDER BY pollip_aid ASC, pollip_user ASC");
		?>
		<div class="wrap">
			<h2><?php _e('Users Voted For This Poll') ?></h2>
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
								$pollip_date = gmdate("jS F Y @ H:i", $poll_ip->pollip_timestamp);
								if($pollip_aid != $poll_last_aid) {
									if($pollip_aid == 0) {
										echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><b>$pollip_answers[$pollip_aid]</b></td>\n</tr>\n";
									} else {
										echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><b>".__('Answer')." $k: $pollip_answers[$pollip_aid]</b></td>\n</tr>\n";
										$k++;
									}
									echo "<tr>\n";
									echo "<th scope=\"row\">".__('No.')."</th>\n";
									echo "<th scope=\"row\">".__('User')."</th>\n";
									echo "<th scope=\"row\">".__('IP/Host')."</th>\n";
									echo "<th scope=\"row\">".__('Date')."</th>\n";
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
							echo "<tr>\n<td colspan=\"4\" align=\"center\">".__('No IP Has Been Logged Yet.')."</td>\n</tr>\n";
						}
					?>
				</table>
		</div>
<?php
		break;
	// Delete A Poll
	case 'delete':
		$title = __('Delete Poll');
		require("./admin-header.php");
		$poll_question = $wpdb->get_row("SELECT pollq_question, pollq_totalvotes FROM $wpdb->pollsq WHERE pollq_id = $poll_id");
		$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_id ORDER BY polla_answers");
		$poll_question_text = stripslashes($poll_question->pollq_question);
		$poll_totalvotes = intval($poll_question->pollq_totalvotes);
?>
		<!-- Delete Poll -->
		<div class="wrap">
			<h2><?php _e('Delete Poll') ?></h2>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
				<input type="hidden" name="pollq_id" value="<?php echo $poll_id; ?>">
				<input type="hidden" name="pollq_question" value="<?php echo $poll_question_text; ?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th colspan="2" scope="row"><?php _e('Question') ?></th>
					</tr>
					<tr>
						<td colspan="2" align="center"><?php echo $poll_question_text; ?></td>
					</tr>
					<tr>
						<th align="left" scope="row"><?php _e('Answers') ?></th>
						<th scope="row"><?php _e('No. Of Votes') ?></th>
					</tr>
					<?php
						$i=1;
						if($poll_answers) {
							foreach($poll_answers as $poll_answer) {
								$polla_answers = stripslashes($poll_answer->polla_answers);
								$polla_votes = intval($poll_answer->polla_votes);								
								echo "<tr>\n";
								echo "<td>".__('Answer')." $i:&nbsp;&nbsp;&nbsp;$polla_answers</td>\n";
								echo "<td align=\"center\">$polla_votes</td>\n</tr>\n";
								$i++;
							}
						}
					?>
					</tr>
					<tr>
						<th colspan="2" scope="row"><?php _e('Total Votes'); ?>: <?php echo $poll_totalvotes; ?></th>
					</tr>
					<tr>
						<td align="center" colspan="2"><br /><p><b><?php _e('You Are About To Delete This Poll'); ?> '<?php echo $poll_question_text; ?>'</b></p><input type="submit" class="button" name="do" value="<?php _e('Delete Poll'); ?>" onclick="return confirm('You Are About To The Delete This Poll \'<?php echo $poll_question_text; ?>\'.\nThis Action Is Not Reversible.\n\n Choose \'Cancel\' to stop, \'OK\' to delete.')">&nbsp;&nbsp;<input type="submit" name="cancel" Value="<?php _e('Cancel'); ?>" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete A Poll Answer
	case 'deleteans':
		$title = __('Delete Poll\'s Answer');
		require("./admin-header.php");
		$poll_answers = $wpdb->get_row("SELECT polla_votes, polla_answers FROM $wpdb->pollsa WHERE polla_aid = $poll_aid AND polla_qid = $poll_id");
		$polla_votes = intval($poll_answers->polla_votes);
		$polla_answers = stripslashes(trim($poll_answers->polla_answers));
		$delete_polla_answers = $wpdb->query("DELETE FROM $wpdb->pollsa WHERE polla_aid = $poll_aid AND polla_qid = $poll_id");
		$update_pollq_totalvotes = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_totalvotes = (pollq_totalvotes-$polla_votes) WHERE pollq_id=$poll_id");
?>
		<!-- Delete Poll's Answer -->
		<div class="wrap">
			<h2><?php _e('Delete Poll\'s Answer') ?></h2>
			<?php
				if($delete_polla_answers) {
					$text = "<font color=\"green\">Poll Answer '$polla_answers' Deleted Successfully</font>";
				} else {
					$text = "<font color=\"red\">Error In Deleting Poll Answer '$polla_answers'</font>";
				}
				if($update_pollq_totalvotes) {
					$text .= "<br /><font color=\"green\">Poll Question's Total Votes Updated Successfully</font>";
				} else {
					$text .= "<br /><font color=\"blue\">No Changes Had Been Made To The Poll's Total Votes</font>";
				}
				_e($text);
			?>
			<p><b><a href="polls-manager.php?mode=edit&id=<?php echo $poll_id; ?>"><?php _e('Click here To Go Back To The Poll Edit Page'); ?></a>.</b></p>
		</div>
<?php
		break;
	// Main Page
	default:
		$title = __('Manage Polls');
		require("./admin-header.php");
		$polls = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY pollq_id DESC");
		$total_ans =  $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->pollsa");
		$total_votes = 0;
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
		<!-- Manage Polls -->
		<div class="wrap">
		<h2><?php _e('Manage Polls'); ?></h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th scope="col"><?php _e('ID'); ?></b></th>
				<th scope="col"><?php _e('Question'); ?></b></th>
				<th scope="col"><?php _e('Total Votes'); ?></b></th>
				<th scope="col"><?php _e('Date Added'); ?></b></th>
				<th scope="col" colspan="2"><?php _e('Action'); ?></th>
			</tr>
			<?php
				if($polls) {
					$i = 0;
					$current_poll = intval(get_settings('poll_currentpoll'));
					foreach($polls as $poll) {
						$poll_id = intval($poll->pollq_id);
						$poll_question = stripslashes($poll->pollq_question);
						$poll_date = gmdate("jS F Y @ H:i", $poll->pollq_timestamp);
						$poll_totalvotes = intval($poll->pollq_totalvotes);
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
						echo "<td><b>$poll_id</b></td>\n";
						echo '<td>';
						if($current_poll > 0) {
							if($current_poll == $poll_id) {
								echo '<b>'.__('Displayed:').'</b> ';
							}
						} elseif($current_poll != -1) {
							if($i == 0) {
								echo '<b>'.__('Displayed:').'</b> ';
							}
						}
						echo "$poll_question</td>\n";
						echo "<td>$poll_totalvotes</td>\n";
						echo "<td>$poll_date</td>\n";
						echo "<td><a href=\"polls-manager.php?mode=edit&id=$poll_id\" class=\"edit\">".__('Edit')."</a></td>\n";
						echo "<td><a href=\"polls-manager.php?mode=delete&id=$poll_id\" class=\"delete\">".__('Delete')."</a></td>\n";
						echo '</tr>';
						$i++;
						$total_votes+= $poll_totalvotes;
						
					}
				} else {
					echo '<tr><td colspan="6" align="center"><b>'.__('No Polls Found').'</b></td></tr>';
				}
			?>
			</table>
		</div>
		<!-- Add A Poll -->
		<div class="wrap">
			<h2><?php _e('Add A Poll'); ?></h2>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=add" method="post">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th align="left" scope="row"><?php _e('Question') ?></th>
						<td><input type="text" size="50" maxlength="200" name="pollq_question"></td>
					</tr>
						<th align="left" scope="row"><?php _e('No. Of Answers:') ?></th>
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
						<td colspan="2" align="center"><input type="submit" name="addpollquestion" value="<?php _e('Add Question'); ?>" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="<?php _e('Cancel'); ?>" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- Polls Stats -->
		<div class="wrap">
		<h2><?php _e('Polls Stats'); ?></h2>
			<table border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th align="left" scope="row"><?php _e('Total Polls:'); ?></th>
				<td align="left"><?php echo $i; ?></td>
			</tr>
			<tr>
				<th align="left" scope="row"><?php _e('Total Polls\' Answers:'); ?></th>
				<td align="left"><?php echo number_format($total_ans); ?></td>
			</tr>
			<tr>
				<th align="left" scope="row"><?php _e('Total Votes Casted:'); ?></th>
				<td align="left"><?php echo number_format($total_votes); ?></td>
			</tr>
			</table>
		</div>
<?php
} // End switch($mode)

### Require Admin Footer
require_once 'admin-footer.php';
?>