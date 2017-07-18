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
 * Version 1.5.7:
 * - Correct the condition of an admin mydebug log being generated when the selection is made to display
 *     filtered entries to hide entries that accessed an item on the list of filtered URIs.
 * - Added guidance in the instructions to support continued operation of this plugin with ZC 1.5.1 because of
 *     an issue that existed in the table handling code of that version corrected in ZC 1.5.2 and above. See
 *     https://github.com/zencart/zencart/commit/f891d240fe199af7510a9a4ae72024f66dd1f33c for solution.
 * - Update the uninstall SQL to prevent removing configuration keys that might have a configuration_group_id of zero.
 **/

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0


} // END OF VERSION 1.5.x INSTALL
