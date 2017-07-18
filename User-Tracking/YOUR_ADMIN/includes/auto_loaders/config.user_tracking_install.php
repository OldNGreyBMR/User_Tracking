<?php
/**
 * @package functions
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (IS_ADMIN_FLAG === true) { // Verify that file is in the admin.
  $autoLoadConfig[999][] = array(
    'autoType' => 'init_script',
    'loadFile' => 'init_user_tracking.php'
  );
} else {
  trigger_error('Install file attempted in location not related to the admin.', E_USER_WARNING);
  @unlink(__FILE__); // Remove this file if it was placed in the store side.
}