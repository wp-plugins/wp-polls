<?php
/*
 * Polls Plugin For WordPress
 *	- polls-install.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


// Require WordPress Config
require_once('../wp-config.php');

// Create Polls Answers Table
$sql[] = "CREATE TABLE $wpdb->pollsa (".
" aid int(10) unsigned NOT NULL auto_increment,".
" qid int(10) NOT NULL default '0',".
" answers varchar(200) NOT NULL default '',".
" votes int(10) NOT NULL default '0',".
" PRIMARY KEY (aid))";

// Create Polls Question Table
$sql[] = "CREATE TABLE $wpdb->pollsq (".
" id int(10) unsigned NOT NULL auto_increment,".
" question varchar(200) NOT NULL default '',".
" timestamp varchar(20) NOT NULL default '',".
" total_votes int(10) NOT NULL default '0',".
" PRIMARY KEY (id)) ";

// Add In Poll Question/Answers
$sql[] = "INSERT INTO $wpdb->pollsq VALUES (1, 'How Is My Site?', '".time()."', 0);";
$sql[] = "INSERT INTO $wpdb->pollsa VALUES (1, 1, 'Good', 0);";
$sql[] = "INSERT INTO $wpdb->pollsa VALUES (2, 1, 'Excellent', 0);";
$sql[] = "INSERT INTO $wpdb->pollsa VALUES (3, 1, 'Bad', 0);";
$sql[] = "INSERT INTO $wpdb->pollsa VALUES (4, 1, 'Can Be Improved', 0);";
$sql[] = "INSERT INTO $wpdb->pollsa VALUES (5, 1, 'No Comments', 0);";

// Run The Queries
foreach($sql as $query) {
	$wpdb->query($query);
}
?>