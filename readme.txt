-> Polls Plugin For WordPress 2.0
--------------------------------------------------
Author		-> Lester 'GaMerZ' Chan
Email		-> gamerz84@hotmail.com
Website		-> http://www.lesterchan.net/
Demo		-> http://www.lesterchan.net/blogs
Documentation	-> http://dev.wp-plugins.org/wiki/wp-polls
Development	-> http://dev.wp-plugins.org/browser/wp-polls/
Updated		-> 1st April 2006
--------------------------------------------------
Note: I have changed almost the whole structure of WP-Polls, So if there is any bug,
please contact me immediately.
--------------------------------------------------


// Version 2.06 (01-04-2006)
- NEW: Poll Bar Is Slightly Nicer
- NEW: Got Rid Of Tables, Now Using List <li>
- NEW: Added In Most Voted And Least Voted Answer/Votes/Percentage For Individual Poll As Template Variables
- NEW: Display Random Poll Option Under Poll -> Poll Options -> Current Poll
- FIXED: Totally Removed Tables In wp-polls.php

// Version 2.05 (01-03-2006)
- NEW: Improved On 'manage_polls' Capabilities
- NEW: Neater Structure
- NEW: No More Install/Upgrade File, It Will Install/Upgrade When You Activate The Plugin
- NEW: Added Poll Stats Function

// Version 2.04 (01-02-2006)
- NEW: Added 'manage_polls' Capabilities To Administrator Role
- NEW: [poll=POLL_ID] Tag To Insert Poll Into A Post
- NEW: Ability To Edit Poll's Timestamp
- NEW: Ability To Edit Individual Poll's Answer Votes
- NEW: %POLL_RESULT_URL% To Display Poll's Result URL
- FIXED: Cannot Sent Header Error

// Version 2.03 (01-01-2006)
- NEW: Compatible With WordPress 2.0 Only
- NEW: Poll Administration Menu Added Automatically Upon Activating The Plugin
- NEW: Removed Add Poll Link From The Administration Menu
- NEW: GPL License Added
- NEW: Page Title Added To wp-polls.php

// Version 2.02a (17-11-2005)
- FIXED: poll-install.php And poll-upgrade.php will Now Be Installed/Upgraded To 2.02 Instead Of 2.01

// Version 2.02 (05-11-2005)
- FIXED: Showing 0 Vote On Poll Edit Page
- FIXED: Null Vote Being Counted As A Vote
- FIXED: Auto Loading Of Poll Option: Polls Per Page In Poll Archive Page Is Now "No"
- NEW: Host Column In Poll IP Table To Prevent Network Lagging When Resolving IP
- NEW: New Poll Error Template

// Version 2.01 (25-10-2005)
- FIXED: Upgrade Script To Insert Lastest Poll ID Of User's Current Polls, Instead Of Poll ID 1
- FIXED: Replace All <?= With <?php
- FIXED: Added addalshes() To $pollip_user
- FIXED: Better Localization Support (80% Done, Will Leave It In The Mean Time)

// Version 2.0 (20-10-2005)
- NEW: IP Logging
- NEW: Poll Options: Sorting Of Answers In Voting Form
- NEW: Poll Options: Sorting Of Answers In Results View
- NEW: Poll Options: Number Of Polls Per Page In Poll Archive
- NEW: Poll Options: Choose Poll To Display On Index Page
- NEW: Poll Options: Able To Disable Poll With Custom Message
- NEW: Poll Options: Poll Templates
- NEW: Display User's Voted Choice
- FIXED: Better Install/Upgrade Script