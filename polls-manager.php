<?php
/*
 * Polls Manager For WordPress
 *	- wp-admin/polls-manager.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


### Require Admin Header
require_once('admin.php');


### Variables Variables Variables
$title = __('Manage Polls');
$this_file = $parent_file = 'polls-manager.php';
$mode = trim($_GET['mode']);
$id = intval($_GET['id']);
$aid = intval($_GET['aid']);


### Cancel
if(isset($_POST['cancel'])) {
	Header('Location: polls-manager.php');
	exit();
}


### Form Processing 
if($_POST['do']) {
	// Decide What To Do
	switch($_POST['do']) {
		case 'Add Poll':
			// Add Poll Question
			if(get_magic_quotes_gpc())
				$pollquestion = addslashes(trim($_POST['pollquestion']));
			else
				$pollquestion = trim($_POST['pollquestion']);
			$now = time();
			$addq = $wpdb->query("INSERT INTO $wpdb->pollsq VALUES (0, '$pollquestion', '$now', 0)");
			if(!$addq) {
				$text .= "<font color=\"red\">Error In Adding Poll '" . stripslashes($pollquestion) . "'</font>";
			}
			// Add Poll Answer
			$pollanswers = $_POST['pollanswer'];
			$poll_last_id = intval($wpdb->insert_id);
			foreach($pollanswers as $pollanswer) {
				if(get_magic_quotes_gpc())
					$pollanswer = addslashes(trim($pollanswer));
				else
					$pollanswer = trim($pollanswer);
				$adda = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0,$poll_last_id,'$pollanswer',0)");
				if(!$adda) {
					$text .= "<font color=\"red\">Error In Adding Poll's Answer '" . stripslashes($pollanswer) . "'</font>";
				}
			}
			if(empty($text)) {
				$text = "<font color=\"green\">Poll '" . stripslashes($pollquestion) . "' Added Successfully</font>";
			}
			break;
		case 'Edit Poll':
			// Edit Poll Question
			$id  = intval($_POST['id']);
			$total_votes = intval($_POST['total_votes']);
			if(get_magic_quotes_gpc())
				$pollquestion = addslashes(trim($_POST['pollquestion']));
			else
				$pollquestion = trim($_POST['pollquestion']);
			$editpollq = $wpdb->query("UPDATE $wpdb->pollsq SET question = '$pollquestion', total_votes = $total_votes WHERE id=$id;");
			if(!$editpollq) {
				$text = "<font color=\"red\">Error In Editing Poll '" . stripslashes($pollquestion) . "'</font>";
			}
			// Get Poll Answers ID
			$answers = array();
			$getpollqid = $wpdb->get_results("SELECT aid FROM $wpdb->pollsa WHERE qid=$id ORDER BY answers");
			if($getpollqid) {
				foreach($getpollqid as $answer) {
						$answers[] = intval($answer->aid);
				}
				foreach($answers as $answer) {
					if(get_magic_quotes_gpc())
						$answer_text = addslashes(trim($_POST[$answer]));
					else
						$answer_text = trim($_POST[$answer]);
					$editpolla = $wpdb->query("UPDATE $wpdb->pollsa  SET answers = '$answer_text'  WHERE qid=$id AND aid=$answer");
					if(!$editpolla) {
						$text .= "<br /><font color=\"red\">Error In Editing Poll's Answer '" . stripslashes($answer_text) . "'</font>";
					}
				}
			} else {
				$text .= "<br /><font color=\"red\">Invalid Poll '" . stripslashes($pollquestion) . "'</font>";
			}
			if(empty($text)) {
				$text = "<font color=\"green\">Poll '" . stripslashes($pollquestion) . "' Edited Successfully</font>";
			} else {
				$text .= '<br /><br /><font color="blue">Please do not be alarmed if you see the errors. The errors occur because most likely you did not modify the values.</font>';
			}
			break;
		case 'Delete Poll':
			$id  = intval($_POST['id']);
			$pollquestion = trim($_POST['poll_question']);
			$delete_q = $wpdb->query("DELETE FROM $wpdb->pollsq WHERE id=$id");
			$delete_ans =  $wpdb->query("DELETE FROM $wpdb->pollsa WHERE qid=$id");

			if(!$delete_q) {
				$text = "<font color=\"red\">Error In Deleting Poll '" . stripslashes($pollquestion) . "' Question</font>";
			} 
			if(!$delete_ans) {
				$text .= "<br /><font color=\"red\">Error In Deleting Poll Answers For '" . stripslashes($pollquestion) . "'</font>";
			}
			if(empty($text)) {
				$text = "<font color=\"green\">Poll '" . stripslashes($pollquestion) . "' Deleted Successfully</font>";
			}
			break;
		case 'Add Answer':
			$id  = intval($_POST['id']);
			if(get_magic_quotes_gpc())
				$answer = addslashes(trim($_POST['answer']));
			else
				$answer = trim($_POST['answer']);
			$addq = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0,$id,'$answer',0)");
			if(!$addq) {
				$text = "<font color=\"red\">Error In Adding Poll Answer '" . stripslashes($answer) . "'</font>";
			} else {
				$text = "<font color=\"green\">Poll Answer '" . stripslashes($answer) . "' Added Successfully</font>";
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
	// Add A Poll
	case 'add':
		$title = 'Add Poll';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 5) {
			die('<p>Insufficient Level</p>');
		}
?>
		<ul id="adminmenu2"> 
			<li><a href="polls-manager.php">Manage Polls</a></li> 
			<li class="last"><a href="polls-manager.php?mode=add" class="current">Add Poll</a></li>
		</ul>
		<div class="wrap">
				<h2>Add Poll</h2>
				<?php
				if(isset($_POST['addpollquestion'])) {
					$noquestion = (int) $_POST['noquestion'];
					if(get_magic_quotes_gpc())
						$pollquestion = stripslashes(trim($_POST['pollquestion']));	
					else
						$pollquestion = trim($_POST['pollquestion']);		
				?>
				<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left" scope="row">Question:</th>
							<td><input type="text" size="50" maxlength="200" name="pollquestion" value="<?=$pollquestion?>"></td>
								<?php
									for($i=1; $i <= $noquestion; $i++) {
										echo '<tr>';
										echo "<th align=\"left\" scope=\"row\">Answers $i:</th>";
										echo '<td><input type="text" size="30" maxlength="200" name="pollanswer[]"></td>';
										echo '</tr>';
									}
								?>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="do" value="Add Poll"  class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
						</tr>
					</table>
				</form>
				<?php } else {?>
				<form action="<?=$_SERVER['PHP_SELF']?>?mode=add" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left" scope="row">Question:</th>
							<td><input type="text" size="50" maxlength="200" name="pollquestion"></td>
						</tr>
							<th align="left" scope="row">No. Of Answers:</th>
							<td>
									<select size="1" name="noquestion">
											<?php
											for($i=1; $i <= 20; $i++) {
												echo "<option value=\"$i\">$i</option>";
											}
											?>
									</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="addpollquestion" value="Add Question" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
						</tr>
					</table>
				</form>
				<?php } ?>
		</div>
<?php
		break;
	// Edit A Poll
	case 'edit':
		$title = 'Edit Poll';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 5) {
			die('<p>Insufficient Level</p>');
		}
		$poll_question = $wpdb->get_row("SELECT question, total_votes FROM $wpdb->pollsq WHERE id = $id");
		$poll_answers = $wpdb->get_results("SELECT aid, answers, votes FROM $wpdb->pollsa WHERE qid = $id ORDER BY answers");
?>
		<ul id="adminmenu2"> 
			<li><a href="polls-manager.php" class="current">Manage Polls</a></li> 
			<li class="last"><a href="polls-manager.php?mode=add">Add Poll</a></li>
		</ul>
		<!-- Edit Poll -->
		<div class="wrap">
			<h2>Edit Poll</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<input type="hidden" name="id" value="<?=$id?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th scope="row" colspan="2">Question</th>
					</tr>
					<tr>
						<th scope="row" colspan="2"><input type="text" size="70" maxlength="200" name="pollquestion" value="<?=$poll_question->question?>"></th>
					</tr>
					<tr>
						<th align="left" scope="row">Answers</th>
						<th align="left" scope="row">No. Of Votes</th>
					</tr>
					<?php
						$i=1;
						$totalvotes = 0;
					foreach($poll_answers as $poll_answer) {
						echo "<tr>\n<td>Answer $i:&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"text\" size=\"50\" maxlength=\"200\" name=\"$poll_answer->aid\" value=\"$poll_answer->answers\">&nbsp;&nbsp;&nbsp;\n";
						echo "<a href=\"polls-manager.php?mode=deleteans&id=$id&aid=$poll_answer->aid\" onclick=\"return confirm('You Are About To Delete This Poll Answer \'$poll_answer->answers\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a>\n";
						echo '</td>';
						echo "<td>$poll_answer->votes</td>\n</tr>\n";
						$totalvotes += $poll_answer->votes;
						$i++;
					}
					?>
					</tr>
					<tr>
						<th align="right" scope="row">Total Votes :</th>
						<td><b><?=$poll_question->total_votes?></b>&nbsp;&nbsp;&nbsp;<input type="text" size="4" maxlength="4" name="total_votes" value="<?=$totalvotes?>"></td>
					</tr>
					<tr>
						<td align="center" colspan="2"><input type="submit" name="do" value="Edit Poll" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
		<div class="wrap">
			<h2>Add Answer</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<input type="hidden" name="id" value="<?=$id?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td><b>Add Answer</b></td>
						<td><input type="text" size="50" maxlength="200" name="answer"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="Add Answer" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete A Poll
	case 'delete':
		$title = 'Delete Poll';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 5) {
			die('<p>Insufficient Level</p>');
		}
		$poll_question = $wpdb->get_row("SELECT question, total_votes FROM $wpdb->pollsq WHERE id = $id");
		$poll_answers = $wpdb->get_results("SELECT aid, answers, votes FROM $wpdb->pollsa WHERE qid = $id ORDER BY answers");
		$poll_question_text = stripslashes($poll_question->question);
?>
		<ul id="adminmenu2"> 
			<li><a href="polls-manager.php" class="current">Manage Polls</a></li> 
			<li class="last"><a href="polls-manager.php?mode=add">Add Poll</a></li>
		</ul>
		<!-- Delete Poll -->
		<div class="wrap">
			<h2>Delete Poll</h2>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="post"> 
				<input type="hidden" name="id" value="<?=$id?>">
				<input type="hidden" name="poll_question" value="<?=$poll_question_text?>">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th colspan="2" scope="row">Question</th>
					</tr>
					<tr>
						<td colspan="2" align="center"><?=$poll_question_text?></td>
					</tr>
					<tr>
						<th align="left" scope="row">Answers</th>
						<th scope="row">No. Of Votes</th>
					</tr>
					<?php
						$i=1;
						foreach($poll_answers as $poll_answer) {
							echo "<tr>\n<td>Answer $i:&nbsp;&nbsp;&nbsp;";
							echo "$poll_answer->answers\n";
							echo '</td>';
							echo "<td align=\"center\">$poll_answer->votes</td>\n</tr>\n";
							$i++;
						}
					?>
					</tr>
					<tr>
						<th colspan="2" scope="row">Total Votes : <?=$poll_question->total_votes?></th>
					</tr>
					<tr>
						<td align="center" colspan="2"><br /><p><b>You Are About To Delete This Poll '<?=$poll_question_text?>'</b></p><input type="submit" class="button" name="do" value="Delete Poll" onclick="return confirm('You Are About To The Delete This Poll \'<?=$poll_question_text?>\'.\nThis Action Is Not Reversible.\n\n Choose \'Cancel\' to stop, \'OK\' to delete.')">&nbsp;&nbsp;<input type="submit" name="cancel" Value="Cancel" class="button"></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete A Poll Answer
	case 'deleteans':
		$title = 'Delete Poll\'s Answer';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 5) {
			die('<p>Insufficient Level</p>');
		}
		$poll_answer = $wpdb->get_row("SELECT votes, answers FROM $wpdb->pollsa WHERE aid=$aid AND qid=$id");
		$votes = intval($poll_answer->votes);
		$answer = stripslashes(trim($poll_answer->answers));
		$delete_ans = $wpdb->query("DELETE FROM $wpdb->pollsa WHERE aid=$aid AND qid=$id");
		$update_q = $wpdb->query("UPDATE $wpdb->pollsq SET total_votes = (total_votes-$votes) WHERE id=$id");
?>
		<ul id="adminmenu2"> 
			<li><a href="polls-manager.php" class="current">Manage Polls</a></li> 
			<li class="last"><a href="polls-manager.php?mode=add">Add Poll</a></li>
		</ul>
		<!-- Delete Poll's Answer -->
		<div class="wrap">
			<h2>Delete Poll's Answer</h2>
			<?php
				if($delete_ans) {
					$text = "<font color=\"green\">Poll Answer '$answer' Deleted Successfully</font>";
				} else {
					$text = "<font color=\"red\">Error In Deleting Poll Answer '$answer'</font>";
				}
				if($update_q) {
					$text .= "<br /><font color=\"green\">Poll Question's Total Votes Updated Successfully</font>";
				} else {
					$text .= "<br /><font color=\"red\">Error In Updating Poll's Total Votes</font>";
				}
				echo $text;
			?>
			<p><b><a href="polls-manager.php?mode=edit&id=<?=$id?>">Click here To Go Back To The Poll Edit Page</a>.</b></p>
		</div>
<?php
		break;
	// Main Page
	default:
		$title = 'Manage Polls';
		$standalone = 0;
		require("./admin-header.php");
		if ($user_level < 5) {
			die('<p>Insufficient Level</p>');
		}
		$polls = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY id DESC");
		$total_ans =  $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->pollsa");
		$total_votes = 0;
?>
		<ul id="adminmenu2"> 
			<li><a href="polls-manager.php" class="current">Manage Polls</a></li> 
			<li class="last"><a href="polls-manager.php?mode=add">Add Poll</a></li>
		</ul>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div class="wrap"><h2>Last Action</h2>'.$text.'	</div>'; } ?>
		<!-- Manage Polls -->
		<div class="wrap">
		<h2>Manage Polls</h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th scope="col">ID</b></th>
				<th scope="col">Question</b></th>
				<th scope="col">Total Votes</b></th>
				<th scope="col">Date Added</b></th>
				<th scope="col" colspan="2">Action</th>
			</tr>
			<?php
				if($polls) {
					$i = 0;
					foreach($polls as $poll) {
						if($i%2 == 0) {
							$style = 'style=\'background-color: #eee\'';
						}  else {
							$style = 'style=\'background-color: none\'';
						}
						echo "<tr $style>";
						echo "<td><b>$poll->id</b></td>";
						echo '<td>';
						if($i == 0) { echo '<b>Displayed: </b>'; }
						echo $poll->question.'</td>';
						echo "<td>$poll->total_votes</td>";
						echo '<td>'.date("d.m.Y", $poll->timestamp).'</td>';
						echo "<td><a href=\"polls-manager.php?mode=edit&id=$poll->id\" class=\"edit\">Edit</a></td>";
						echo "<td><a href=\"polls-manager.php?mode=delete&id=$poll->id\" class=\"delete\">Delete</a></td>";
						echo '</tr>';
						$i++;
						$total_votes+= $poll->total_votes;
						
					}
				} else {
					echo '<tr><td colspan="6" align="center"><b>No Polls Found</td></tr>';
				}
			?>
			</table>
		</div>
		<!-- Polls Stats -->
		<div class="wrap">
		<h2>Polls Stats</h2>
			<table border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th align="left" scope="row">Total Polls:</th>
				<td align="left"><?=$i?></td>
			</tr>
			<tr>
				<th align="left" scope="row">Total Polls' Answers:</th>
				<td align="left"><?=number_format($total_ans)?></td>
			</tr>
			<tr>
				<th align="left" scope="row">Total Votes Casted:</th>
				<td align="left"><?=number_format($total_votes)?></td>
			</tr>
			</table>
		</div>
<?php
} // End switch($mode)

### Require Admin Footer
require_once 'admin-footer.php';
?>