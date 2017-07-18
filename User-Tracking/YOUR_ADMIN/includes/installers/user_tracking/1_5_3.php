<?php
/**
 * @package functions
 * @copyright Copyright 2003-2017 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: mc12345678 thanks to bislewl 6/9/2015
 */

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0

    $UTExists = FALSE;

    if (function_exists('zen_page_key_exists'))
    {
        $UTExists = zen_page_key_exists($admin_page . 'Config');
    } else {
        $UTExists_result = $db->Execute("SELECT FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "Config' LIMIT 1");
        if ($UTExists_result->EOF && $UTExists_result->RecordCount() == 0) {
        } else {
            $UTExists = TRUE;
        }
    }

    $sort_order = array();
    $sort_order = array(
                      'ZEN_CONFIG_USER_TRACKING',
                      'ADMIN_CONFIG_USER_TRACKING',
                      'CONFIG_USER_TRACKING_EXCLUDED',
                      'CONFIG_USER_TRACKING_SESSION_LIMIT',
                      'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE',
                      'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS',
                      'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS',
                      'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP',
                      'CONFIG_USER_TRACKING_PURGE_NUMBER',
                      'CONFIG_USER_TRACKING_PURGE_UNITS',
                      'CONFIG_USER_TRACKING_TRACK_TYPE_RECORD',
                      'USER_TRACKING_WHOIS_URL',
                      'ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY',
                  );

    if (!$UTExists)
    {

        if ((int)$configuration_group_id > 0) {
            zen_register_admin_page($admin_page . 'Config',
                                'BOX_TOOLS_' . $module_constant . '_CONFIG',
                                'FILENAME_CONFIGURATION',
                                'gID=' . $configuration_group_id,
                                'configuration',
                                'Y',
                                $configuration_group_id);

            $messageStack->add('Enabled User Tracking Configuration Menu.', 'success');

            zen_register_admin_page($admin_page,
                                'BOX_TOOLS_' . $module_constant,
                                'FILENAME_' . $module_constant,
                                'gID=' . $configuration_group_id,
                                'tools',
                                'Y',
                                $configuration_group_id);

            $messageStack->add('Enabled User Tracking Menu.', 'success');
        }

        if (defined('TABLE_USER_TRACKING'))
        {
            $table_create = $db->Execute("
                  CREATE TABLE IF NOT EXISTS " . TABLE_USER_TRACKING . " (
                    `customer_id` int(11) default NULL,
                    `click_id` int(11) default NULL,
                    `full_name` varchar(64) NOT NULL default '',
                    `session_id` varchar(32) NOT NULL default '',
                    `ip_address` varchar(15) NOT NULL default '',
                    `time_entry` varchar(14) NOT NULL default '',
                    `time_last_click` varchar(14) NOT NULL default '',
                    `last_page_url` varchar(128) NOT NULL default '',
                    `referer_url` varchar(254) NOT NULL default '',
                    `page_desc` varchar(64) NOT NULL default '',
                    `customers_host_address` varchar(64) NOT NULL default ''
                  ) ENGINE=MyISAM;
            ");
            $messageStack->add('Added table ' . TABLE_USER_TRACKING , 'success');
        }

        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'ZEN_CONFIG_USER_TRACKING', 'User Tracking Visitors', 'true', 'Check the Customers/Guests behaviour ? (each click will be recorded)', " . 10 * (1 + (int)array_search('ZEN_CONFIG_USER_TRACKING', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'ADMIN_CONFIG_USER_TRACKING', 'User Tracking (ADMIN)', 'true', 'Check the ADMINs behaviour ? (each click will be recorded)', " . 10 * (1 + (int)array_search('ADMIN_CONFIG_USER_TRACKING', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_EXCLUDED', 'User Tracking (exclude this IP-Address)', 'your IP', 'Do NOT record this IP Address<br>(like webmaster/owners/Beta-testers)', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_EXCLUDED', $sort_order)) . ", NOW(), NULL, NULL);");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_SESSION_LIMIT', 'User Tracking (Session Limit)', '50', 'Displaying the latest # sessions of this 24 hour period.<br>(SET to 999999 for unlimited per 24 hour period)<br>NOTE:<BR>Watch you space !', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_SESSION_LIMIT', $sort_order)) . ", NOW(), NULL, NULL);");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE', 'User Tracking (Admin User Can Delete)', 'true', 'Allow Record Deletion to be Active?<br/>Setting this to true will override ENTRY and SESSION purges.<br/>Default <b>true</b><br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS', 'User Tracking (Admin User Can Delete Historical Data)', 'false', 'Allow Record Deletion of records older than now - the purge duration set below.<br/>This value is ignored if Admin User Can Delete is set to true.  Otherwise set this to true to allow deletion of visits.<br/>Default <b>false</b>.<br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS', 'User Tracking (Admin User Can Delete SESSIONS)', 'false', 'Allow SESSION Deletion to be Active?<br/>This setting is ignored if Admin User Can Delete is set to true.<br/>Default <b>false</b><br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP', 'User Tracking (Admin User Can Delete IP)', 'false', 'Allow Deletion of records that match the identified IP address?<br/>This setting is ignored if Admin User Can Delete is set to true.<br/>Default <b>false</b><br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_PURGE_NUMBER', 'User Tracking (Purge this Number)', '3', 'What is the number associated with purging before the current date/time?<br/><br/>An example would be to choose 3 here and units associated with days to delete data greater than 3 days before today.<br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_PURGE_NUMBER', $sort_order)) . ", NOW(), NULL, NULL);");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_PURGE_UNITS', 'User Tracking (Purge Units)', '1440', 'Pick the units associate with the periodicity to allow purging data.<br/><br/>60) Hours<br/>1440) Days<br/>10080) Weeks<br/>43200) Months (Based on 30 days)<br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_PURGE_UNITS', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''60'', ''1440'',''10080'',''43200''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'CONFIG_USER_TRACKING_TRACK_TYPE_RECORD', 'User Tracking (Type of User Interaction to Record)', '1', 'Type of user tracking to record?<br/><br/>1 - All visitors.<br/>2 - Visitors views where sessions have been started.<br/>3 - All users except bots/spiders ( requires Configuration->Sessions->Prevent Spider Sessions->true)  (Don\'t know if this works with Zen-Cart versions older than 1.2.6d)<br/><br/>Related to above: If you set Force Cookie Use->true, then at the first user entry, sessions do not start!!! And in variants 2 and 3, user-tracking will not have started. In this case you lose one click and do not log the refferal. But if this user is a returning user and has an old/previous zen cookie the session started and your tracking system will collect this info. So, the result beforehand is not logged or will not be known.<br/>', " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_TRACK_TYPE_RECORD', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''1'', ''2'',''3''),');");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'USER_TRACKING_WHOIS_URL', 'User Tracking (your favorite WHOIS URL)', 'http://www.dnsstuff.com/tools/whois.ch?ip=', 'Put here you favorite WHOIS tracking site<br>(the IP will follow automaticly after this url)', " . 10 * (1 + (int)array_search('USER_TRACKING_WHOIS_URL', $sort_order)) . ", NOW(), NULL, NULL);");
        $db->Execute("INSERT IGNORE INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, sort_order, date_added, use_function, set_function) VALUES (" . (int) $configuration_group_id . ", 'ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY', 'User Tracking (Show Product Category when tracking product clicks)', 'true', 'Show Product Category when tracking product clicks', " . 10 * (1 + (int)array_search('ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY', $sort_order)) . ", NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''),');");

        $messageStack->add('Inserted configuration for ' . $module_name , 'success');

    } else {
        // Updated the sort orders so that they are more uniform and easy to support.
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('ZEN_CONFIG_USER_TRACKING', $sort_order)) . " WHERE configuration_key = 'ZEN_CONFIG_USER_TRACKING';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('ADMIN_CONFIG_USER_TRACKING', $sort_order)) . " WHERE configuration_key = 'ADMIN_CONFIG_USER_TRACKING';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_EXCLUDED', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_EXCLUDED';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_SESSION_LIMIT', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_SESSION_LIMIT';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_PURGE_NUMBER', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_PURGE_NUMBER';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_PURGE_UNITS', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_PURGE_UNITS';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('CONFIG_USER_TRACKING_TRACK_TYPE_RECORD', $sort_order)) . " WHERE configuration_key = 'CONFIG_USER_TRACKING_TRACK_TYPE_RECORD';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('USER_TRACKING_WHOIS_URL', $sort_order)) . " WHERE configuration_key = 'USER_TRACKING_WHOIS_URL';");
        $db->Execute("UPDATE IGNORE " . TABLE_CONFIGURATION . " SET sort_order = " . 10 * (1 + (int)array_search('ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY', $sort_order)) . " WHERE configuration_key = 'ZEN_CONFIG_SHOW_USER_TRACKING_CATEGORY';");

        $messageStack->add('Updated sort order configuration for ' . $module_name , 'success');
    }
} // END OF VERSION 1.5.x INSTALL
