<?php
/**
 * @package functions
 * @copyright Copyright 2003-2017 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: mc12345678 thanks to bislewl 6/9/2015
 */

/**
 *
 * Version 1.5.8:
 * - Add an additional check to prevent an admin mydebug log being when the selection is made to display
 *     filtered entries to hide entries that accessed an item on the list of filtered URIs.
 * - Incorporate optimizations provided by DrByte to improve the quality of the program
 * - Joined admin/includes/functions/extra_functions/user_tracking.php into the function code for the catalog
 *     side which meant that was able to incorporate into the catalog observer.  Functions can be reached through
 *     the use of $user_tracking_observe->zen_update_user_tracking() instead of just zen_update_user_tracking().
 * - Installer is expected to remove admin/includes/classes/class.user_tracking.php and 
 *     admin/includes/functions/extra_functions/user_tracking.php if it is present because these files are no longer
 *     needed.
 * - Incorporated ip data collection code into the program to support compatibility between ZC versions where for
 *     example the admin side doesn't have the same ip collection code.
 * - Cleaned up the look of the code to be consistent within an individual file.
 **/

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0

    if (file_exists(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.user_tracking.php')) {
        unlink(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.user_tracking.php');
    }

    if (file_exists(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'extra_functions/user_tracking.php')) {
        unlink(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'extra_functions/user_tracking.php');
    }

} // END OF VERSION 1.5.x INSTALL
