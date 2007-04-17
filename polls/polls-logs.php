<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Polls 2.20										|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Polls Logs																			|
|	- wp-content/plugins/polls/polls-logs.php									|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}


$poll_question_data = $wpdb->get_row("SELECT pollq_multiple, pollq_question, pollq_totalvoters FROM $wpdb->pollsq WHERE pollq_id = $poll_id");
$poll_question = stripslashes($poll_question_data->pollq_question);
$poll_totalvoters = intval($poll_question_data->pollq_totalvoters);
$poll_multiple = intval($poll_question_data->pollq_multiple);
$poll_registered = $wpdb->get_var("SELECT COUNT(pollip_userid) FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_userid > 0");
$poll_comments = $wpdb->get_var("SELECT COUNT(pollip_user) FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_user != '".__('Guest', 'wp-polls')."' AND pollip_userid = 0");
$poll_guest = $wpdb->get_var("SELECT COUNT(pollip_user) FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_user = '".__('Guest', 'wp-polls')."'");
$poll_totalrecorded = ($poll_registered+$poll_comments+$poll_guest);
$poll_answers_data = $wpdb->get_results("SELECT polla_aid, polla_answers FROM $wpdb->pollsa WHERE polla_qid = $poll_id ORDER BY ".get_option('poll_ans_sortby').' '.get_option('poll_ans_sortorder'));
$poll_voters = $wpdb->get_col("SELECT pollip_user FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_user != '".__('Guest', 'wp-polls')."' ORDER BY pollip_user ASC");
?>
		<div class="wrap">
			<h2><?php _e('Poll\'s Logs', 'wp-polls'); ?></h2>
			<p><strong><?php echo $poll_question; ?></strong></p>
			<p>
				<?php printf(__('There are a total of <strong>%s</strong> recorded votes for this poll.', 'wp-polls'), $poll_totalrecorded); ?><br />
				<?php printf(__('<strong>&raquo;</strong> <strong>%s</strong> vote(s) are voted by registered users', 'wp-polls'), $poll_registered); ?><br />
				<?php printf(__('<strong>&raquo;</strong> <strong>%s</strong> vote(s) are voted by comment authors', 'wp-polls'), $poll_comments); ?><br />
				<?php printf(__('<strong>&raquo;</strong> <strong>%s</strong> vote(s) are voted by guests', 'wp-polls'), $poll_guest); ?>
			</p>
		</div>
				<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade">'.stripslashes($text).'</div>'; } else { echo '<div id="message" class="updated" style="display: none;"></div>'; } ?>
				<!-- Users Voted For This Poll -->
				<?php
					$poll_ips = $wpdb->get_results("SELECT pollip_aid, pollip_ip, pollip_host, pollip_timestamp, pollip_user FROM $wpdb->pollsip WHERE pollip_qid = $poll_id ORDER BY pollip_aid ASC, pollip_user ASC");
					if($poll_totalrecorded > 0) {
				?>
				<form id="poll_logs_form" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>"> 
				<div class="wrap">
					<h2><?php _e('Filter Poll\'s Logs', 'wp-polls') ?></h2>
					<div id="poll_logs_display">
						<table width="50%"  border="0" cellspacing="3" cellpadding="3">
							<tr>
								<td>
									<strong><?php _e('Display All Users That Voted For:', 'wp-polls'); ?></strong>
								</td>
								<td>
									<select name="users_voted_for" size="1">
										<?php
											if($poll_answers_data) {
												foreach($poll_answers_data as $data) {
													echo '<option value="'.$data->polla_aid.'">'.stripslashes(strip_tags(htmlspecialchars($data->polla_answers))).'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<strong><?php _e('Voters To Include', 'wp-polls'); ?></strong>
								</td>
								<td>
									<input type="checkbox" name="include_registered" value="1" />&nbsp;<?php _e('Registered Users', 'wp-polls'); ?><br />
									<input type="checkbox" name="include_comment" value="1" />&nbsp;<?php _e('Comment Authors', 'wp-polls'); ?><br />
									<input type="checkbox" name="include_guest" value="1" />&nbsp;<?php _e('Guests', 'wp-polls'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Filter', 'wp-polls'); ?>" class="button" /></td>
							</tr>
						</table>
						<table width="50%"  border="0" cellspacing="3" cellpadding="3">
							<tr>
								<td>
									<strong><?php _e('Display What This User Has Voted:', 'wp-polls'); ?></strong>
								</td>
								<td>
									<select name="what_user_voted" size="1">
										<?php
											if($poll_voters) {
												foreach($poll_voters as $pollip_user) {
													echo '<option value="'.stripslashes(htmlspecialchars($pollip_user)).'">'.stripslashes(htmlspecialchars($pollip_user)).'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Filter', 'wp-polls'); ?>" class="button" /></td>
							</tr>
						</table>
						<?php if($poll_multiple > -1) { ?>
						<table width="50%"  border="0" cellspacing="3" cellpadding="3">
							<tr>
								<td>
									<strong><?php _e('Display Users That Voted For: ', 'wp-polls'); ?></strong>
								</td>
								<td>
									<select name="num_choices_sign" size="1">
										<option value="more"><?php _e('More Than', 'wp-polls'); ?></option>
										<option value="more_exactly"><?php _e('More Than Or Exactly', 'wp-polls'); ?></option>
										<option value="exactly"><?php _e('Exactly', 'wp-polls'); ?></option>
										<option value="less_exactly"><?php _e('Less Than Or Exactly', 'wp-polls'); ?></option>
										<option value="less"><?php _e('Less Than', 'wp-polls'); ?></option>
									</select>
									&nbsp;&nbsp;
									<select name="num_choices" size="1">
										<?php 
											for($i = 1; $i <= $poll_multiple; $i++) {
												if($i == 1) {
													echo '<option value="1">'.__('1 Choice', 'wp-polls').'</option>';
												} else {
													echo '<option value="'.$i.'">'.sprintf(__('%s Choices', 'wp-polls'), $i).'</option>';
												}
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<strong><?php _e('Voters To Include', 'wp-polls'); ?></strong>
								</td>
								<td>
									<input type="checkbox" name="include_registered" value="1" />&nbsp;<?php _e('Registered Users', 'wp-polls'); ?><br />
									<input type="checkbox" name="include_comment" value="1" />&nbsp;<?php _e('Comment Authors', 'wp-polls'); ?><br />
									<input type="checkbox" name="include_guest" value="1" />&nbsp;<?php _e('Guests', 'wp-polls'); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Filter', 'wp-polls'); ?>" class="button" /></td>
							</tr>
						</table>
						<?php 
								} 
						}
						?>
							<?php
								if($poll_ips) {
									echo '<table width="100%"  border="0" cellspacing="3" cellpadding="3">'."\n";
									$k = 1;
									$poll_last_aid = -1;
									foreach($poll_ips as $poll_ip) {
										$pollip_aid = intval($poll_ip->pollip_aid);
										$pollip_user = stripslashes($poll_ip->pollip_user);
										$pollip_ip = $poll_ip->pollip_ip;
										$pollip_host = $poll_ip->pollip_host;
										$pollip_date = mysql2date(get_option('date_format').' @ '.get_option('time_format'), gmdate('Y-m-d H:i:s', $poll_ip->pollip_timestamp));
										if($pollip_aid != $poll_last_aid) {
											if($pollip_aid == 0) {
												echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><strong>$pollip_answers[$pollip_aid]</strong></td>\n</tr>\n";
											} else {
												echo "<tr style='background-color: #b8d4ff'>\n<td colspan=\"4\"><strong>".__('Answer', 'wp-polls')." $k: $pollip_answers[$pollip_aid]</strong></td>\n</tr>\n";
												$k++;
											}
											echo "<tr class=\"thead\">\n";
											echo "<th>".__('No.', 'wp-polls')."</th>\n";
											echo "<th>".__('User', 'wp-polls')."</th>\n";
											echo "<th>".__('IP/Host', 'wp-polls')."</th>\n";
											echo "<th>".__('Date', 'wp-polls')."</th>\n";
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
									echo '</table>'."\n";
								}
							?>
					</div>
					<div id="poll_logs_display_none" style="text-align: center; display: <?php if(!$poll_ips) { echo 'block'; } else { echo 'none'; } ?>;" ><?php _e('No poll logs available for this poll.', 'wp-polls'); ?></div>
				</div>
				</form>
		<!-- Delete Poll Logs -->
		<div class="wrap">
			<h2><?php _e('Poll Logs', 'wp-polls'); ?></h2>
			<div align="center" id="poll_logs">
				<?php if($poll_ips) { ?>
					<strong><?php _e('Are You Sure You Want To Delete Logs For This Poll Only?', 'wp-polls'); ?></strong><br /><br />
					<input type="checkbox" id="delete_logs_yes" name="delete_logs_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-polls'); ?><br /><br />
					<input type="button" name="do" value="<?php _e('Delete Logs For This Poll Only', 'wp-polls'); ?>" class="button" onclick="delete_this_poll_logs(<?php echo $poll_id; ?>, '<?php printf(js_escape(__('You are about to delete poll logs for this poll \'%s\' ONLY. This action is not reversible.', 'wp-polls')), htmlspecialchars($poll_question_text)); ?>');" />
				<?php 
					} else {
						_e('No poll logs available for this poll.', 'wp-polls');
					}
				?>
			</div>
			<p><?php _e('Note: If your logging method is by IP and Cookie or by Cookie, users may still be unable to vote if they have voted before as the cookie is still stored in their computer.', 'wp-polls'); ?></p>
		</div>