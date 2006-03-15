<?php
/*
Plugin Name: WP-Polls
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds A Poll Feature To WordPress
Version: 2.06
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


/*  Copyright 2005  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Polls Table Name
$wpdb->pollsq					= $table_prefix . 'pollsq';
$wpdb->pollsa					= $table_prefix . 'pollsa';
$wpdb->pollsip					= $table_prefix . 'pollsip';


### Function: Poll Administration Menu
add_action('admin_menu', 'poll_menu');
function poll_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page(__('Polls'), __('Polls'), 'manage_polls', 'polls/polls-manager.php');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('polls/polls-manager.php', __('Manage Polls'), __('Manage Polls'), 'manage_polls', 'polls/polls-manager.php');
		add_submenu_page('polls/polls-manager.php', __('Poll Options'), __('Poll Options'), 'manage_polls', 'polls/polls-options.php');
	}
}


### Function: Get Poll
function get_poll($temp_poll_id = 0, $display = true) {
	global $wpdb;
	// Check Whether Poll Is Disabled
	if(intval(get_settings('poll_currentpoll')) == -1) {
		if($display) {
			echo stripslashes(get_settings('poll_template_disable'));
			return;
		} else {
			return stripslashes(get_settings('poll_template_disable'));
		}		
	// Poll Is Enabled
	} else {
		// Hardcoded Poll ID Is Not Specified
		if(intval($temp_poll_id) == 0) {
			// Current Poll ID Is Not Specified
			if(intval(get_settings('poll_currentpoll')) == 0) {
				// Get Lastest Poll ID
				$poll_id = intval(get_settings('poll_latestpoll'));
			} else {
				// Get Current Poll ID
				$poll_id = intval(get_settings('poll_currentpoll'));
			}
		// Get Hardcoded Poll ID
		} else {
			$poll_id = intval($temp_poll_id);
		}
	}

	// User Click on View Results Link
	$pollresult_id = intval($_GET['pollresult']);
	if($pollresult_id == $poll_id) {
		if($display) {
			echo display_pollresult($poll_id);
			return;
		} else {
			return display_pollresult($poll_id);
		}
	// Check Whether User Has Voted
	} else {
		// Check Cookie First
		$voted_cookie = check_voted_cookie($poll_id);
		if($voted_cookie > 0) {
			if($display) {
				echo display_pollresult($poll_id, $voted_cookie);
				return;
			} else {
				return display_pollresult($poll_id, $voted_cookie);
			}
		// Check IP If Cookie Cannot Be Found
		} else {
			$voted_ip = check_voted_ip($poll_id);
			if($voted_ip > 0) {
				if($display) {
					echo display_pollresult($poll_id, $voted_ip);
					return;
				} else {
					return display_pollresult($poll_id, $voted_ip);
				}
			// User Never Vote. Display Poll Voting Form
			} else {
				if($display) {
					echo display_pollvote($poll_id);
					return;
				} else {
					return display_pollvote($poll_id);
				}
			}
		}
	}	
}


### Function: Displays Polls CSS
add_action('wp_head', 'poll_css');
function poll_css() {
	echo '<style type="text/css" media="screen">'."\n";
	echo "\t".'.wp-polls ul li {'."\n";
	echo "\t\t".'text-align: left;'."\n";
	echo "\t\t".'list-style: none;'."\n";
	echo "\t".'}'."\n";
	echo "\t".'.wp-polls ul li:before, #sidebar ul ul ul li:before {'."\n";
	echo "\t\t".'content: \'\';'."\n";
	echo "\t".'}'."\n";
	echo '</style>'."\n";
}


### Function: Check Voted By Cookie
function check_voted_cookie($poll_id) {
	// 0: False | > 0: True
	return intval($_COOKIE["voted_$poll_id"]);
}


### Function: Check Voted By IP
function check_voted_ip($poll_id) {
	global $wpdb;
	// Check IP From IP Logging Database
	$get_voted_aid = $wpdb->get_var("SELECT pollip_aid FROM $wpdb->pollsip WHERE pollip_qid = $poll_id AND pollip_ip = '".get_ipaddress()."'");
	// 0: False | > 0: True
	return intval($get_voted_aid);
}


### Function: Display Voting Form
function display_pollvote($poll_id) {
	global $wpdb;
	// Temp Poll Result
	$temp_pollvote = '';
	// Get Poll Question Data
	$poll_question = $wpdb->get_row("SELECT pollq_id, pollq_question, pollq_totalvotes FROM $wpdb->pollsq WHERE pollq_id = $poll_id LIMIT 1");
	// Poll Question Variables
	$poll_question_text = stripslashes($poll_question->pollq_question);
	$poll_question_id = intval($poll_question->pollq_id);
	$poll_question_totalvotes = intval($poll_question->pollq_totalvotes);
	$template_question = stripslashes(get_settings('poll_template_voteheader'));
	$template_question = str_replace("%POLL_QUESTION%", $poll_question_text, $template_question);
	$template_question = str_replace("%POLL_ID%", $poll_question_id, $template_question);
	$template_question = str_replace("%POLL_TOTALVOTES%", $poll_question_totalvotes, $template_question);
	// Get Poll Answers Data
	$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_question_id ORDER BY ".get_settings('poll_ans_sortby').' '.get_settings('poll_ans_sortorder'));
	// If There Is Poll Question With Answers
	if($poll_question && $poll_answers) {
		// Display Poll Voting Form
		$temp_pollvote .= "<div id=\"wp-polls-$poll_question_id\" class=\"wp-polls\">\n";
		$temp_pollvote .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">'."\n";
		$temp_pollvote .= "<input type=\"hidden\" name=\"poll_id\" value=\"$poll_question_id\" />\n";
		// Print Out Voting Form Header Template
		$temp_pollvote .= $template_question;
		foreach($poll_answers as $poll_answer) {
			// Poll Answer Variables
			$poll_answer_id = intval($poll_answer->polla_aid); 
			$poll_answer_text = stripslashes($poll_answer->polla_answers);
			$poll_answer_votes = intval($poll_answer->polla_votes);
			$template_answer = stripslashes(get_settings('poll_template_votebody'));
			$template_answer = str_replace("%POLL_ID%", $poll_question_id, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER_ID%", $poll_answer_id, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER%", $poll_answer_text, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER_VOTES%", number_format($poll_answer_votes), $template_answer);
			// Print Out Voting Form Body Template
			$temp_pollvote .= $template_answer;
		}
		// Determine Poll Result URL
		$poll_result_url = $_SERVER['REQUEST_URI'];
		$poll_result_url = preg_replace('/pollresult=(\d+)/i', 'pollresult='.$poll_question_id, $poll_result_url);
		if(intval($_GET['pollresult']) == 0) {
			if(strpos($poll_result_url, '?') !== false) {
				$poll_result_url = "$poll_result_url&amp;pollresult=$poll_question_id";
			} else {
				$poll_result_url = "$poll_result_url?pollresult=$poll_question_id";
			}
		}
		// Voting Form Footer Variables
		$template_footer = stripslashes(get_settings('poll_template_votefooter'));
		$template_footer = str_replace("%POLL_RESULT_URL%", $poll_result_url, $template_footer);
		// Print Out Voting Form Footer Template
		$temp_pollvote .= $template_footer;
		$temp_pollvote .= "</form>\n";
		$temp_pollvote .= "</div>\n";
	} else {
		$temp_pollvote .= stripslashes(get_settings('poll_template_disable'));
	}
	// Return Poll Vote Template
	return $temp_pollvote;
}


### Function: Display Results Form
function display_pollresult($poll_id, $user_voted = 0) {
	global $wpdb;
	// Temp Poll Result
	$temp_pollresult = '';	
	// Most/Least Variables
	$poll_most_answer = '';
	$poll_most_votes = 0;
	$poll_most_percentage = 0;
	$poll_least_answer = '';
	$poll_least_votes = 0;
	$poll_least_percentage = 0;
	// Get Poll Question Data
	$poll_question = $wpdb->get_row("SELECT pollq_id, pollq_question, pollq_totalvotes FROM $wpdb->pollsq WHERE pollq_id = $poll_id LIMIT 1");
	// Poll Question Variables
	$poll_question_text = stripslashes($poll_question->pollq_question);
	$poll_question_id = intval($poll_question->pollq_id);
	$poll_question_totalvotes = intval($poll_question->pollq_totalvotes);
	$template_question = stripslashes(get_settings('poll_template_resultheader'));
	$template_question = str_replace("%POLL_QUESTION%", $poll_question_text, $template_question);
	$template_question = str_replace("%POLL_ID%", $poll_question_id, $template_question);
	$template_question = str_replace("%POLL_TOTALVOTES%", $poll_question_totalvotes, $template_question);
	// Get Poll Answers Data
	$poll_answers = $wpdb->get_results("SELECT polla_aid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid = $poll_question_id ORDER BY ".get_settings('poll_ans_result_sortby').' '.get_settings('poll_ans_result_sortorder'));
	// If There Is Poll Question With Answers
	if($poll_question && $poll_answers) {
		// Is The Poll Total Votes 0?
		$poll_totalvotes_zero = true;
		if($poll_question_totalvotes > 0) {
			$poll_totalvotes_zero = false;
		}
		// Print Out Result Header Template
		$temp_pollresult .= "<div id=\"wp-polls-$poll_question_id\" class=\"wp-polls\">\n";
		$temp_pollresult .= $template_question;
		foreach($poll_answers as $poll_answer) {
			// Poll Answer Variables
			$poll_answer_id = intval($poll_answer->polla_aid); 
			$poll_answer_text = stripslashes($poll_answer->polla_answers);
			$poll_answer_votes = intval($poll_answer->polla_votes);
			$poll_answer_text = stripslashes($poll_answer->polla_answers);
			$poll_answer_percentage = 0;
			$poll_answer_imagewidth = 0;
			// Calculate Percentage And Image Bar Width
			if(!$poll_totalvotes_zero) {
				if($poll_answer_votes > 0) {
					$poll_answer_percentage = round((($poll_answer_votes/$poll_question_totalvotes)*100));
					$poll_answer_imagewidth = round($poll_answer_percentage);
				} else {
					$poll_answer_percentage = 0;
					$poll_answer_imagewidth = 1;
				}
			} else {
				$poll_answer_percentage = 0;
				$poll_answer_imagewidth = 1;
			}
			// Let User See What Options They Voted
			if($user_voted == $poll_answer_id) {
				// Results Body Variables
				$template_answer = stripslashes(get_settings('poll_template_resultbody2'));
				$template_answer = str_replace("%POLL_ANSWER_ID%", $poll_answer_id, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER%", $poll_answer_text, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_VOTES%", number_format($poll_answer_votes), $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
				// Print Out Results Body Template
				$temp_pollresult .= $template_answer;
			} else {
				// Results Body Variables
				$template_answer = stripslashes(get_settings('poll_template_resultbody'));
				$template_answer = str_replace("%POLL_ANSWER_ID%", $poll_answer_id, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER%", $poll_answer_text, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_VOTES%", number_format($poll_answer_votes), $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
				// Print Out Results Body Template
				$temp_pollresult .= $template_answer;
			}
			// Get Most Voted Data
			if($poll_answer_votes > $poll_most_votes) {
				$poll_most_answer = $poll_answer_text;
				$poll_most_votes = $poll_answer_votes;
				$poll_most_percentage = $poll_answer_percentage;
			}
			// Get Least Voted Data
			if($poll_least_votes == 0) {
				$poll_least_votes = $poll_answer_votes;
			}
			if($poll_answer_votes <= $poll_least_votes) {
				$poll_least_answer = $poll_answer_text;
				$poll_least_votes = $poll_answer_votes;
				$poll_least_percentage = $poll_answer_percentage;
			}
		}
		// Results Footer Variables
		$template_footer = stripslashes(get_settings('poll_template_resultfooter'));
		$template_footer = str_replace("%POLL_TOTALVOTES%", number_format($poll_question_totalvotes), $template_footer);
		$template_footer = str_replace("%POLL_MOST_ANSWER%", $poll_most_answer, $template_footer);
		$template_footer = str_replace("%POLL_MOST_VOTES%", number_format($poll_most_votes), $template_footer);
		$template_footer = str_replace("%POLL_MOST_PERCENTAGE%", $poll_most_percentage, $template_footer);
		$template_footer = str_replace("%POLL_LEAST_ANSWER%", $poll_least_answer, $template_footer);
		$template_footer = str_replace("%POLL_LEAST_VOTES%", number_format($poll_least_votes), $template_footer);
		$template_footer = str_replace("%POLL_LEAST_PERCENTAGE%", $poll_least_percentage, $template_footer);
		// Print Out Results Footer Template
		$temp_pollresult .= $template_footer;
		$temp_pollresult .= "</div>\n";
	} else {
		$temp_pollresult .= stripslashes(get_settings('poll_template_disable'));
	}	
	// Return Poll Result
	return $temp_pollresult;
}


### Function: Vote Poll
add_action('init', 'vote_poll');
function vote_poll() {
	global $wpdb, $user_identity;
	if(!empty($_POST['vote'])) {
		$poll_id = intval($_POST['poll_id']);
		$poll_aid = intval($_POST["poll-$poll_id"]);
		if($poll_id > 0 && $poll_aid > 0) {
			$voted_ip = check_voted_ip($poll_id);
			$voted_cookie = check_voted_cookie($poll_ip);
			if($voted_ip == 0 && $voted_cookie == 0) {
				if(!empty($user_identity)) {
					$pollip_user = addslashes($user_identity);
				} elseif(!empty($_COOKIE['comment_author_'.COOKIEHASH])) {
					$pollip_user = addslashes($_COOKIE['comment_author_'.COOKIEHASH]);
				} else {
					$pollip_user = 'Guest';
				}
				$vote_cookie = setcookie("voted_".$poll_id, $poll_aid, time() + 30000000, COOKIEPATH);
				if($vote_cookie) {
					$pollip_ip = get_ipaddress();
					$pollip_host = gethostbyaddr($pollip_ip);
					$pollip_timestamp = current_time('timestamp');
					$vote_ip = $wpdb->query("INSERT INTO $wpdb->pollsip VALUES(0,$poll_id,$poll_aid,'$pollip_ip','$pollip_host','$pollip_timestamp','$pollip_user')");
					if($vote_ip) {
						$vote_a = $wpdb->query("UPDATE $wpdb->pollsa SET polla_votes = (polla_votes+1) WHERE polla_qid = $poll_id AND polla_aid = $poll_aid");
						if($vote_a) {
							$vote_q = $wpdb->query("UPDATE $wpdb->pollsq SET pollq_totalvotes = (pollq_totalvotes+1) WHERE pollq_id = $poll_id");
						} // End if($vote_a)
					} // End if($vote_ip)
				} // End if($vote_cookie)
			}// End if($voted_ip == 0 && $voted_cookie == 0)
		} // End if(!empty($_POST['vote']))
	} // End if($poll_id > 0 && $poll_aid > 0)
}


### Function: Get IP Address
if(!function_exists('get_ipaddress')) {
	function get_ipaddress() {
		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if(strpos($ip_address, ',') !== false) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return $ip_address;
	}
}


### Function: Place Poll In Content (By: Robert Accettura Of http://robert.accettura.com/)
add_filter('the_content', 'place_poll', '12');
function place_poll($content){
     $content = preg_replace( "/\[poll=(\d+)\]/ise", "display_poll('\\1')", $content); 
    return $content;
}


### Function: Display The Poll In Content (By: Robert Accettura Of http://robert.accettura.com/)
function display_poll($poll_id, $display_pollarchive = false){
	if (function_exists('vote_poll')){
		if($display_pollarchive) {
			return get_poll($poll_id, false)."\n".'<p><a href="'.get_settings('home').'/wp-polls.php">Polls Archive</a></p>';
		} else {
			return get_poll($poll_id, false);
		}
	}
}


### Function: Get Poll Total Questions
if(!function_exists('get_pollquestions')) {
	function get_pollquestions() {
		global $wpdb;
		$totalpollq = $wpdb->get_var("SELECT COUNT(pollq_id) FROM $wpdb->pollsq");
		echo $totalpollq;
	}
}


### Function: Get Poll Total Answers
if(!function_exists('get_pollanswers')) {
	function get_pollanswers() {
		global $wpdb;
		$totalpolla = $wpdb->get_var("SELECT COUNT(polla_aid) FROM $wpdb->pollsa");
		echo $totalpolla;
	}
}


### Function: Get Poll Total Votes
if(!function_exists('get_pollvotes')) {
	function get_pollvotes() {
		global $wpdb;
		$totalpollip = $wpdb->get_var("SELECT COUNT(pollip_id) FROM $wpdb->pollsip");
		echo $totalpollip;
	}
}


### Function: Create Poll Tables
add_action('activate_polls/polls.php', 'create_poll_table');
function create_poll_table() {
	global $wpdb;
	include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	// Create Poll Tables (3 Tables)
	$create_table = array();
	$create_table['pollsq'] = "CREATE TABLE $wpdb->pollsq (".
									"pollq_id int(10) NOT NULL auto_increment,".
									"pollq_question varchar(200) NOT NULL default '',".
									"pollq_timestamp varchar(20) NOT NULL default '',".
									"pollq_totalvotes int(10) NOT NULL default '0',".
									"PRIMARY KEY (pollq_id))";
	$create_table['pollsa'] = "CREATE TABLE $wpdb->pollsa (".
									"polla_aid int(10) NOT NULL auto_increment,".
									"polla_qid int(10) NOT NULL default '0',".
									"polla_answers varchar(200) NOT NULL default '',".
									"polla_votes int(10) NOT NULL default '0',".
									"PRIMARY KEY (polla_aid))";
	$create_table['pollsip'] = "CREATE TABLE $wpdb->pollsip (".
									"pollip_id int(10) NOT NULL auto_increment,".
									"pollip_qid varchar(10) NOT NULL default '',".
									"pollip_aid varchar(10) NOT NULL default '',".
									"pollip_ip varchar(100) NOT NULL default '',".
									"pollip_host VARCHAR(200) NOT NULL default '',".
									"pollip_timestamp varchar(20) NOT NULL default '0000-00-00 00:00:00',".
									"pollip_user tinytext NOT NULL,".
									"PRIMARY KEY (pollip_id))";
	maybe_create_table($wpdb->pollsq, $create_table['pollsq']);
	maybe_create_table($wpdb->pollsa, $create_table['pollsa']);
	maybe_create_table($wpdb->pollsip, $create_table['pollsip']);
	// Check Whether It is Install Or Upgrade
	$first_poll = $wpdb->get_var("SELECT pollq_id FROM $wpdb->pollsq LIMIT 1");
	// If Install, Insert 1st Poll Question With 5 Poll Answers
	if(empty($first_poll)) {
		// Insert Poll Question (1 Record)
		$insert_pollq = $wpdb->query("INSERT INTO $wpdb->pollsq VALUES (1, 'How Is My Site?', '".current_time('timestamp')."', 0);");
		if($insert_pollq) {
			// Insert Poll Answers  (5 Records)
			$wpdb->query("INSERT INTO $wpdb->pollsa VALUES (1, 1, 'Good', 0);");
			$wpdb->query("INSERT INTO $wpdb->pollsa VALUES (2, 1, 'Excellent', 0);");
			$wpdb->query("INSERT INTO $wpdb->pollsa VALUES (3, 1, 'Bad', 0);");
			$wpdb->query("INSERT INTO $wpdb->pollsa VALUES (4, 1, 'Can Be Improved', 0);");
			$wpdb->query("INSERT INTO $wpdb->pollsa VALUES (5, 1, 'No Comments', 0);");
		}
	}
	// Add In Options (16 Records)
	add_option('poll_template_voteheader', '<p align="center"><b>%POLL_QUESTION%</b></p>'.
	'<ul>', 'Template For Poll\'s Question');
	add_option('poll_template_votebody',  '<li><label for="poll-answer-%POLL_ANSWER_ID%"><input type="radio" id="poll-answer-%POLL_ANSWER_ID%" name="poll-%POLL_ID%" value="%POLL_ANSWER_ID%" /> %POLL_ANSWER%</label></li>', 'Template For Poll\'s Answers');
	add_option('poll_template_votefooter', '</ul>'.
	'<p align="center"><input type="submit" name="vote" value="   Vote   " class="Buttons" /><br /><a href="%POLL_RESULT_URL%">View Results</a></p>', 'Template For Poll\'s Voting Footer');
	add_option('poll_template_resultheader', '<p align="center"><b>%POLL_QUESTION%</b></p>'.
	'<ul>', 'Template For Poll Header');
	add_option('poll_template_resultbody', '<li>%POLL_ANSWER% <small>(%POLL_ANSWER_PERCENTAGE%%)</small><br /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollstart.gif" height="10" width="2" /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollbar.gif" height="10" width="%POLL_ANSWER_IMAGEWIDTH%" alt="%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)" title="%POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)" /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollend.gif" height="10" width="2" /></li>', 'Template For Poll Results');
	add_option('poll_template_resultbody2', '<li><b><i>%POLL_ANSWER% <small>(%POLL_ANSWER_PERCENTAGE%%)</small></i></b><br /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollstart.gif" height="10" width="2" /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollbar.gif" height="10" width="%POLL_ANSWER_IMAGEWIDTH%" alt="You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)" title="You Have Voted For This Choice  - %POLL_ANSWER% -> %POLL_ANSWER_PERCENTAGE%% (%POLL_ANSWER_VOTES% Votes)" /><img src="'.get_settings('siteurl').'/wp-content/plugins/polls/images/pollend.gif" height="10" width="2" /></li>', 'Template For Poll Results (User Voted)');
	add_option('poll_template_resultfooter', '</ul>'.
	'<p align="center">Total Votes: <b>%POLL_TOTALVOTES%</b></p>', 'Template For Poll Result Footer');
	add_option('poll_template_disable', 'Sorry, there are no polls available at the moment.', 'Template For Poll When It Is Disabled');
	add_option('poll_template_error', 'An error has occurred when processing your poll.', 'Template For Poll When An Error Has Occured');
	add_option('poll_currentpoll', 0, 'Current Displayed Poll');
	add_option('poll_latestpoll', 1, 'The Lastest Poll');
	add_option('poll_archive_perpage', 5, 'Number Of Polls To Display Per Page On The Poll\'s Archive', 'no');
	add_option('poll_ans_sortby', 'polla_aid', 'Sorting Of Poll\'s Answers');
	add_option('poll_ans_sortorder', 'asc', 'Sort Order Of Poll\'s Answers');
	add_option('poll_ans_result_sortby', 'polla_votes', 'Sorting Of Poll\'s Answers Result');
	add_option('poll_ans_result_sortorder', 'desc', 'Sorting Order Of Poll\'s Answers Result');
	// Set 'manage_polls' Capabilities To Administrator	
	$role = get_role('administrator');
	if(!$role->has_cap('manage_polls')) {
		$role->add_cap('manage_polls');
	}
}
?>