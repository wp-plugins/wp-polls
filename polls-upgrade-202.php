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
|	- Upgrade WP-Polls From 2.0x To 2.04										|
|	- wp-admin/polls-upgrade-202.php											|
|																							|
+----------------------------------------------------------------+
*/


### Require Config
require('../wp-config.php');

### Variables, Variables, Variables
$alter_table = array();
$insert_options = array();
$update_options = array();
$error = '';

### Alter Tables (1 Table)
$alter_table[] = "ALTER TABLE $wpdb->pollsip ADD pollip_host VARCHAR(200) NOT NULL AFTER pollip_ip;";

### Insert Options  (1 Row)
$insert_options[] ="INSERT INTO $wpdb->options VALUES (0, 0, 'poll_template_error', 'Y', '1', 'An error has occurred when processing your poll.', '20', '8', 'Template For Poll When An Error Has Occured', '1', 'yes');";

### Update Options (1 Row)
$update_options[] = "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name = 'poll_archive_perpage'";

### Total IPs Needed To Be Resolved
$ip_totalcount = $wpdb->get_var("SELECT COUNT(pollip_id) FROM $wpdb->pollsip WHERE pollip_ip != ''");

### Check Whether There Is Any Pre Errors
$wpdb->show_errors = false;
$check_upgrade = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'poll_template_error'");
if($check_upgrade) {
	$error = __('You Had Already Installed WP-Polls.');
}
if(empty($wpdb->pollsq) || empty($wpdb->pollsa) || empty($wpdb->pollsip)) {
	$error = __('Please Define The pollsq, pollsa and pollsip tables in wp-settings.php.');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Upgrading'); ?> &rsaquo; <?php _e('WP-Polls 2.04'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css" media="screen">
		@import url( wp-admin.css );
	</style>
</head>
<body>
	<div class="wrap"> 
		<h2><?php _e('Upgrading WP-Polls 2.04'); ?></h2>
		<p><?php _e('This upgrade script will upgrade WP-Polls from version 2.00 or 2.01 to version 2.04 for your Wordpress.'); ?></p>
		<p>
			<?php _e('This upgrade script will be doing the following:'); ?><br />
			<b>&raquo;</b> <b>1</b> <?php _e('table will be altered namely <b>pollsip</b>.'); ?><br />
			<b>&raquo;</b> <b>1</b> <?php _e('option will be inserted into the <b>options</b> table.'); ?><br />
			<b>&raquo;</b> <b>1</b> <?php _e('option will be updated from <b>options</b> table.'); ?><br />
			<b>&raquo;</b> <b><?php echo $ip_totalcount; ?></b> <?php _e('IPs will be resolved and updated.'); ?><br />
			<b>&raquo;</b> <b>4</b> <?php _e('tables will be optimized namely <b>pollsq</b>, <b>pollsa</b>, <b>pollsip</b> and <b>options</b>.'); ?><br />
		</p>
		<?php
			if(empty($error)) {
				if(!empty($_POST['upgrade'])) {
					// Alter Table
					$alter_table_count = 0;
					echo "<p><b>".__('Altering Tables:')."</b>";
					foreach($alter_table as $altertable) {
						$wpdb->query($altertable);
					}
					$check_pollsip = $wpdb->get_var("SELECT pollip_id FROM $wpdb->pollsip WHERE pollip_host = '' LIMIT 1");
					if($check_pollsip) { 
						echo "<br /><b>&raquo;</b> Table (<b>$wpdb->pollsa</b>) altered.";
						$alter_table_count++; 
					} else {
						echo "<br /><b>&raquo;</b> <font color=\"red\">Table (<b>$wpdb->pollsip</b>) table NOT altered.</font>";
					}
					echo "<br /><b>&raquo;</b> <b>$alter_table_count / 1</b> Table Altered.</p>";
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
								$insert_options_count++;
						} else {
							echo "<br /><b>&raquo;</b> <font color=\"red\">Option (<b>$temp_option</b>) NOT inserted.</font>";
						}
					}
					echo "<br /><b>&raquo;</b> <b>$insert_options_count / 1</b> Option Inserted.</p>";
					// Update Options
					$update_options_count = 0;
					echo "<p><b>".__('Updating Options:')."</b>";
					foreach($update_options as $updateoptions) {
						$temp_options = $wpdb->query($updateoptions);
						$temp_option = explode("=", $updateoptions);
						$temp_option = $temp_option[2];
						$temp_option = substr($temp_option, 2, -1);
						if($temp_options) {
								echo "<br /><b>&raquo;</b> Option (<b>$temp_option</b>) updated.";
								$update_options_count++;
						} else {
							echo "<br /><b>&raquo;</b> <font color=\"red\">Option (<b>$temp_option</b>) NOT updated.</font>";
						}
					}
					echo "<br /><b>&raquo;</b> <b>$update_options_count / 1</b> Option Updated.</p>";
					// Resolve IPs
					$ip_count = 0;
					echo "<p><b>".__('Resolving IPs:')."</b>";
					$ips_data = $wpdb->get_results("SELECT pollip_id, pollip_ip FROM $wpdb->pollsip WHERE pollip_ip != ''");
					if($ips_data) {
						foreach($ips_data as $ip_data) {
							$pollip_id = intval($ip_data->pollip_id);
							$pollip_host = gethostbyaddr($ip_data->pollip_ip);
							$update_ip = $wpdb->query("UPDATE $wpdb->pollsip SET pollip_host = '$pollip_host' WHERE pollip_id = $pollip_id");
							if($update_ip) {
								echo "<br /><b>&raquo;</b> IP (<b>$ip_data->pollip_ip</b>) resolved.";
								$ip_count++;
							} else {
								echo "<br /><b>&raquo;</b> <font color=\"red\">IP (<b>$ip_data->pollip_ip</b>) NOT resolved.</font>";
							}
						}
					} else {
						echo "<br /><b>&raquo;</b> There are no IP to be resolved.";
					}
					echo "<br /><b>&raquo;</b> <b>$ip_count / $ip_totalcount</b> IPs Resolved.</p>";
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
					if($alter_table_count == 1 && $insert_options_count == 1) {
						echo '<p align="center"><b>'.__('WP-Polls Upgraded Successfully To Version 2.04.').'</b><br />'.__('Please remember to delete this file before proceeding on.').'</p>';
					}
				} else {
		?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<div align="center"><?php _e('It may take some time for all the ips to be resolved.'); ?><br /><input type="submit" name="upgrade" value="<?php _e('Click Here To Upgrade WP-Polls 2.04'); ?>" class="button"></div>
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