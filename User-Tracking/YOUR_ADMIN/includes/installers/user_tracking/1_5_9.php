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
 * Version 1.5.9 2019-01-02:
 * - Addressed multiple strict error reporting warning notifications.
 * - Verified operational in ZC 1.5.6. up to PHP 7.2.
 * - Renamed table class information so that does not replace the ZC default table code, although this was not made responsive yet.
 * - Updated version installers to prevent generating mydebug logs when executing the installation on a newer system.
 * - Refactored primary class file.
 * - Modified session collection information to only collect/restore session data if it exists as defined.
 * - Added currency session data to better support shopping cart review of tracked user without strict warning notifications.
 **/

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0


} // END OF VERSION 1.5.x INSTALL
