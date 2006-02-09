<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Polls 2.05										|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Poll Archive																		|
|	- wp-polls.php																		|
|																							|
+----------------------------------------------------------------+
*/


### Wordpress Header
require(dirname(__FILE__).'/wp-blog-header.php');

### Function: Poll Page Title
add_filter('wp_title', 'poll_pagetitle');
function poll_pagetitle($poll_pagetitle) {
	return $poll_pagetitle.' &raquo; Polls';
}

### Polls Variables
$page = intval($_GET['page']);
$polls_questions = array();
$polls_answers = array();
$polls_ip = array();
$polls_perpage = intval(get_settings('poll_archive_perpage'));
$poll_questions_ids = '0';
$poll_voted = false;
$poll_voted_aid = 0;
$poll_id = 0;

### Get Total Polls
$total_polls = $wpdb->get_var("SELECT COUNT(pollq_id) FROM $wpdb->pollsq");

### Checking $page and $offset
if (empty($page) || $page == 0) { $page = 1; }
if (empty($offset)) { $offset = 0; }

### Determin $offset
$offset = ($page-1) * $polls_perpage;

### Determine Max Number Of Polls To Display On Page
if(($offset + $polls_perpage) > $total_polls) { 
	$max_on_page = $total_polls; 
} else { 
	$max_on_page = ($offset + $polls_perpage); 
}

### Determine Number Of Polls To Display On Page
if (($offset + 1) > ($total_polls)) { 
	$display_on_page = $total_polls; 
} else { 
	$display_on_page = ($offset + 1); 
}

### Determing Total Amount Of Pages
$total_pages = ceil($total_polls / $polls_perpage);

### Make Sure Poll Is Not Disabled
if(intval(get_settings('poll_currentpoll')) != -1 && $page < 2) {
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

### Get Poll Questions
$questions = $wpdb->get_results("SELECT * FROM $wpdb->pollsq WHERE pollq_id != $poll_id ORDER BY pollq_id DESC LIMIT $offset, $polls_perpage");
if($questions) {
	foreach($questions as $question) {
		$polls_questions[] = array('id' => intval($question->pollq_id), 'question' => stripslashes($question->pollq_question), 'timestamp' => $question->pollq_timestamp, 'totalvotes' => intval($question->pollq_totalvotes));
		$poll_questions_ids .= intval($question->pollq_id).', ';
	}
	$poll_questions_ids = substr($poll_questions_ids, 0, -2);
}

### Get Poll Answers
$answers = $wpdb->get_results("SELECT polla_aid, polla_qid, polla_answers, polla_votes FROM $wpdb->pollsa WHERE polla_qid IN ($poll_questions_ids) ORDER BY ".get_settings('poll_ans_result_sortby').' '.get_settings('poll_ans_result_sortorder'));
if($answers) {
	foreach($answers as $answer) {
		$polls_answers[] = array('aid' => intval($answer->polla_aid), 'qid' => intval($answer->polla_qid), 'answers' => stripslashes($answer->polla_answers), 'votes' => intval($answer->polla_votes));
	}
}

### Get Poll IPs
$ips = $wpdb->get_results("SELECT pollip_qid, pollip_aid FROM $wpdb->pollsip WHERE pollip_qid IN ($poll_questions_ids) AND pollip_ip = '".get_ipaddress()."'");
if($ips) {
	foreach($ips as $ip) {
		$polls_ips[] = array('qid' => intval($ip->pollip_qid), 'aid' => intval($ip->pollip_aid));
	}
}
### Function: Check Voted To Get Voted Answer
function check_voted($poll_id) {
	global $polls_ips;
	$temp_voted_aid = 0;
	if(intval($_COOKIE["voted_$poll_id"]) > 0) {
		$temp_voted_aid = intval($_COOKIE["voted_$poll_id"]);
	} else {
		if($polls_ips) {
			foreach($polls_ips as $polls_ip) {
				if($polls_ip['qid'] == $poll_id) {
					$temp_voted_aid = $polls_ip['aid'];
				}
			}
		}
	}
	return $temp_voted_aid;
}
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
		<?php
			if($page < 2) {
				echo "<!-- <Currrent Poll> -->\n";
				echo '<h2 class="pagetitle">'.__('Current Poll').'</h2>'."\n";
				// Current Poll
				if(intval(get_settings('poll_currentpoll')) == -1) {
					echo get_settings('poll_template_disable');
				} else {
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
				echo "<!-- </Currrent Poll> -->\n";
			}
		?>
		<!-- <Poll Archives> -->
		<h2 class="pagetitle"><?php _e('Polls Archive'); ?></h2>
			<?php
				foreach($polls_questions as $polls_question) {
					// Is The Poll Total Votes 0?
					$poll_totalvotes_zero = true;
					if($polls_question['totalvotes'] > 0) {
						$poll_totalvotes_zero = false;
					}
					// Poll Question Variables
					$template_question = stripslashes(get_settings('poll_template_resultheader'));
					$template_question = str_replace("%POLL_QUESTION%", $polls_question['question'], $template_question);
					$template_question = str_replace("%POLL_ID%", $polls_question['id'], $template_question);
					$template_question = str_replace("%POLL_TOTALVOTES%", $polls_question['totalvotes'], $template_question);
					// Print Out Result Header Template
					echo $template_question;
					foreach($polls_answers as $polls_answer) {
						if($polls_question['id'] == $polls_answer['qid']) {
							// Calculate Percentage And Image Bar Width
							if(!$poll_totalvotes_zero) {
								if($polls_answer['votes'] > 0) {
									$poll_answer_percentage = round((($polls_answer['votes']/$polls_question['totalvotes'])*100));
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
							if(check_voted($polls_question['id']) == $polls_answer['aid']) {				
								// Results Body Variables
								$template_answer = stripslashes(get_settings('poll_template_resultbody2'));
								$template_answer = str_replace("%POLL_ANSWER_ID%", $polls_answer['aid'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER%", $polls_answer['answers'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_VOTES%", $polls_answer['votes'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
								// Print Out Results Body Template
								echo $template_answer;
							} else {
								// Results Body Variables
								$template_answer = stripslashes(get_settings('poll_template_resultbody'));
								$template_answer = str_replace("%POLL_ANSWER_ID%", $polls_answer['aid'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER%", $polls_answer['answers'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_VOTES%", $polls_answer['votes'], $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_PERCENTAGE%", $poll_answer_percentage, $template_answer);
								$template_answer = str_replace("%POLL_ANSWER_IMAGEWIDTH%", $poll_answer_imagewidth, $template_answer);
								// Print Out Results Body Template
								echo $template_answer;
							}
							// Delete Away From Array
							unset($polls_answer['answers']);
						}
					}
					// Results Footer Variables
					$template_footer = stripslashes(get_settings('poll_template_resultfooter'));
					$template_footer = str_replace("%POLL_TOTALVOTES%", $polls_question['totalvotes'], $template_footer);
					// Print Out Results Footer Template
					echo $template_footer;
					echo "<br /><hr class=\"Divider\" />\n";
				}
			?>
		<!-- </Poll Archives> -->

		<!-- <Paging> -->
		<?php
			if($total_polls > 0) {
		?>
		<br />
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td align="left" width="50%">
					<?php
						if($page > 1 && ((($page*$polls_perpage)-($polls_perpage-1)) <= $total_polls)) {
							echo '<b>&laquo;</b> <a href="wp-polls.php?page='.($page-1).'" title="&laquo; '.__('Previous Page').'">'.__('Previous Page').'</a>';
						} else {
							echo '&nbsp;';
						}
					?>
				</td>
				<td align="right" width="50%">
					<?php
						if($page >= 1 && ((($page*$polls_perpage)+1) <=  $total_polls)) {
							echo '<a href="wp-polls.php?page='.($page+1).'" title="'.__('Next Page').' &raquo;">'.__('Next Page').'</a> <b>&raquo;</b>';
						} else {
							echo '&nbsp;';
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<?php _e('Pages'); ?> (<?echo $total_pages; ?>) :
					<?php
						if ($page >= 4) {
							echo '<b><a href="wp-polls.php?page=1" title="'.__('Go to First Page').'">&laquo; '.__('First').'</a></b> ... ';
						}
						if($page > 1) {
							echo ' <b><a href="wp-polls.php?page='.($page-1).'" title="&laquo; '.__('Go to Page').' '.($page-1).'">&laquo;</a></b> ';
						}
						for($i = $page - 2 ; $i  <= $page +2; $i++) {
							if ($i >= 1 && $i <= $total_pages) {
								if($i == $page) {
									echo "<b>[$i]</b> ";
								} else {
									echo '<a href="wp-polls.php?page='.($i).'" title="'.__('Page').' '.$i.'">'.$i.'</a> ';
								}
							}
						}
						if($page < $total_pages) {
							echo ' <b><a href="wp-polls.php?page='.($page+1).'" title="'.__('Go to Page').' '.($page+1).' &raquo;">&raquo;</a></b> ';
						}
						if (($page+2) < $total_pages) {
							echo ' ... <b><a href="wp-polls.php?page='.($total_pages).'" title="'.__('Go to Last Page').'">'.__('Last').' &raquo;</a></b>';
						}
					?>
				</td>
			</tr>
		</table>	
		<!-- </Paging> -->
		<?php
			}
		?>	
	</div>
<?php 		
	get_sidebar();
	get_footer();
?>