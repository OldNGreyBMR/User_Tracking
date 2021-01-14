Contribution:  User Tracking
Version:  V.1.5.10

Designed for: Zen Cart v1.5 Release
Converted into Zen by: Dave Kennelly dave@open-operations.com
Updated by: mc12345678
Thanks to BlessIsaacola for helping correct the newinstallsql and continued testing
Thanks also to bislewl for the layout/concept of the autoinstaller code.
Thanks to torvista for continued behind the scenes modifications and suggestions.
Thanks to DrByte for allowing me (mc12345678) the opportunity to update a program and to push for such updates to come to fruition.
Thanks to Nitroedge for pushing for and explaining the funtionality desired and its business reason.
Thanks again to DrByte for revisiting the plugin and identifying opportunities to optimize the code for UT 1.5.8
License: under the GPL - See http://www.gnu.org/licenses/licenses.html#GPL License for info.
Forum Support:  Only given via the forums, please. http://www.zen-cart.com/forum/showthread.php?t=35081

========================================================
NEW/Upgrade Installation Procedure:


1. The files in the contribution are arranged for Zen-Cart Version 1.5.x, just put all of them in the appropriate folder(s).

1-B: The file structure is ready to drop in. Rename the admin folder to match your custom admin folder directory
     and upload the directory to the root of your store where that admin directory exists.

2. Login into the admin area.

3. File Modifications:
	3-1- to allow tracking pages loaded by visitors to the site for ZC pre 1.5.5, add or verify present the
	     following line to the end of /includes/templates/YOUR TEMPLATE/common/tpl_main_page.php.  If the file
	     does not exist in your template, then copy the common/tpl_main_page.php file from the template_default
	     into the folder named for your chosen template(s).  (This is the recommended location to store changes
	     to Zen Cart, using the template override folders makes future upgrades a little easier when changes
	     come out or if it is desired to return to basic settings.)

<?php $zco_notifier->notify('NOTIFY_FOOTER_END'); ?>

	3-2- To allow tracking pages loaded in the admin area, add the following to the end of /YOUR_ADMIN/includes/footer.php.

<?php $zco_notifier->notify('NOTIFY_ADMIN_FOOTER_END'); ?>

4. Go to admin of your store (ZenCart), in admin/configuration you should see user tracking config, by clicking on this
   you should see:

User Tracking Configuration

Title Default Value
User Tracking Visitors true
User Tracking (ADMIN) true
User Tracking (exclude this IP-Address) your IP
User Tracking (Session Limit) 50
User Tracking (Admin User Can Delete) true
User Tracking (Purge this Number) 3
User Tracking (Purge Units) Days
User Tracking (Type of User Interaction to Record) 1
User Tracking (your favorite WHOIS URL) http://www.dnsstuff.com/tools/whois.ch?ip=
User Tracking (Show Product Category when tracking product clicks) true
User Tracking (User Tracking Filter Words) wp%20login%20php,%20action%20register:


5- in admin/tools click on user tracking, you should see:

User Tracking Start:

This tool allows for you to see the click patterns of the users through your site, organized by sessions. This data can be very valuable to those looking for ways/areas to improve your site by watching how customers actually use it. You can surf back and forth through the days by using the link below.

7- troubleshooting:
7-1- first check again if all files are correctly located in the zen directories.
7-2- Check for myDebugxxxx logs as described at: https://zen-cart.com/content.php?124-blank-page.  While the issue may not be a blank page
     the information gathered will help all the same.
7-3- go to phpmyadmin, in table, configuration, delete all values related to user tracking, in table configuration-group, delete the table user tracking
7-4- try install sql package with alternative method (if the first time was phpmyadmin, so this time do with zen admin add sql patch tools)
7-5- check again if you have added one modification to both files, tpl.footer.php and footer.php
7-6- No content displayed between the ZC admin menu and the typical footer image after selecting tools->User Tracking?
     Verify that the changes of
     https://github.com/zencart/zencart/commit/f891d240fe199af7510a9a4ae72024f66dd1f33c have been applied.
7-7- still problem, take a look at forum (forum address at the top of this readme) and submit your question.

Updated 01/02/2020 Version 1.5.10 mc12345678:
1. Addressed strict error reporting associated with admin_id as an array key.
2. Verified operational in ZC 1.5.7. up to PHP 7.3.
3. Revised the display of session data so that the date/time was not wrapped and allowed the URI to be displayed over a longer row.
4. Added javascript to bring the selected session to view.
5. Began using array variable to simplify consistent modification of page html.
6. Added ability to disable display of admin activity.
7. Refactored the observer class. Eliminated excess else statements.
8. Moved code to a variable assignment instead of processing within a function.
9. Made use of some of the unused internal variables.
10. Removed redundant checks against the value of some global variables.
11. Added database removal code to observer so can be used more robustly, even possibly automatically.
12. Simplified zen_check_bot function.
13. Updated the installer to remove variable $current_version, ensure operation,
14. provide compatibility for longer version identifiers,


Updated 01/02/2019 Version 1.5.9 mc12345678:
1. Addressed multiple strict error reporting warning notifications.
2. Verified operational in ZC 1.5.6. up to PHP 7.2.
3. Renamed table class information so that does not replace the ZC default table code, although this was not made responsive yet.
4. Updated version installers to prevent generating mydebug logs when executing the installation on a newer system.
5. Refactored primary class file.
6. Modified session collection information to only collect/restore session data if it exists as defined.
7. Added currency session data to better support shopping cart review of tracked user without strict warning notifications.

Updated 07/24/2017 Version 1.5.8 mc12345678:
1. Add an additional check to prevent an admin mydebug log being when the selection is made to display
     filtered entries to hide entries that accessed an item on the list of filtered URIs.
2. Incorporate optimizations provided by DrByte to improve the quality of the program.
3. Joined admin/includes/functions/extra_functions/user_tracking.php into the function code for the catalog
     side which meant that was able to incorporate into the catalog observer. Functions can be reached through
     the use of $user_tracking_observe->zen_update_user_tracking() instead of just zen_update_user_tracking().
4. Installer is expected to remove admin/includes/classes/class.user_tracking.php and
     admin/includes/functions/extra_functions/user_tracking.php if it is present because these files are no
     longer needed.
5. Incorporated ip data collection code into the program to support compatibility between ZC versions where
     for example the admin side doesn't have the same ip collection code.
6. Cleaned up the look of the code to be consistent within an individual file.

Updated 07/16/2017 Version 1.5.7 mc12345678:
1. Correct the condition of an admin mydebug log being generated when the selection is made to display
     filtered entries to hide entries that accessed an item on the list of filtered URIs.
2. Added guidance in the instructions to support continued operation of this plugin with ZC 1.5.1 because of
     an issue that existed in the table handling code of that version corrected in ZC 1.5.2 and above. See
     https://github.com/zencart/zencart/commit/f891d240fe199af7510a9a4ae72024f66dd1f33c for solution.
3. Update the uninstall SQL to prevent removing configuration keys that might have a configuration_group_id of zero.

Updated 07/13/2017 Version 1.5.6 mc12345678:
1. Version 1.5.6:
2. Correct the installer again.
3. Add more explanation in the installation instructions.
4. Added error logging if admin install file is placed in the catalog side of the store.

Updated 07/02/2017 Version 1.5.5 mc12345678:
1. Removed HTML that was in the middle of PHP code.
2. Moved Admin menu option "name" to the languages folder to support multiple languages.
3. Improved sanitization for catalog and store side data capture.
4. Made radio labels clickable to support radio button activation.
5. Added feature to filter URL information that was captured when the site was visited.
6. Added default settings to the top of the admin/user_tracking.php file.
7. Removed unused code/variables in the two detection files.
8. Updated version compare software to latest available in the ZC 1.5.5 series (not identified yet to a specific version)

Updated 06/10/2017 Version 1.5.4 mc12345678:
1. Revised the installer to restore the expected operation. Bug had been introduced that prevented recognition of full installation.
2. Incorporated an additional check/correction to support reperformance of the installation if an error was introduced during installation,
helping to move forward from the above problem.
3. Generalized the installer further to not depend on the file extension taking only 4 characters in length.
4. Added install file to recognize this version.
5. Updated the geodata to the 8 June 2017 version.
6. Cleaned up code a little by removing closing php statements.
7. Prevent the install auto_loader from activating from the catalog side.
8. Rename class constructors to be PHP 7.0 compliant.
9. Modified the display of the User Tracking information
    to use the generic admin table type class/object.
10. The idea is/was that it may make transition to ZC 1.6.0
    easier, though could also be undone as necessary.
11. Further completed the transition of GET links to POST buttons.
12. Incorporated the type conversion issue identified
    by davewest at: https://www.zen-cart.com/showthread.php?222290-Type-conversion-numeric-to-string-and-back&highlight=user+tracking+mktime
13. Generally adjusted the lines of the main page to use
    spaces instead of tabs, aligned code to support improved
    readability.
14. Incorporated associated language file modifications,
    even if the language is/was not translated yet.

Updated 02/23/2017 Version 1.5.3 mc12345678:
1. Implemented an installation/upgrade script instead of expecting users to try to successfully run the installation sql and to
remove the need of clearing the database of existing records when performing an update that does not modify the records table.
2. Tightened security controls on the plugin by using focused database entry/record sanitization as well as output control to
reduce the likelihood of script being executed by display of attempted url.  The captured session data only includes what is needed
to support the desired information to be displayed.
3. Updated code to be php 7.2 compliant based on reported deprecation of the each() function.
4. Modified the cart content display to provide cart data adjacent only to the record applicable to that cart.
5. Added a filter to show records that meet a minimum number of hits for that session.
6. Moved some standard html table code into using the default admin ZC table style code.
7. Converted the submit button to a post style submission instead of a get format.
8. Incorporated additional cod changes suggested by torvista as well as DrByte's most recent in forum posted version.
9. Basically restored the code back to the condition it was in at version 1.5.1.
10. Added the day forward/backward at the bottom of the page (so not just at the top).
11. Updated the geoip.dat file to the February 2017 version.
12. Removed the included User Tracking Configuration file to instead rely on the admin's configuration code.

Version 1.5.2 by corseter:
1. Reverted code back to Version 1.4.2 (or before) and modified the new user sql install to support ZC 1.5.4.
2. Identified items in the Readfirst.txt file that had primarily been addressed in version 1.5 and/or apply to using PHP 5.x

Updated 12/08/2013 Version 1.5
1. Corrected a long standing issue with capturing data from the URL.  The URLs are still truncated; however, they are now sent through for cleansing using Zen Carts db class prior to being sent to the SQL.  This should reduce the occurrences of/prevent the SQL statement failure.
2. Incorporated a new GeoIP.dat file (recent as of: Dec-05-2013.  Similar updates can be obtained from:  http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz.
3. Incorporated a new geoip.inc file to accompany the GeoIP.dat file.  The geoip.inc file can be obtained from: https://raw.github.com/maxmind/geoip-api-php/master/src/geoip.inc.
4. Incorporated zen_href_link to generate links.

UPDATING INSTRUCTIONS:
For SQL statements: If updating from version 1.4.2, then: Use the UPDATE_VER.sql after any other SQL statements (This will update the version number of User Tracking)
otherwise same instructions as applicable from the 11/10/2013 update.

Files Updated from Version 1.4.4:
includes/functions/extra_functions/user_tracking.php
YOUR_ADMIN/user_tracking.php
YOUR_ADMIN/user_tracking_config.php
YOUR_ADMIN/includes/GeoIP.dat
YOUR_ADMIN/includes/geoip.inc
YOUR_ADMIN/includes/functions/extra_functions/user_tracking.php

Added UPDATE_VER.sql
========================================================
Updated 11/26/2013 Version 1.4.4
1. Corrected a PHP warning that appeared because of use of the incorrect programming language.
2. Corrected a problem that could appear in the tracking_user_config area introduced in Version 1.4.3.

UPDATING INSTRUCTIONS:
For SQL statements: same instructions as applicable from the 11/10/2013 update.

Files updated from 1.4.3b are:
YOUR_ADMIN\user_tracking_config.php
includes\functions\extra_functions\user_tracking.php

Files updated from 1.4.3 or 1.4.3a are:
YOUR_ADMIN\user_tracking.php
YOUR_ADMIN\user_tracking_config.php
YOUR_ADMIN\includes\lanaguages\english\user_tracking.php
includes\functions\extra_functions\user_tracking.php
========================================================
Updated 11/24/2013 Version 1.4.3b
1. Corrected a PHP warning that appears if there are no visits tracked for the date being reviewed.
2. Corrected an issue that had the possibility of generating an error log: If a product's description included a character that would be translated by use of an escape character as part of a SQL command, (eg: he's would be changed to he\'s) then, if that escape character landed in just the right position, it would end up as the last character in the string prior to a single quote as part of the SQL statement. This would then cause an error in the execution of the SQL statement and an error log to be generated. Resolution was to remove the last character if that last character is a \. This issue has been resolved for: page description, last page URI, and referrer URI.
3. Updated the button display of spiders, such that if option 3 is chosen in system setup that a message appears.

UPDATING INSTRUCTIONS:
For SQL statements: same instructions as applicable from the 11/10/2013 update.

Files updated from 1.4.3 or 1.4.3a are:
YOUR_ADMIN\user_tracking.php
YOUR_ADMIN\includes\lanaguages\english\user_tracking.php
includes\functions\extra_functions\user_tracking.php
========================================================
Updated 11/14/2013 Version 1.4.3a
1. Updated the version numbers of the instructions and the new_install_user_tracking.sql file. No other changes made in this version.
========================================================
Updated 11/10/2013

1. Added settings to Admin Config:
1.1) User visiting the User tracking page ability to delete log entries.
1.2) Ability to modify how much history to keep when purging/deleting entries using standard units of measure (hours, days, weeks, months (determined using 30 days)
1.3) Added option of data to submit into log based on user forum input.
1.4) Added a version field to the configuration utility.
2. Modified English Language File to show the duration and units of the record(s) being kept/purged.
3. Updated the shop's class.user_tracking observer to support logging of visits based on entries of the Zen-Cart user forum.
4. Changed the Update SQL file to reflect only those changes applicable with this upgrade (I.e. old update SQL which was only really applicable to upgrading older versions of User Tracking to an earlier version.
5. Added the number of unique visitors and spiders to the beginning of the list of sessions, so that it is now at top and bottom.
6. Hid all options to delete information if the admin has set the delete option to false, which means not only does it not display, but also that should not be able to use a get statement to delete the old data.  The ability to delete all of the items overrides the inability to delete any one item.  (ie., if delete all is on and delete IP set to off then delete IP will still be possible.)
7. Added uninstall_user_tracking.sql file.

UPDATING INSTRUCTIONS:
If updating from versions 1.4.0, 1.4.1, or 1.4.2
Use the UPDATE_user_tracking.sql file first.

Use previous update files and instructions to update from Zen Cart versions before 1.5.0 to at least User Tracking 1.4.2.  If updating from Version 1.4.2 of User Tracking, then need to

replace/update the following files:
admin/user_tracking.php
admin/user_tracking_config.php
admin/includes/extra_datafiles/user_tracking_database_tables.php
admin/includes/languages/english/user_tracking.php

Update the following files:
includes/classes/observers/class.user_tracking.php

The includes/classes/observers/class.user_tracking.php file contains additional rules that are controlled by an admin configuration setting.  These rules are not provided here for modification of the footer.php file.

========================================================

UPDATED 06/24/2013

Identified that IE 7 implementation of OnChange for hide/show spiders did not work as expected.  Changed command to OnClick to be loaded for the radio button that would perform the next action.  (I.e., when spiders are hidden, clicking on Hide spiders will now do nothing, while clicking on Show Spiders will reload the page and show the data collected from a visit by a spider.)
Added notification/observer events instead of simply using function calls.  The actions are still dependent on the footer(s) (requires only modifying one footer file for each of the store and the admin versus attempting to identify every possible header file/header event to observe.)  The observers are not required to be used (I.e., no change to the template files is required to continue use after updating the other files.)
Incorporated text that was hard coded in the admin area into the language file.
Added display of Back to Today or Forward to Today if selected date is 2 or more days away from the date at the time the php code is run.
Updated the Readme's to try to incorporate the history of previous updates, to minimize the number of files, and to capture the recent changes.

UPDATING INSTRUCTIONS:

Similar updating instructions as previous for updating from earlier Zen Cart version, if updating from Version 1.4.0 or 1.4.1 of User Tracking, then need to
replace/update the following files:
admin/user_tracking.php
admin/languages/english/user_tracking.php
admin/languages/farsi/user_tracking.php

Added the following files:
includes/auto_loaders/config.user_tracking.php
includes/classes/observers/class.user_tracking.php
admin/includes/auto_loaders/config.user_tracking.php
admin/includes/classes/class.user_tracking.php

The above added files support using the notify/observer events. If not incorporating the following changes, then those four files may be deleted without negative impact to your cart.  To update to using the notify/observer events, modify the line of code in each of the following two files from:
file:
/includes/templates/ YOUR TEMPLATE /common/tpl_footer.php
from code:
<?php if (ZEN_CONFIG_USER_TRACKING == 'true') { zen_update_user_tracking(); } ?>
to code:
<?php $zco_notifier->notify('NOTIFY_FOOTER_END'); ?>

file:
/admin/includes/footer.php
from code:
<?php if (ADMIN_CONFIG_USER_TRACKING == 'true') { zen_update_user_tracking(); } ?>
to code:
<?php $zco_notifier->notify('NOTIFY_ADMIN_FOOTER_END'); ?>

========================================================

UPDATED 06/15/2013

Added ability to hide/show spider visits using the spiders.txt file of Zen Cart.  Default view is to hide spider visits.
Modified the information shown at the bottom of the view to show the number of users and number of bots/spiders that have visited.  This spider/bot information will show whether the actual visit is or is not shown.  Also incorporated modifications published in the Thread related to display of the Idle time of a visitor.  (Previous calculations lead to incorrect display depending on timezone.)

UPDATING INSTRUCTIONS:

Similar updating instructions as previous for updating from earlier Zen Cart version, if updating from Version 1.4.0 of User Tracking, then need to only replace/update the admin/user_tracking.php file.

========================================================

UPDATED: 07/28/2011 - V 1.5.0 - Install Instructions The Same
knuckle-101 - Host 99 http://www.host-99.com/- Donate to the ZenCart Team

http://www.zen-cart.com/index.php?main_page=infopages&pages_id=14

========================================================

UPDATED 07/28/2011

Removed admin --> includes --> boxes (Entire Folder and Sub Folders and files)
SQL Added for Dropdown Link Under Tools Section

UPDATING INSTRUCTIONS:

If updating from previous versions 1.3.9h etc. Remove the following files from the admin for V 1.5.0 new navigation method

admin --> includes --> boxes --> extra_boxes --> user_tracking_tools_dhtml.php


Enjoy....

========================================================
assembled: reza:02/15/2007,
----------------
I have not changed any thing in this contribution. when I got problem after installation of user tracker version 1.3.6 on zen 1.3.7 and reading all comments of the forum, I found that most of the peoples got the same problems due to the separation of original package and upgraded package. for This I decided to put all together for zen 1.3.7.

with thanks to Jeff and Woodymon for their supports in the forum. please address your donations to them.

========================================================

User Tracking v.1.3.6.2 (by JeffD) (UPDATE from v.1.3.6.1)
Packaged by Woodymon 02-13-07

Regards to changes in User Tracking 1.3.6.2 120406 (JeffD) (Dec. 04, 2006), since 1.3.6.2 was not a regular release I am only including in this attachment changes from previous version: two changed files php plus the SQL patch and the readme.txt. I am hoping JeffD will be back with a full fileset User Tracking update in the future.

I'm running User Tracking v.1.3.6.2 (by JeffD) on Zen Cart 1.37 shop and it is working well. YMMV on other versions of Zen Cart.

BE SURE TO READ THE README!!!! No warranties implied. If you are not sure how to install this then best to stay with previous version.

REPEAT: ONLY the CHANGED User Tracking v.1.3.6.2 files are in this attachment. You will need JeffD's previous version 1.3.6.1 User Tracking installed for this to be useful to you.

Please post questions to the User Tracking mod support thread:
http://www.zen-cart.com/forum/showthread.php?t=35081


UPDATE INSTRUCTIONS:

If you have the previous version of User Tracking 1.3.6.1 installed then, besides updating your shop with the two new php files listed below, you only need to run following SQL patch (and not the full SQL patch file included in this attachment):

[code]INSERT INTO configuration VALUES ('', 'User Tracking (Show Product Category when tracking product clicks)', 'ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY', 'true', 'Show Product Category when tracking product clicks', 999, 60, '2006-12-05 11:19:26', '2006-12-05 21:20:07', NULL, 'zen_cfg_select_option(array(''true'', ''false''),');[/code]

Add your table prefix as required. (e.g. zen_configuration)


FILES INCLUDED (with changes from previous version):

readme.txt
user_tracking.sql

\includes\functions\extra_functions\
user_tracking.php

\admin\includes\functions\extra_functions\
user_tracking.php

--
FILES NOT INCLUDED (no changes from previous version):

\includes\extra_datafiles\
user_tracking_database_tables.php

\admin\
user_tracking.php
user_tracking_config.php

\admin\images\flags\
*.*

\admin\includes\
GeoIP.dat
geoip.inc

\admin\includes\boxes\extra_boxes\
user_tracking_tools_dhtml.php

\admin\includes\extra_datafiles\
user_tracking_database_tables.php

\admin\includes\languages\english\
user_tracking.php
user_tracking_config.php

{end}

----------------
Updated: JTD:11/27/06 - jeffdripps@isegames.com

Modified the user_tracking.sql by extending the size of the last_page_url field from 64 characters to 128 characters as this was previously too short and would often truncate URL link data.

Truncated the session_id field from 128 characters to 32 characters as this was wasted space.

Below is the commands necessary for the sql patch tool to alter the table to the new format described in the user_tracking.sql and described above:

ALTER TABLE `user_tracking` CHANGE `session_id` `session_id` VARCHAR(32);
ALTER TABLE `user_tracking` CHANGE `last_page_url` `last_page_url` VARCHAR(128);
ALTER TABLE `user_tracking` ADD `customers_host_address` varchar(64) NOT NULL;


Changed the script itself into a form and added a popup date selection menu so you can select a specific date to begin the listing.

Fixed the functions to work with sql 5.x and for longer last_page_url field writes.

Added a missing country flag or two.

Included the latest GeoIP.dat file.

Updated language defines for the popup additions.

History:
updated: 02/15/2007- assembling upgraded user tracking 1.3.6.2 with the original 1.3.6
Updated: 11/28/06 - fixed omission and errors in sql.
Updated: 11/29/06 - Added customers_host_address field support
Updated: 11/30/06 - Revised the report query and listing to begin at midnight on the requested day rather than (the current time - 24 hours) as was the case previously. This just seems more useful and intuitive.
Updated: 12/01/06 - Added Support for displaying product name, (borrowed from Pinghead version) as per Woody's suggestion.
Updated: 12/01/06 - Added Support for recording all page titles when tracking admin.
Updated: 12/04/06 - Added Configuration key and code for disabling category output when tracking product clicks.


Please let me know if you find bugs or omissions?

Thanks,
Jeff
jeffdripps@isegames.com

==========================================================

WHAT DOES THIS MODULE DO?

This module tracks your visitors hits on your site and displays pages visited by user session in admin
========================================================

History:
12/12/2004 - Initial Release
07/28/2011 - Updated SQL for Box Links


========================================================

FILES TO OVER-WRITE
none

========================================================

File Modifications: 2

add the following to the end of /includes/templates/ YOUR TEMPLATE /common/tpl_footer.php

<?php if (ZEN_CONFIG_USER_TRACKING == 'true') { zen_update_user_tracking(); } ?>

add if you want to track admin pages viewed add the following to the end of /admin/includes/footer.php

<?php if (ADMIN_CONFIG_USER_TRACKING == 'true') { zen_update_user_tracking(); } ?>

========================================================

Database Modifications:

A new database table needs to be created to store your site visits.

========================================================

INSTALLATION:

All files are arranged in the correct Zen Cart v1.2.x - 1.3.x structure.
Just upload to your server as they are without any editing required.
upload the included user_tracking.sql using the Zen-cart SQL Patches Tool
Make the file modifications above.

========================================================

USE:
Admin/Tools/User Tracking displays your users visits
Admin/Tools/User Manager Config allows some user manager configuration

