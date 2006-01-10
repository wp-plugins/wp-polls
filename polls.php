<?php
/*
Plugin Name: WP-Polls
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds A Poll Feature To WordPress
Version: 2.04
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


### Function: Poll Menu
add_action('admin_menu', 'poll_menu');
function poll_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page('Polls', 'Polls', 1, 'polls-manager.php');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('polls-manager.php', __('Manage Polls'), __('Manage Polls'), 1, 'polls-manager.php');
		add_submenu_page('polls-manager.php', __('Poll Option'), __('Poll Option'), 1, 'polls-options.php');
	}
}


### Function: Get Poll
function get_poll($temp_poll_id = 0) {
	global $wpdb;
	// Check Whether Poll Is Disabled
	if(intval(get_settings('poll_currentpoll')) == -1) {
		echo stripslashes(get_settings('poll_template_disable'));
		return;
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
	if(intval($_GET['pollresult']) == 1) {
		display_pollresult($poll_id);
	// Check Whether User Has Voted
	} else {
		// Check Cookie First
		$voted_cookie = check_voted_cookie($poll_id);
		if($voted_cookie > 0) {
			display_pollresult($poll_id, $voted_cookie);
		// Check IP If Cookie Cannot Be Found
		} else {
			$voted_ip = check_voted_ip($poll_id);
			if($voted_ip > 0) {
				display_pollresult($poll_id, $voted_ip);
			// User Never Vote. Display Poll Voting Form
			} else {
				display_pollvote($poll_id);
			}
		}
	}	
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
		echo '<form action="'.$_SERVER['REQUEST_URI'].'" name="polls" method="post">'."\n";
		echo "<input type=\"hidden\" name=\"poll_id\" value=\"$poll_question_id\" />\n";
		// Print Out Voting Form Header Template
		echo $template_question;
		foreach($poll_answers as $poll_answer) {
			// Poll Answer Variables
			$poll_answer_id = intval($poll_answer->polla_aid); 
			$poll_answer_text = stripslashes($poll_answer->polla_answers);
			$poll_answer_votes = intval($poll_answer->polla_votes);
			$template_answer = stripslashes(get_settings('poll_template_votebody'));
			$template_answer = str_replace("%POLL_ID%", $poll_question_id, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER_ID%", $poll_answer_id, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER%", $poll_answer_text, $template_answer);
			$template_answer = str_replace("%POLL_ANSWER_VOTES%", $poll_answer_votes, $template_answer);
			// Print Out Voting Form Body Template
			echo $template_answer;
		}
		// Determine Poll Result URL
		$poll_result_url = $_SERVER['REQUEST_URI'];
		if(strpos($poll_result_url, '?') !== false) {
			$poll_result_url = $poll_result_url.'&pollresult=1';
		} else {
			$poll_result_url = $poll_result_url.'?pollresult=1';
		}
		// Voting Form Footer Variables
		$template_footer = stripslashes(get_settings('poll_template_votefooter'));
		$template_footer = str_replace("%POLL_RESULT_URL%", $poll_result_url, $template_footer);
		// Print Out Voting Form Footer Template
		echo $template_footer;
		echo "</form>\n";
	} else {
		echo stripslashes(get_settings('poll_template_disable'));
	}
}


### Function: Display Results Form
function display_pollresult($poll_id, $user_voted = 0) {
	global $wpdb;
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
		echo $template_question;
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
					$poll_answer_imagewidth = round($poll_answer_percentage*0.9);
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
				$template_answer = str_replace("%POLL_ANSWER_VOTES%", $poll_answer_votes, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
				// Print Out Results Body Template
				echo $template_answer;
			} else {
				// Results Body Variables
				$template_answer = stripslashes(get_settings('poll_template_resultbody'));
				$template_answer = str_replace("%POLL_ANSWER_ID%", $poll_answer_id, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER%", $poll_answer_text, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_VOTES%", $poll_answer_votes, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
				$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
				// Print Out Results Body Template
				echo $template_answer;
			}
		}
		// Results Footer Variables
		$template_footer = stripslashes(get_settings('poll_template_resultfooter'));
		$template_footer = str_replace("%POLL_TOTALVOTES%", $poll_question_totalvotes, $template_footer);
		// Print Out Results Footer Template
		echo $template_footer;
	} else {
		echo stripslashes(get_settings('poll_template_disable'));
	}
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


### Function: Place Poll In Content (By: Robert Accettura Of http://robert.accettura.com/)
add_filter('the_content', 'place_poll', '12');
function place_poll($content){
     $content = preg_replace( "/\[poll=(\d+)\]/ise", "display_poll('\\1')", $content); 
    return $content;
}


### Function: Display The Poll In Content (By: Robert Accettura Of http://robert.accettura.com/)
function display_poll($poll_id, $display_pollarchive = true){
	if (function_exists('vote_poll')){
		if($display_pollarchive) {
			return get_poll($poll_id)."\n".'<p><a href="'.get_settings('home').'/wp-polls.php">Polls Archive</a></p>';
		} else {
			return get_poll($poll_id);
		}
	}
}
?>