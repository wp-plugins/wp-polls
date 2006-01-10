-> Installation Instructions
------------------------------------------------------------------
// Open wp-admin folder

Put:
------------------------------------------------------------------
polls-install.php
polls-manager.php
polls-options.php
------------------------------------------------------------------


// Open wp-content/plugins folder

Put:
------------------------------------------------------------------
polls.php
------------------------------------------------------------------

// Open Wordpress root folder

Put:
------------------------------------------------------------------
wp-polls.php
------------------------------------------------------------------


// Open wp-includes/images folder

Put:
------------------------------------------------------------------
pollbar.gif
------------------------------------------------------------------


// Activate the polls plugin


// Run wp-admin/polls-install.php

Note:
------------------------------------------------------------------
Please remember to remove polls-install.php after installation.
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