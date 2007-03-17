<?php
/*
 * Poll Plugin For WordPress
 *	- wp-polls.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


// Require WordPress Header
require('wp-blog-header.php');

// Vote Stuffs
vote_poll();

// Some Variable To Declare
$polls_questions = array();
$polls_answers = array();

$questions = $wpdb->get_results("SELECT * FROM $wpdb->pollsq ORDER BY id DESC");
$answers = $wpdb->get_results("SELECT aid, qid, answers, votes FROM $wpdb->pollsa ORDER BY votes DESC");
foreach($questions as $question) {
	$polls_questions[] = array('id' => intval($question->id), 'question' => stripslashes($question->question), 'timestamp' => $question->timestamp, 'total_votes' => intval($question->total_votes));
}
foreach($answers as $answer) {
	$polls_answers[] = array('aid' => intval($answer->aid), 'qid' => intval($answer->qid), 'answers' => stripslashes($answer->answers), 'votes' => intval($answer->votes));
}

// If View Results
if(intval($_GET['showresults']) == 1) { 
	$vote_text = '<p align="center"><a href="wp-polls.php">Vote</a></p>'; 
}

// If User Click Vote
if(isset($_POST['vote'])) {
	if(isset($_POST['poll-'.$polls_questions[0]['id']])) { 
		$voted = true; 
	}
}

// Check User Cookie
if(isset($_COOKIE["voted_".$polls_questions[0]['id']])) { $voted = true;	}
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
		<h2 class="pagetitle">Current Poll</h2>
				<?php
						if($voted || intval($_GET['showresults']) == 1) {
							echo '<table width="100%" border="0" cellspacing="3" cellpadding="3">';
							echo "<tr>\n<td colspan=\"2\" align=\"center\"><b>".$polls_questions[0]['question']."</b></td>\n</tr>";
							foreach($polls_answers as $polls_answer) {
								if($polls_questions[0]['id'] == $polls_answer['qid']) {
									if(intval($polls_questions[0]['total_votes']) > 0) {
										$percentage = round((($polls_answer['votes']/$polls_questions[0]['total_votes'])*100));
										$imagebar = $percentage;
									} else {
										$percentage = 0;
										$imagebar = 1;
									}
									echo "<tr>\n<td align=\"left\" width=\"50%\">".$polls_answer['answers']."<br /><img src=\"../wp-images/pollbar.gif\" height=\"5\" width=\"$imagebar\" alt=\"$percentage% (".$polls_answer['votes']." Votes)\"></td>\n";
									echo "<td align=\"right\" width=\"50%\"><b>$percentage%</b></td>\n</tr>\n";
								}
								unset($polls_answer);
							}
							echo "<tr>\n<td colspan=\"2\" align=\"center\">Total Votes: <b>".$polls_questions[0]['total_votes']."</b>$vote_text</td>\n</tr>";
							echo '</table><br />';
					} else {
						echo '<form action="wp-polls.php" name="polls" method="post">';
						echo "<input type=\"hidden\" name=\"poll_id\" value=\"".$polls_questions[0]['id']."\">";
						echo '<table width="100%" border="0" cellspacing="3" cellpadding="3">';
						echo "<tr>\n<td align=\"center\"><b>".$polls_questions[0]['question']."</b></td>\n</tr>\n<tr>\n<td align=\"left\">";
						foreach($polls_answers as $polls_answer) {
							if($polls_questions[0]['id'] == $polls_answer['qid']) {
								echo "<input type=\"radio\" name=\"poll-".$polls_questions[0]['id']."\" value=\"".$polls_answer['aid']."\">&nbsp;".$polls_answer['answers']."<br />\n";
								unset($polls_answer);
							}
						}
						echo '</td></tr><tr><td align="center"><input type="submit" name="vote" value="  Vote  " class="Buttons"><br /><a href="wp-polls.php?showresults=1">View Results</a></td></tr></table></form>';
					}
					// Delete The First Poll From The Array
					unset($polls_questions[0]);
				?>
			<h2 class="pagetitle">Polls Archive</h2>
				<?php
					$i = 1;
					foreach($polls_questions as $polls_question) {
						echo '<table width="100%" border="0" cellspacing="3" cellpadding="3">';
						echo "<tr>\n<td colspan=\"2\" align=\"center\"><b>".$polls_question['question']."</b></td>\n</tr>";
						foreach($polls_answers as $polls_answer) {
							if($polls_question['id'] == $polls_answer['qid']) {
								if(intval($polls_question['total_votes']) > 0) {
									$percentage = round((($polls_answer['votes']/$polls_question['total_votes'])*100));
									$imagebar = $percentage;
								} else {
									$imagebar = 1;
								}
								echo "<tr>\n<td align=\"left\" width=\"50%\">".$polls_answer['answers']."<br /><img src=\"../wp-images/pollbar.gif\" height=\"5\" width=\"$imagebar\" alt=\"$percentage% (".$polls_answer['votes']." Votes)\"></td>\n";
								echo "<td align=\"right\" width=\"50%\"><b>$percentage%</b></td>\n</tr>\n";
								// Delete Away From Array
								unset($polls_answer['answers']);
							}
						}
						echo "<tr>\n<td colspan=\"2\" align=\"center\">Total Votes: <b>".$polls_question['total_votes']."</b></td>\n</tr>";
						echo '</table><br /><hr class="Divider" />';
						$i++;
					}
				?>
	</div>
<?php 		
	get_sidebar();
	get_footer();
?>