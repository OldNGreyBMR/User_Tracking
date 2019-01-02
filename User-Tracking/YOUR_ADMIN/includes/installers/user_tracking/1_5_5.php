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
 * Version 1.5.5:
 * - Removed HTML that was in the middle of PHP code.
 * - Moved Admin menu option "name" to the languages folder to support multiple languages.
 * - Improved sanitization for catalog and store side data capture.
 * - Made radio labels clickable to support radio button activation.
 * - Added feature to filter URL information that was captured when the site was visited.
 * - Added default settings to the top of the admin/user_tracking.php file.
 * - Removed unused code/variables in the two detection files.
 * - Updated version compare software to latest available in the ZC 1.5.5 series (not identified yet to a specific version)
 **/

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0

    $next_sort = $db->Execute('SELECT (MAX(sort_order) + 10) as sort FROM ' . TABLE_CONFIGURATION . ' WHERE configuration_id = ' . (isset($configuration_id) ? (int)$configuration_id : 0));

    $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION
        . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function)
        VALUES (" . (isset($configuration_id) ? (int) $configuration_group_id : 0) . ", 'CONFIG_USER_TRACKING_USER_FILTER_WORDS', 'User Tracking Filter Words', 'wp%20login%20php,%20action%20register', 'Identify comma separated words used in the url to filter a visitor, for example users that visit the login page, shopping cart, or those attempting to test system limits.<br />Default: wp%20login%20php,%20action%20register', " . (int)$next_sort->fields['sort'] . ", NOW(), NULL, NULL)");

} // END OF VERSION 1.5.x INSTALL
