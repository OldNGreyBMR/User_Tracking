<?php

/**
 * Fire the user tracking logger on the appropriate notifier hooks
 *
 * @author mc12345678
 */
class user_tracking extends base
{
    function __construct()
    {
        $observe_this = array();

        $observe_this[] = 'NOTIFY_FOOTER_END';
        $observe_this[] = 'NOTIFY_ADMIN_FOOTER_END';

        $this->attach($this, $observe_this);
    }

    function update(&$callingClass, $notifier, $paramsArray)
    {
        if (!defined('ZEN_CONFIG_USER_TRACKING') || ZEN_CONFIG_USER_TRACKING !== 'true') return;

        if ($notifier == 'NOTIFY_FOOTER_END') {
            global $session_started, $spider_flag;

            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD === '1') {
                $this->zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD === '2' && $session_started) {
                $this->zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD === '3' && !$spider_flag) {
                $this->zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD === '4') {
                $this->zen_update_user_tracking();
            }
        }

        if ($notifier == 'NOTIFY_ADMIN_FOOTER_END') {
            $this->zen_update_user_tracking();
        }
    }

    function zen_update_user_tracking()
    {
        global $db;

        foreach (explode(",", CONFIG_USER_TRACKING_EXCLUDED) as $skip_ip) {
            $skip_tracking[trim($skip_ip)] = 1;
        }
        $wo_ip_address          = $this->ut_get_ip_address(); //(function_exists('zen_get_ip_address')) ? zen_get_ip_address() : $db->prepare_input(getenv('REMOTE_ADDR'));
        // JTD:05/15/06 - Query bug fixes for mySQL 5.x

        if (isset($skip_tracking[$wo_ip_address]) && $skip_tracking[$wo_ip_address] === 1) return;

        if (IS_ADMIN_FLAG === true) {
            $wo_admin_id            = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
            $admin                  = isset($_SESSION['admin_id']) ? $db->Execute("select admin_name from " . TABLE_ADMIN . " where admin_id = " . (int)$_SESSION['admin_id']) : null;
            $wo_full_name           = isset($admin) ? $admin->fields['admin_name'] : 'Admin not logged in';
            $customers_host_address = isset($_SESSION['admin_ip_address']) ? $_SESSION['admin_ip_address'] : 'admin_ip_address'; // JTD:11/27/06 - added host address support
            $cust_id                = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
        } else {
            if (!empty($_SESSION['customer_id'])) {
                $customer           = $db->Execute("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = " . (int)$_SESSION['customer_id']);
                $wo_full_name       = $customer->fields['customers_firstname'] . ' ' . $customer->fields['customers_lastname'];
            } else {
                $wo_full_name       = 'Guest';
            }
            $cust_id = (!empty($_SESSION['customer_id'])) ? (int)$_SESSION['customer_id'] : 0;
            $customers_host_address = $_SESSION['customers_host_address']; // JTD:11/27/06 - added host address support
        }
        $wo_session_id              = zen_session_id();
        $wo_last_page_url           = getenv('REQUEST_URI');
        $referer_url                = (empty($_SERVER['HTTP_REFERER'])) ? $wo_last_page_url : $_SERVER['HTTP_REFERER'];
        $referer_url                = $referer_url;
        $page_desc                  = '';

        if ((!empty($_GET['products_id']) || !empty($_GET['cPath']))) {
            if (!empty($_GET['cPath']) && $_GET['cPath'] && ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY === 'true') {   // JTD:12/04/06 - Woody feature request
                $cPath_array         = zen_parse_category_path($_GET['cPath']);
                $cPath               = implode('_', $cPath_array);
                $current_category_id = array_pop($cPath_array);
                if (function_exists('zen_get_categories_name')) {
                    $page_desc           = zen_get_categories_name((int)$current_category_id) . '&nbsp;-&nbsp;';
                } elseif (function_exists('zen_get_categories_name_from_product')) {
                    $page_desc           = zen_get_categories_name_from_product((int)$current_category_id) . '&nbsp;-&nbsp;';
                }
            }
            if (!empty($_GET['products_id']) && $_GET['products_id']) {
                $page_desc .= zen_get_products_name((int)$_GET['products_id']);
            }
        } else {
            $page_desc = defined('HEADING_TITLE') ? HEADING_TITLE : NAVBAR_TITLE;
            if (IS_ADMIN_FLAG === true && !defined('HEADING_TITLE')) {
                $page_desc_values = $db->Execute("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = " . (int)$_GET['gID']);
                $page_desc        = $page_desc_values->fields['configuration_group_title'];
            }
        }
        $current_time = time();

        if ($cust_id === null) {
            $cust_id = 0;
        }

        $customers_host_address = $customers_host_address;

        $page_desc = substr($page_desc, 0, 63);

        $wo_last_page_url = substr($wo_last_page_url, 0, 125);

        $referer_url = substr($referer_url, 0, 253);

        $user_track_array = array();

        $user_track_array[] = array('fieldName' => 'customer_id', 'value' => $cust_id, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'full_name', 'value' => $wo_full_name, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'session_id', 'value' => $wo_session_id, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'ip_address', 'value' => $wo_ip_address, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'time_entry', 'value' => $current_time, 'type' => 'date');
        $user_track_array[] = array('fieldName' => 'time_last_click', 'value' => $current_time, 'type' => 'date');
        $user_track_array[] = array('fieldName' => 'last_page_url', 'value' => $wo_last_page_url, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'referer_url', 'value' => $referer_url, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'page_desc', 'value' => $page_desc, 'type' => 'string');
        $user_track_array[] = array('fieldName' => 'customers_host_address', 'value' => $customers_host_address, 'type' => 'string');

        $db->perform(TABLE_USER_TRACKING, $user_track_array);
    }

    function ut_get_ip_address()
    {
        if (function_exists('zen_get_ip_address')) {
            return zen_get_ip_address();
        } else {
            /**
             * Code taken from ZC 1.5.5 function zen_get_ip_address from includes/functions/functions_general.php
             */
            $ip = '';
            /**
             * resolve any proxies
             */
            if (isset($_SERVER)) {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED'];
                } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
                } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_FORWARDED_FOR'];
                } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
                    $ip = $_SERVER['HTTP_FORWARDED'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
            }
            if (trim($ip) == '') {
                if (getenv('HTTP_X_FORWARDED_FOR')) {
                    $ip = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_CLIENT_IP')) {
                    $ip = getenv('HTTP_CLIENT_IP');
                } else {
                    $ip = getenv('REMOTE_ADDR');
                }
            }

            /**
             * sanitize for validity as an IPv4 or IPv6 address
             */
            $ip = preg_replace('~[^a-fA-F0-9.:%/,]~', '', $ip);

            /**
             *  if it's still blank, set to a single dot
             */
            if (trim($ip) == '') $ip = '.';

            return $ip;
        }
    }

}
