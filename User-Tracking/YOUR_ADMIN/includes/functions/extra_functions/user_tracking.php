<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//  $Id: usertracking 2004-12-1 dave@open-operations.com http://open-operations.com
//  UPDATED 2013-12-07 mc12345678 http://mc12345678.weebly.com
function zen_update_user_tracking()
{
    global $db;

//    $skip_tracking[CONFIG_USER_TRACKING_EXCLUDED] = 1;
    foreach (explode(",", CONFIG_USER_TRACKING_EXCLUDED) as $skip_ip) {
        $skip_tracking[trim($skip_ip)] = 1;
    }
    $wo_ip_address          = zen_get_ip_address(); //getenv('REMOTE_ADDR');
    if ($skip_tracking[$wo_ip_address] == 1) return;

    $wo_admin_id            = (int)$_SESSION['admin_id'];
    $admin                  = $db->Execute("select admin_name from " . TABLE_ADMIN . " where admin_id = " . (int)$_SESSION['admin_id']);
    $wo_full_name           = $admin->fields['admin_name'];
    $wo_session_id          = zen_session_id();
    $customers_host_address = $_SESSION['admin_ip_address']; // JTD:11/27/06 - added host address support
    $wo_last_page_url       = getenv('REQUEST_URI');
    $referer_url            = ($_SERVER['HTTP_REFERER'] == '') ? $wo_last_page_url : $_SERVER['HTTP_REFERER'];

    if ($_GET['products_id']) {
        $page_desc           = zen_get_products_name((int)$_GET['products_id']);
    } elseif ($_GET['cPath']) {
        $cPath_array         = zen_parse_category_path($_GET['cPath']);
        $cPath               = implode('_', $cPath_array);
        $current_category_id = array_pop($cPath_array);
        $page_desc           = zen_get_categories_name((int)$current_category_id);
    } else {
        $page_desc = defined('HEADING_TITLE') ? HEADING_TITLE : 'CFG';
        if ($page_desc == 'CFG') { // JTD:12/01/06 - look up configuration group name
            $page_desc_values = $db->Execute("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = " . (int)$_GET['gID']);
            $page_desc        = addslashes($page_desc_values->fields['configuration_group_title']);
        }
    }
    $current_time = time();

    $user_track_array = array();

    $user_track_array[] = ['fieldName' => 'customer_id', 'value' => $_SESSION['admin_id'], 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'full_name', 'value' => $wo_full_name, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'session_id', 'value' => $wo_session_id, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'ip_address', 'value' => $wo_ip_address, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'time_entry', 'value' => $current_time, 'type' => 'date'];
    $user_track_array[] = ['fieldName' => 'time_last_click', 'value' => $current_time, 'type' => 'date'];
    $user_track_array[] = ['fieldName' => 'last_page_url', 'value' => $wo_last_page_url, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'referer_url', 'value' => $referer_url, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'page_desc', 'value' => $page_desc, 'type' => 'string'];
    $user_track_array[] = ['fieldName' => 'customers_host_address', 'value' => $customers_host_address, 'type' => 'string'];

    $db->perform(TABLE_USER_TRACKING, $user_track_array);
}
