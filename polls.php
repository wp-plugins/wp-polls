<?php
/*
Plugin Name: Polls
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds A Poll Feature To WordPress
Version: 1.5
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


### Get Poll
function get_poll($poll_id = 0) {
	global $wpdb, $voted;
	// Get Poll Question And Answer Data
	if($poll_id <= 0) {
		$poll_question = $wpdb->get_row("SELECT id, question, total_votes FROM $wpdb->pollsq ORDER BY id DESC LIMIT 1");
		$poll_answers = $wpdb->get_results("SELECT aid, answers, votes FROM $wpdb->pollsa WHERE qid = $poll_question->id ORDER BY votes DESC");
	} else {
		$poll_question = $wpdb->get_row("SELECT id, question, total_votes FROM $wpdb->pollsq WHERE id = $poll_id LIMIT 1");
		$poll_answers = $wpdb->get_results("SELECT aid, answers, votes FROM $wpdb->pollsa WHERE qid = $poll_id ORDER BY votes DESC");
	}
	// If View Results
	if(intval($_GET['showresults']) == 1) { 
		$vote_text = '<p align="center"><a href="index.php">Vote</a></p>'; 
	}
	
	// Poll Variables
	$poll_question_text = stripslashes($poll_question->question);

	// If User Click Vote
	if(isset($_POST['vote'])) {
		if(isset($_POST["poll-.$poll_question->id"])) { 
			$voted = true; 
		}
	}

	// Check User Cookie
	if(isset($_COOKIE["voted_$poll_question->id"])) { $voted = true;	}

	// If User Has Voted
	if($voted || intval($_GET['showresults']) == 1) {
		echo '<table width="100%" border="0" cellspacing="3" cellpadding="3">';
		echo "<tr>\n<td colspan=\"2\" align=\"center\"><b>$poll_question_text</b></td>\n</tr>";
		foreach($poll_answers as $poll_answer) {
			// Make Sure Total Votes Is Not 0
			if(intval($poll_question->total_votes) > 0) {
				$percentage = round((($poll_answer->votes/$poll_question->total_votes)*100));
				$imagebar = $percentage*0.9;
			} else {
				$percentage = 0;
				$imagebar = 1;
			}
			$poll_answer_text = stripslashes($poll_answer->answers);
			echo "<tr>\n<td align=\"left\" width=\"50%\">$poll_answer_text<br /><img src=\"".get_settings('home')."/wp-images/pollbar.gif\" height=\"5\" width=\"$imagebar\" alt=\"".htmlspecialchars($poll_answer_text)." -> $percentage% ($poll_answer->votes Votes)\" /></td>\n";
			echo "<td align=\"right\" width=\"50%\"><b>$percentage%</b></td>\n</tr>\n";			
		}
		echo "<tr>\n<td colspan=\"2\" align=\"center\">Total Votes: <b>$poll_question->total_votes</b>$vote_text</td>\n</tr>";
		echo '</table>';
	// If User Has Not Voted
	} else {
		echo '<form action="'.$_SERVER['REQUEST_URI'].'" name="polls" method="post">';
		echo "<input type=\"hidden\" name=\"poll_id\" value=\"$poll_question->id\" />";
		echo '<table width="100%" border="0" cellspacing="3" cellpadding="3">';
		echo "<tr>\n<td align=\"center\"><b>$poll_question_text</b></td>\n</tr>\n<tr>\n<td align=\"left\">";
		foreach($poll_answers as $poll_answer) {
			echo "<input type=\"radio\" name=\"poll-$poll_question->id\" class=\"poll\" value=\"$poll_answer->aid\" />&nbsp;".strip_tags($poll_answer->answers)."<br />\n";
		}
		echo '</td></tr><tr><td align="center"><input type="submit" name="vote" value="  Vote  " class="Buttons" /><br /><a href="index.php?showresults=1">View Results</a></td></tr></table></form>';
	}
}


### Vote Poll
function vote_poll() {
	global $wpdb;
	if(isset($_POST['vote'])) {
		$poll_id = intval($_POST['poll_id']);
		$poll_aid = intval($_POST["poll-$poll_id"]);
		if(!isset($_COOKIE["voted_$poll_id"])) {
			$vote_a = $wpdb->query("UPDATE $wpdb->pollsa SET votes = (votes+1) WHERE qid = $poll_id AND aid = $poll_aid");
			if($vote_a) {
				$vote_q = $wpdb->query("UPDATE $wpdb->pollsq SET total_votes = (total_votes+1) WHERE id = $poll_id");
				if($vote_q) {
					setcookie("voted_".$poll_id, 1, time() + 30000000, COOKIEPATH);
				}
			}
		}
	}
}
?>