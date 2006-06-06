<?php
/*
Plugin Name: WP-Polls Widget
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds a Sidebar Widget To Display Poll From WP-Polls Plugin
Version: 2.1
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


/*  Copyright 2006  Lester Chan  (email : gamerz84@hotmail.com)

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


### Function: Init WP-Polls Widget
function widget_polls_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	### Function: WP-Polls Widget
	function widget_polls($args) {
		extract($args);
		$title = __('Polls');
		echo $before_widget.$before_title.$title.$after_title;
		if (function_exists('vote_poll') && basename($_SERVER['PHP_SELF']) != 'wp-polls.php') {
			get_poll();
			echo "<ul>\n<li><a href=\"".get_settings('home')."wp-polls.php\">Polls Archive</a></li></ul>\n";
		}
		echo $after_widget;
	}
	
	// Register Widgets
	register_sidebar_widget('Polls', 'widget_polls');
}


### Function: Load The WP-Polls Widget
add_action('plugins_loaded', 'widget_polls_init');
?>