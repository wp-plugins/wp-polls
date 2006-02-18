-> Installation Instructions
------------------------------------------------------------------
// Open wp-content/plugins folder

Put:
------------------------------------------------------------------
Folder: polls
------------------------------------------------------------------

// Open Wordpress root folder

Put:
------------------------------------------------------------------
wp-polls.php
------------------------------------------------------------------


// Activate the WP-Polls plugin





-> Usage Instructions
------------------------------------------------------------------
// Open wp-content/themes/<YOUR THEME NAME>/sidebar.php

Add:
------------------------------------------------------------------
<?php if (function_exists('vote_poll')): ?>
<li>
	<h2>Polls</h2>
	<ul>
		<?php get_poll();?>
		<li><a href="<?php echo get_settings('home'); ?>/wp-polls.php">Polls Archive</a></li>
	</ul>
</li>
<?php endif; ?>
------------------------------------------------------------------
Note:
------------------------------------------------------------------
To show specific poll, use <?php get_poll(<ID>);?> where <ID> is your poll id.
To embed a specific poll in your post, use [poll=<ID>] where <ID> is your poll id.
------------------------------------------------------------------


// Polls Stats (You can place it anywhere outside the WP Loop)

// To Display Total Polls

Use:
------------------------------------------------------------------
<?php if (function_exists('get_pollquestions')): ?>
	<?php get_pollquestions(); ?>
<?php endif; ?>
------------------------------------------------------------------


// To Display Total Poll Answers

Use:
------------------------------------------------------------------
<?php if (function_exists('get_pollanswers')): ?>
	<?php get_pollanswers(); ?>
<?php endif; ?>
------------------------------------------------------------------


// To Display Total Poll Votes

Use:
------------------------------------------------------------------
<?php if (function_exists('get_pollvotes')): ?>
	<?php get_pollvotes(); ?>
<?php endif; ?>
------------------------------------------------------------------