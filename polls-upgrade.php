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
|	- Upgrade WP-Polls From 1.0x To 2.02										|
|	- wp-admin/polls-upgrade.php												|
|																							|
+----------------------------------------------------------------+
*/


### Require Config
require('../wp-config.php');

### Variables, Variables, Variables
$create_table = array();
$alter_table = array();
$insert_options = array();
$error = '';

### Create Tables (1 Table)
$create_table[] = "CREATE TABLE $wpdb->pollsip (".
	"pollip_id int(10) NOT NULL auto_increment,".
	"pollip_qid varchar(10) NOT NULL default '',".
	"pollip_aid varchar(10) NOT NULL default '',".
	"pollip_ip varchar(100) NOT NULL default '',".
	"pollip_host VARCHAR(200) NOT NULL default '',".
	"pollip_timestamp varchar(20) NOT NULL default '0000-00-00 00:00:00',".
	"pollip_user tinytext NOT NULL,".
	"PRIMARY KEY (pollip_id))";

### Alter Tables (2 Tables)
$alter_table[] = "ALTER TABLE $wpdb->pollsq CHANGE id pollq_id INT(10) NULL AUTO_INCREMENT ,".
	"CHANGE question pollq_question VARCHAR(200) NOT NULL ,".
	"CHANGE timestamp pollq_timestamp VARCHAR(20) NOT NULL ,".
	"CHANGE total_votes pollq_totalvotes INT(10) DEFAULT '0' NOT NULL";
$alter_table[] = "ALTER TABLE $wpdb->pollsa CHANGE aid polla_aid INT(10) NOT NULL AUTO_INCREMENT ,".
	"CHANGE qid polla_qid INT(10) DEFAULT '0' NOT NULL ,".
	"CHANGE answers polla_answers VARCHAR( 200 ) NOT NULL ,".
	"CHANGE votes polla_votes INT(10) DEFAULT '0' NOT NULL";

### Get Lastest Poll ID
$poll_latest_id = $wpdb->get_var("SELECT id FROM $wpdb->pollsq ORDER BY id DESC LIMIT 1");
if(intval($poll_latest_id) < 1) { $poll_latest_id = 1; }

### Insert Options  (16 Rows)
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_voteheader', 'Y', 3, '<table width=\\\"100%\\\" border=\\\"0\\\" cellspacing=\\\"3\\\" cellpadding=\\\"3\\\">\r\n<tr>\r\n<td align=\\\"center\\\"><b>%POLL_QUESTION%</b></td>\r\n</tr>', 20, 8, 'Template For Poll''s Question', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (124, 0, 'poll_template_votebody', 'Y', 3, '<tr>\r\n<td align=\\\"left\\\"><input type=\\\"radio\\\" name=\\\"poll-%POLL_ID%\\\" value=\\\"%POLL_ANSWER_ID%\\\" /> %POLL_ANSWER%</td>\r\n</tr>', 20, 8, 'Template For Poll''s Answers', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (123, 0, 'poll_template_votefooter', 'Y', 3, '<tr>\r\n<td align=\\\"center\\\"><input type=\\\"submit\\\" name=\\\"vote\\\" value=\\\"   Vote   \\\" class=\\\"Buttons\\\" /><br /><a href=\\\"index.php?pollresult=1\\\">View Results</a></td>\r\n</tr>\r\n</table>', 20, 8, 'Template For Poll''s Voting Footer', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultheader', 'Y', 3, '<table width=\\\"100%\\\" border=\\\"0\\\" cellspacing=\\\"3\\\" cellpadding=\\\"3\\\">\r\n<tr>\r\n<td colspan=\\\"2\\\" align=\\\"center\\\"><b>%POLL_QUESTION%</b></td>\r\n</tr>', 20, 8, '', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultbody', 'Y', 3, '<tr>\r\n<td align=\\\"left\\\" width=\\\"70%\\\">%POLL_ANSWER%<br /><img src=\\\"wp-images/pollbar.gif\\\" height=\\\"5\\\" width=\\\"%POLL_ANSWER_IMAGEWIDTH%\\\" alt=\\\"%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\\\" /></td>\r\n<td align=\\\"right\\\" width=\\\"30%\\\"><b>%POLL_ANSWER_PERCENTAGE%%</b></td>\r\n</tr>', 20, 8, '', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultbody2', 'Y', 3, '<tr>\r\n<td align=\\\"left\\\" width=\\\"70%\\\"><i>%POLL_ANSWER%</i><br /><img src=\\\"wp-images/pollbar.gif\\\" height=\\\"5\\\" width=\\\"%POLL_ANSWER_IMAGEWIDTH%\\\" alt=\\\"You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\\\" /></td>\r\n<td align=\\\"right\\\" width=\\\"30%\\\"><i><b>%POLL_ANSWER_PERCENTAGE%%</b></i></td>\r\n</tr>', 20, 8, '', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultfooter', 'Y', 3, '<tr>\r\n<td colspan=\\\"2\\\" align=\\\"center\\\">Total Votes: <b>%POLL_TOTALVOTES%</b><td>\r\n</tr>\r\n</table>', 20, 8, '', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_disable', 'Y', 3, 'Sorry, there are no polls available at the moment.', 20, 8, 'Template For Poll When It Is Disabled', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_error', 'Y', '3', 'An error has occurred when processing your poll.', '20', '8', 'Template For Poll When An Error Has Occured', '8', 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_currentpoll', 'Y', 3, '0', 20, 8, 'Current Displayed Poll', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_latestpoll', 'Y', 3, '$poll_latest_id', 20, 8, 'The Lastest Poll', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_archive_perpage', 'Y', 3, '10', 2, 8, 'Number Of Polls To Display Per Page On The Poll''s Archive', 8, 'no');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_sortby', 'Y', 1, 'polla_aid', 20, 8, 'Sorting Of Poll''s Answers', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_sortorder', 'Y', 1, 'asc', 20, 8, 'Sort Order Of Poll''s Answers', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_result_sortby', 'Y', 1, 'polla_votes', 20, 8, 'Sorting Of Poll''s Answers Result', 8, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_result_sortorder', 'Y', 1, 'desc', 20, 8, 'Sorting Order Of Poll''s Answers Result', 8, 'yes');";

### Check Whether There Is Any Pre Errors
$wpdb->show_errors = false;
$check_upgrade = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'poll_latestpoll'");
if($check_upgrade) {
	$error = __('You Had Already Installed WP-Polls.');
}
if(empty($wpdb->pollsq) || empty($wpdb->pollsa) || empty($wpdb->pollsip)) {
	$error = __('Please Define The pollsq, pollsa and pollsip in wp-settings.php.');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Upgrading'); ?> &rsaquo; <?php _e('WP-Polls'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css" media="screen">
		@import url( wp-admin.css );
	</style>
</head>
<body>
	<div class="wrap"> 
		<h2><?php _e('Upgrading WP-Polls'); ?></h2>
		<p><?php _e('This upgrade script will upgrade WP-Polls to version 2.0 for your Wordpress.'); ?></p>
		<p>
			<?php _e('This upgrade script will be doing the following:'); ?><br />
			<b>&raquo;</b> <b>1</b> <?php _e('table will be created namely <b>pollsip</b>.'); ?><br />
			<b>&raquo;</b> <b>2</b> <?php _e('tables will be altered namely <b>pollsq</b> and <b>pollsa</b>.'); ?><br />
			<b>&raquo;</b> <b>15</b> <?php _e('options will be inserted into the <b>options</b> table.'); ?><br />
			<b>&raquo;</b> <b>4</b> <?php _e('tables will be optimized namely <b>pollsq</b>, <b>pollsa</b>, <b>pollsip</b> and <b>options</b>.'); ?><br />
		</p>
		<?php
			if(empty($error)) {
				if(!empty($_POST['upgrade'])) {
					// Create Tables
					$create_table_count = 0;
					echo "<p><b>".__('Creating Tables:')."</b>";
					foreach($create_table as $createtable) {
						$wpdb->query($createtable);
					}
					$check_pollsip = $wpdb->query("SHOW COLUMNS FROM $wpdb->pollsip");
					if($check_pollsip) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsip</b>) created.";
						$create_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsip</b>) table NOT created.</font>";
					}
					echo "<br /><b>&raquo;</b> <b>$create_table_count / 1</b> Table Created.</p>";
					// Alter Table
					$alter_table_count = 0;
					echo "<p><b>".__('Altering Tables:')."</b>";
					foreach($alter_table as $altertable) {
						$wpdb->query($altertable);
					}
					$check_pollsq = $wpdb->get_var("SELECT pollq_id FROM $wpdb->pollsq LIMIT 1");
					$check_pollsa = $wpdb->get_var("SELECT polla_aid FROM $wpdb->pollsa LIMIT 1");
					if($check_pollsq) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsq</b>) altered.";
						$alter_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsip</b>) table NOT altered.</font>";
					}
					if($check_pollsa) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsa</b>) altered.";
						$alter_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsip</b>) table NOT altered.</font>";
					}
					echo "<br /><b>&raquo;</b> <b>$alter_table_count / 2</b> Tables Altered.</p>";
					// Insert Options
					$insert_options_count = 0;
					echo "<p><b>".__('Inserting Options:')."</b>";
					foreach($insert_options as $insertoptions) {
						$temp_options = $wpdb->query($insertoptions);
						$temp_option = explode(" ", $insertoptions);
						$temp_option = $temp_option[6];
						$temp_option = substr($temp_option, 1, -2);
						if($temp_options) {
								echo "<br /><b>&raquo;</b> Option (<b>$temp_option</b>) inserted.";
								$insert_options_count ++;
						} else {
							echo "<br /><b>&raquo;</b> <font color=\"red\">Option (<b>$temp_option</b>) NOT inserted.</font>";
						}
					}
					echo "<br /><b>&raquo;</b> <b>$insert_options_count / 16</b> Options Inserted.</p>";
					// Optimize Tables
					$optimize_table_count = 0;
					echo "<p><b>".__('Optimizing Tables:')."</b>";
					$optimize_tables = $wpdb->query("OPTIMIZE TABLE $wpdb->pollsq, $wpdb->pollsa, $wpdb->pollsip, $wpdb->options");
					if($optimize_tables) {
						echo "<br /><b>&raquo;</b> Tables (<b>$wpdb->pollsq</b>, <b>$wpdb->pollsa</b>, <b>$wpdb->pollsip</b>, <b>$wpdb->options</b>) optimized.";
						$optimize_table_count = 4;
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Tables (<b>$wpdb->pollsq</b>, <b>$wpdb->pollsa</b>, <b>$wpdb->pollsip</b>, <b>$wpdb->options</b>) NOT optimized.</font>";
					}
					echo "<br /><b>&raquo;</b> <b>$optimize_table_count / 4</b> Tables Optimized.</p>";
					// Check Whether Install Is Successful
					if($create_table_count == 1 && $alter_table_count == 2 && $insert_options_count == 16) {
						echo '<p align="center"><b>'.__('WP-Polls Upgraded Successfully.').'</b><br />'.__('Please remember to delete this file before proceeding on.').'</p>';
					}
				} else {
		?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<div align="center"><input type="submit" name="upgrade" value="<?php _e('Click Here To Upgrade WP-Polls'); ?>" class="button"></div>
				</form>
		<?php
				}
			} else {
				echo "<p align=\"center\"><font color=\"red\"><b>$error</b></font></p>\n";
			}
		?>
	</div>
</body>
</html>