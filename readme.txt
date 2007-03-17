-> Polls Plugin For WordPress
--------------------------------------------------
Author	-> Lester 'GaMerZ' Chan
Email	-> lesterch@singnet.com.sg
Website	-> http://www.lesterchan.net/
Demo	-> http://www.lesterchan.net/blogs
Updated	-> 4th October 2005
--------------------------------------------------
Notes	-> Minium level required to add/edit/delete polls is 5
	-> Please backup your database before trying to install this plugin
--------------------------------------------------


// Open wp-admin/menu.php

Find And Remove:
------------------------------------------------------------------
$submenu['polls-manager.php'][5] = array(__('Manage Polls'), 5, 'polls-manager.php');
$submenu['polls-manager.php'][10] = array(__('Add Poll'), 5, 'polls-add.php');
------------------------------------------------------------------


-> Installation Instructions
--------------------------------------------------
// Open wp-settings.php

Find:
------------------------------------------------------------------
$wpdb->postmeta					= $table_prefix . 'postmeta';
------------------------------------------------------------------
Add Below It:
------------------------------------------------------------------
$wpdb->pollsa					= $table_prefix . 'pollsa';
$wpdb->pollsq					= $table_prefix . 'pollsq';
------------------------------------------------------------------


// Open wp-admin/menu.php

Find:
------------------------------------------------------------------
$menu[20] = array(__('Links'), 5, 'link-manager.php');
------------------------------------------------------------------
Add Below It:
------------------------------------------------------------------
$menu[21] = array(__('Polls'), 5, 'polls-manager.php');
------------------------------------------------------------------

// Open wp-admin folder

Put:
------------------------------------------------------------------
polls-install.php
polls-manager.php
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


// Open wp-images folder

Put:
------------------------------------------------------------------
pollbar.gif
------------------------------------------------------------------


// Activate the polls plugin


// Run wp-admin/polls-install.php

Note:
------------------------------------------------------------------
If You See A Blank Page Means It Is Successfully
------------------------------------------------------------------


// Open wp-content/themes/<YOUR THEME NAME>/header.php

Add on the first line:
------------------------------------------------------------------
<?php vote_poll(); ?>
------------------------------------------------------------------


// Open wp-content/themes/<YOUR THEME NAME>/sidebar.php

Add:
------------------------------------------------------------------
<li>
	<h2>Polls</h2>
	<ul><?php get_poll();?></ul>
	<p><a href="wp-polls.php">Polls Archive</a></p>
</li>
------------------------------------------------------------------

Note:
------------------------------------------------------------------
To show specific poll, use <?php get_poll(<ID>);?> where <ID> is your
poll id.
------------------------------------------------------------------