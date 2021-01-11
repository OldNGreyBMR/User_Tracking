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
 * Version 1.5.10 2020-01-02:
 * - Addressed strict error reporting associated with admin_id as an array key.
 * - Verified operational in ZC 1.5.7. up to PHP 7.3.
 * - Revised the display of session data so that the date/time was not wrapped and allowed the URI to be displayed over a longer row.
 * - Added 
 * - Refactored the observer class. Eliminated excess else statements.
 * - Moved code to a variable assignment instead of processing within a function.
 * - Made use of some of the unused internal variables.
 * - Removed redundant checks against the value of some global variables.
 * - Added database removal code to observer so can be used more robustly, even possibly automatically.
 * - Simplified zen_check_bot function.
 **/

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0


} // END OF VERSION 1.5.x INSTALL
