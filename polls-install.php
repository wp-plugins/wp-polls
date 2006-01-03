<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Polls 2.03										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Install WP-Polls 2.03															|
|	- wp-admin/polls-install.php													|
|																							|
+----------------------------------------------------------------+
*/


### Require Config
require('../wp-config.php');

### Variables, Variables, Variables
$current_timestamp = current_time('timestamp');
$create_table = array();
$insert_pollq = array();
$insert_polla = array();
$insert_options = array();
$error = '';

### Create Tables (3 Tables)
$create_table[] = "CREATE TABLE $wpdb->pollsq (".
	"pollq_id int(10) NOT NULL auto_increment,".
	"pollq_question varchar(200) NOT NULL default '',".
	"pollq_timestamp varchar(20) NOT NULL default '',".
	"pollq_totalvotes int(10) NOT NULL default '0',".
	"PRIMARY KEY (pollq_id))";
$create_table[] = "CREATE TABLE $wpdb->pollsa (".
	"polla_aid int(10) NOT NULL auto_increment,".
	"polla_qid int(10) NOT NULL default '0',".
	"polla_answers varchar(200) NOT NULL default '',".
	"polla_votes int(10) NOT NULL default '0',".
	"PRIMARY KEY (polla_aid))";
$create_table[] = "CREATE TABLE $wpdb->pollsip (".
	"pollip_id int(10) NOT NULL auto_increment,".
	"pollip_qid varchar(10) NOT NULL default '',".
	"pollip_aid varchar(10) NOT NULL default '',".
	"pollip_ip varchar(100) NOT NULL default '',".
	"pollip_host VARCHAR(200) NOT NULL default '',".
	"pollip_timestamp varchar(20) NOT NULL default '0000-00-00 00:00:00',".
	"pollip_user tinytext NOT NULL,".
	"PRIMARY KEY (pollip_id))";

### Insert Poll Question (1 Row)
$insert_pollq[] = "INSERT INTO $wpdb->pollsq VALUES (1, 'How Is My Site?', '$current_timestamp', 0);";

### Insert Poll Answers  (5 Rows)
$insert_polla[] = "INSERT INTO $wpdb->pollsa VALUES (1, 1, 'Good', 0);";
$insert_polla[] = "INSERT INTO $wpdb->pollsa VALUES (2, 1, 'Excellent', 0);";
$insert_polla[] = "INSERT INTO $wpdb->pollsa VALUES (3, 1, 'Bad', 0);";
$insert_polla[] = "INSERT INTO $wpdb->pollsa VALUES (4, 1, 'Can Be Improved', 0);";
$insert_polla[] = "INSERT INTO $wpdb->pollsa VALUES (5, 1, 'No Comments', 0);";

### Insert Options  (16 Rows)
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_voteheader', 'Y', 1, '<table width=\\\"100%\\\" border=\\\"0\\\" cellspacing=\\\"3\\\" cellpadding=\\\"3\\\">\r\n<tr>\r\n<td align=\\\"center\\\"><b>%POLL_QUESTION%</b></td>\r\n</tr>', 20, 8, 'Template For Poll''s Question', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_votebody', 'Y', 1, '<tr>\r\n<td align=\\\"left\\\"><input type=\\\"radio\\\" name=\\\"poll-%POLL_ID%\\\" value=\\\"%POLL_ANSWER_ID%\\\" /> %POLL_ANSWER%</td>\r\n</tr>', 20, 8, 'Template For Poll''s Answers', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_votefooter', 'Y', 1, '<tr>\r\n<td align=\\\"center\\\"><input type=\\\"submit\\\" name=\\\"vote\\\" value=\\\"   Vote   \\\" class=\\\"Buttons\\\" /><br /><a href=\\\"index.php?pollresult=1\\\">View Results</a></td>\r\n</tr>\r\n</table>', 20, 8, 'Template For Poll''s Voting Footer', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultheader', 'Y', 1, '<table width=\\\"100%\\\" border=\\\"0\\\" cellspacing=\\\"3\\\" cellpadding=\\\"3\\\">\r\n<tr>\r\n<td colspan=\\\"2\\\" align=\\\"center\\\"><b>%POLL_QUESTION%</b></td>\r\n</tr>', 20, 8, '', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultbody', 'Y', 1, '<tr>\r\n<td align=\\\"left\\\" width=\\\"70%\\\">%POLL_ANSWER%<br /><img src=\\\"".get_settings('home')."/wp-includes/images/pollbar.gif\\\" height=\\\"5\\\" width=\\\"%POLL_ANSWER_IMAGEWIDTH%\\\" alt=\\\"%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\\\" /></td>\r\n<td align=\\\"right\\\" width=\\\"30%\\\"><b>%POLL_ANSWER_PERCENTAGE%%</b></td>\r\n</tr>', 20, 8, '', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultbody2', 'Y', 1, '<tr>\r\n<td align=\\\"left\\\" width=\\\"70%\\\"><i>%POLL_ANSWER%</i><br /><img src=\\\"".get_settings('home')."/wp-includes/images/pollbar.gif\\\" height=\\\"5\\\" width=\\\"%POLL_ANSWER_IMAGEWIDTH%\\\" alt=\\\"You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)\\\" /></td>\r\n<td align=\\\"right\\\" width=\\\"30%\\\"><i><b>%POLL_ANSWER_PERCENTAGE%%</b></i></td>\r\n</tr>', 20, 8, '', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_resultfooter', 'Y', 1, '<tr>\r\n<td colspan=\\\"2\\\" align=\\\"center\\\">Total Votes: <b>%POLL_TOTALVOTES%</b><td>\r\n</tr>\r\n</table>', 20, 8, '', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_disable', 'Y', 1, 'Sorry, there are no polls available at the moment.', 20, 8, 'Template For Poll When It Is Disabled', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_error', 'Y', 1, 'An error has occurred when processing your poll.', '20', '8', 'Template For Poll When An Error Has Occured', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_currentpoll', 'Y', 1, '0', 20, 8, 'Current Displayed Poll', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_latestpoll', 'Y', 1, '1', 20, 8, 'The Lastest Poll', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_archive_perpage', 'Y', 1, '5', 2, 8, 'Number Of Polls To Display Per Page On The Poll''s Archive', 1, 'no');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_sortby', 'Y', 1, 'polla_aid', 20, 8, 'Sorting Of Poll''s Answers', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_sortorder', 'Y', 1, 'asc', 20, 8, 'Sort Order Of Poll''s Answers', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_result_sortby', 'Y', 1, 'polla_votes', 20, 8, 'Sorting Of Poll''s Answers Result', 1, 'yes');";
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_ans_result_sortorder', 'Y', 1, 'desc', 20, 8, 'Sorting Order Of Poll''s Answers Result', 1, 'yes');";

### Check Whether There Is Any Pre Errors
$wpdb->show_errors = false;
$check_install = $wpdb->query("SHOW COLUMNS FROM $wpdb->pollsq");
if($check_install) {
	$error = __('You Had Already Installed WP-Polls.');
}
if(empty($wpdb->pollsq) || empty($wpdb->pollsa) || empty($wpdb->pollsip)) {
	$error = __('Please Define The pollsq, pollsa and pollsip in wp-settings.php.');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Installing'); ?> &rsaquo; <?php _e('WP-Polls 2.03'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css" media="screen">
		@import url( wp-admin.css );
	</style>
</head>
<body>
	<div class="wrap"> 
		<h2><?php _e('Install WP-Polls 2.03'); ?></h2>
		<p><?php _e('This install script will install WP-Polls 2.03 for your Wordpress'); ?>.</p>
		<p>
			<?php _e('This install script will be doing the following:'); ?><br />
			<b>&raquo;</b> <b>3</b> <?php _e('tables will be created namely <b>pollsq</b>, <b>pollsa</b> and <b>pollsip</b>.'); ?><br />
			<b>&raquo;</b> <b>1</b> <?php _e('poll question (<b>How Is My Site?</b>) will be inserted into <b>pollsq</b> table.'); ?><br />
			<b>&raquo;</b> <b>5</b> <?php _e('poll answers(<b>Good</b>, <b>Excellent</b>, <b>Bad</b>, <b>Can Be Improved</b>, <b>No Comments</b>) will be inserted into <b>pollsa</b> table.'); ?><br />
			<b>&raquo;</b> <b>15</b> <?php _e('options will be inserted into the <b>options</b> table.'); ?><br />
			<b>&raquo;</b> <b>4</b> <?php _e('tables will be optimized namely <b>pollsq</b>, <b>pollsa</b>, <b>pollsip</b> and <b>options</b>.'); ?><br />
		</p>
		<?php
			if(empty($error)) {
				if(!empty($_POST['install'])) {
					// Create Tables
					$create_table_count = 0;
					echo "<p><b>".__('Creating Tables:')."</b>";
					foreach($create_table as $createtable) {
						$wpdb->query($createtable);
					}
					$check_pollsq = $wpdb->query("SHOW COLUMNS FROM $wpdb->pollsq");
					$check_pollsa = $wpdb->query("SHOW COLUMNS FROM $wpdb->pollsa");
					$check_pollsip = $wpdb->query("SHOW COLUMNS FROM $wpdb->pollsip");
					if($check_pollsq) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsq</b>) created.";
						$create_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsq</b>) table NOT created.</font>";
					}
					if($check_pollsa) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsa</b>) created.";
						$create_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsa</b>) table NOT created.</font>";
					}
					if($check_pollsip) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsip</b>) created.";
						$create_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsip</b>) table NOT created.</font>";
					}
					echo "<br /><b>&raquo;</b> <b>$create_table_count / 3</b> Tables Created.</p>";
					// Insert Poll Questions
					$insert_pollq_count = 0;
					echo "<p><b>".__('Inserting Poll Questions:')."</b>";
					foreach($insert_pollq as $insertpollq) {
						$temp_pollq = $wpdb->query($insertpollq);
						$temp_poll_question = explode("VALUES ", $insertpollq);
						$temp_poll_question = $temp_poll_question[1];
						$temp_poll_question = substr($temp_poll_question, 5, -20);
						if($temp_pollq) {
								echo "<br /><b>&raquo;</b> Poll question (<b>$temp_poll_question</b>) inserted.";
								$insert_pollq_count ++;
						} else {
							echo "<br /><b>&raquo;</b> <font color=\"red\">Poll question (<b>$temp_poll_question</b>) NOT inserted.</font>";
						}
					}
					echo "<br /><b>&raquo;</b> <b>$insert_pollq_count / 1</b> Poll Questions Inserted.</p>";
					// Insert Poll Answers
					$insert_polla_count = 0;
					echo "<p><b>".__('Inserting Poll Answers:')."</b>";
					foreach($insert_polla as $insertpolla) {
						$temp_polla = $wpdb->query($insertpolla);
						$temp_poll_answer = explode("VALUES ", $insertpolla);
						$temp_poll_answer = $temp_poll_answer[1];
						$temp_poll_answer = substr($temp_poll_answer, 8, -6);
						if($temp_polla) {
								echo "<br /><b>&raquo;</b> Poll answer (<b>$temp_poll_answer</b>) inserted.";
								$insert_polla_count ++;
						} else {
							echo "<br /><b>&raquo;</b> <font color=\"red\">Poll answer (<b>$temp_poll_answer</b>) NOT inserted.</font>";
						}
					}
					echo "<br /><b>&raquo;</b> <b>$insert_polla_count / 5</b> Poll Answers Inserted.</p>";
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
					if($create_table_count == 3 && $insert_pollq_count == 1 && $insert_polla_count == 5 && $insert_options_count == 16) {
						echo '<p align="center"><b>'.__('WP-Polls 2.03 Installed Successfully.').'</b><br />'.__('Please remember to delete this file before proceeding on.').'</p>';
					}
				} else {
		?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<div align="center"><input type="submit" name="install" value="<?php _e('Click Here To Install WP-Polls 2.03'); ?>" class="button"></div>
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