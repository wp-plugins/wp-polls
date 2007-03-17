-> Polls Plugin For WordPress
--------------------------------------------------
Author	-> Lester 'GaMerZ' Chan
Email	-> lesterch@singnet.com.sg
Website	-> http://www.lesterchan.net/
Demo	-> http://www.lesterchan.net/blogs
Updated	-> 17th November 2005
--------------------------------------------------
Notes	-> Minium level required to add/edit/delete polls is 8
	-> Please backup your database before trying to install this plugin
--------------------------------------------------


// Version 2.02a (17-11-2005)
- FIXED: poll-install.php And poll-upgrade.php will Now Be Installed/Upgraded To 2.02 Instead Of 2.01

// Version 2.02 (05-11-2005)
- FIXED: Showing 0 Vote On Poll Edit Page
- FIXED: Null Vote Being Counted As A Vote
- FIXED: Auto Loading Of Poll Option: Polls Per Page In Poll Archive Page Is Now "No"
- ADDED: Host Column In Poll IP Table To Prevent Network Lagging When Resolving IP
- ADDED: New Poll Error Template

// Version 2.01 (25-10-2005)
- FIXED: Upgrade Script To Insert Lastest Poll ID Of User's Current Polls, Instead Of Poll ID 1
- FIXED: Replace All <?= With <?php
- FIXED: Added addalshes() To $pollip_user
- FIXED: Better Localization Support (80% Done, Will Leave It In The Mean Time)

// Version 2.0 (20-10-2005)
- ADDED: IP Logging
- ADDED: Poll Options: Sorting Of Answers In Voting Form
- ADDED: Poll Options: Sorting Of Answers In Results View
- ADDED: Poll Options: Number Of Polls Per Page In Poll Archive
- ADDED: Poll Options: Choose Poll To Display On Index Page
- ADDED: Poll Options: Able To Disable Poll With Custom Message
- ADDED: Poll Options: Poll Templates
- ADDED: Display User's Voted Choice
- FIXED: Better Install/Upgrade Script