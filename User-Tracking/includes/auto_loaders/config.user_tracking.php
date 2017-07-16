<?php

/**
 * Autoloader array for user tracking functionality. Makes sure that user tracking is instantiated at the
 * right point of the Zen Cart initsystem.
 *
 * @package     user_tracking
 * @author      mc12345678
 * @copyright   Copyright 2008-2013 mc12345678
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://www.zen-cart.com/
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: config.user_tracking.php xxxx 2013-06-28 20:31:10Z conor $
 */

 $autoLoadConfig[0][] = array(
	'autoType' => 'class',
	'loadFile' => 'observers/class.user_tracking.php'
	);
 $autoLoadConfig[199][] = array(
	'autoType' => 'classInstantiate',
	'className' => 'user_tracking',
	'objectName' => 'user_tracking_observe'
	);
