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

$module_constant = 'USER_TRACKING'; // This should be a UNIQUE name followed by _VERSION for convention
$module_installer_directory = DIR_FS_ADMIN . 'includes/installers/user_tracking'; // This is the directory your installer is in, usually this is lower case
$module_name = "User Tracking"; // This should be a plain English or Other in a user friendly way
$admin_page = 'UserTracking';
$zencart_com_plugin_id = 159; // from zencart.com plugins - Leave Zero not to check
//Just change the stuff above... Nothing down here should need to change


$configuration_group_id = '';
if (defined('CONFIG_' . $module_constant . '_VERSION')) {
    // Version information exists, therefore use that information as the current version.
    ${$module_constant . "_current_version"} = constant('CONFIG_' . $module_constant . '_VERSION');
} else {
    // Version information does not exist, begin with version 0.0.0.
    ${$module_constant . "_current_version"} = "0.0.0";

    // Check to see if the configuration group is in the database/plugin has been installed.
    $installed = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '" . $module_name . " Config'");
    if ($installed->EOF || $installed->RecordCount() == 0)
    {
      // The configuration group does not exist, so add it to the database and establish the configuration_group_id.
      $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('" . $module_name . " Config', 'Set " . $module_name . " Configuration Options', '1', '1');");
      $configuration_group_id = (int)$db->Insert_ID();
    } else {
      // Configuration group exists in database, so get the configuration_group_id.
      $configuration_group_id = (int)$installed->fields['configuration_group_id'];
    }

    // Set the sort order of the configuration group to be equal to the configuration_group_id, idea being that each new group will be added to the end.
    $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = " . $configuration_group_id . " WHERE configuration_group_id = " . (int)$configuration_group_id . ";");

    // If the configuration group did not previously exist, then neither did the version information because it is created in this module.
    if ($installed->EOF || $installed->RecordCount() == 0)
    {
      $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES
                      ('" . $module_name . " (Version Installed)', 'CONFIG_" . $module_constant . "_VERSION', '" . ${$module_constant . "_current_version"} . "', 'Version installed:', " . (int)$configuration_group_id . ", 0, NOW(), NULL, 'zen_cfg_select_option(array(\'0.0.0\'),');");
      $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES
                      ('" . $module_name . " (Update Check)', '" . $module_constant . "_PLUGIN_CHECK', '" . SHOW_VERSION_UPDATE_IN_HEADER . "', 'Allow version checking if Zen Cart version checking enabled<br/><br/>If false, no version checking performed.<br/>If true, then only if Zen Cart version checking is on:', " . (int)$configuration_group_id . ", 0, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),');");
      define($module_constant . '_PLUGIN_CHECK', SHOW_VERSION_UPDATE_IN_HEADER);
    }
}
if ($configuration_group_id == '') {
    $config = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key= 'CONFIG_" . $module_constant . "_VERSION'");
    $configuration_group_id = $config->fields['configuration_group_id'];
}

// Obtain a list of files in the installer directory.
if (is_dir($module_installer_directory)) {
  $installers = scandir($module_installer_directory, (defined('SCANDIR_SORT_DESCENDING') ? SCANDIR_SORT_DESCENDING : 1)); // Sorted Descending

  if (!empty($installers)) {
    natsort($installers);
  }

  // Determine the extension of this file to be used for comparison on the others.
  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $file_extension_len = strlen($file_extension); // Allow file extension to be "flexible"

  // Step through each installer file to establish the first file that matches the search criteria.
  while (substr($installers[0], strrpos($installers[0], '.')) != $file_extension || preg_match('~^[^\._].*\.php$~i', $installers[0]) <= 0 || $installers[0] == 'empty.txt') {
    unset($installers[0]);
    if (count($installers) == 0) {
      break;
    }
    $installers = array_values($installers);
  }

  // If there are still installer files to process, then do so.
  if (($num_installers = count($installers)) > 0) {
      $newest_version = $installers[$num_installers - 1];
      $newest_version = substr($newest_version, 0, -1 * $file_extension_len);

//      sort($installers);
      if (version_compare($newest_version, ${$module_constant . "_current_version"}) > 0) {
          foreach ($installers as $installer) {
              if (!(substr($installer, strrpos($installer, '.')) == $file_extension && (preg_match('~^[^\._].*\.php$~i', $installer) > 0 || $installer != 'empty.txt'))) {
                  continue;
              }
              if (!(version_compare($newest_version, substr($installer, 0, -1 * $file_extension_len)) >= 0 && version_compare(${$module_constant . "_current_version"}, substr($installer, 0, -1 * $file_extension_len)) < 0)) {
                  continue;
              }

              include($module_installer_directory . '/' . $installer);
              ${$module_constant . "_current_version"} = str_replace("_", ".", substr($installer, 0, -1 * $file_extension_len));
              $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . ${$module_constant . "_current_version"} . "', set_function = 'zen_cfg_select_option(array(\'" . ${$module_constant . "_current_version"} . "\'),' WHERE configuration_key = 'CONFIG_" . $module_constant . "_VERSION' LIMIT 1;");
              $messageStack->add("Installed " . $module_name . " v" . ${$module_constant . "_current_version"}, 'success');
          }
      }
  }
}


// Respect the admin setting for version checking to prevent checking this if the store is disabled. (typically set because the version checker may generate warnings/errors.
if (SHOW_VERSION_UPDATE_IN_HEADER && !function_exists('plugin_version_check_for_updates')) {
    function plugin_version_check_for_updates($plugin_file_id = 0, $version_string_to_compare = '', $strict_zc_version_compare = false)
    {
        if ($plugin_file_id == 0) return false;
        $new_version_available = false;
        $lookup_index = 0;
        $url1 = 'https://plugins.zen-cart.com/versioncheck/'.(int)$plugin_file_id;
        $url2 = 'https://www.zen-cart.com/versioncheck/'.(int)$plugin_file_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 9);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 9);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Plugin Version Check [' . (int)$plugin_file_id . '] ' . HTTP_SERVER);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        if ($error > 0) {
            trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying http instead.");
            curl_setopt($ch, CURLOPT_URL, str_replace('tps:', 'tp:', $url1));
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
        }
        if ($error > 0) {
            trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying www instead.");
            curl_setopt($ch, CURLOPT_URL, str_replace('tps:', 'tp:', $url2));
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
        }
        curl_close($ch);
        if ($error > 0 || $response == '') {
            trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying file_get_contents() instead.");
            $ctx = stream_context_create(array('http' => array('timeout' => 5)));
            $response = file_get_contents($url1, null, $ctx);
            if ($response === false) {
                trigger_error('file_get_contents() error checking plugin versions.' . "\nTrying http instead.");
                $response = file_get_contents(str_replace('tps:', 'tp:', $url1), null, $ctx);
            }
            if ($response === false) {
                trigger_error('file_get_contents() error checking plugin versions.' . "\nAborting.");
                return false;
            }
        }

        $data = json_decode($response, true);
        if (!$data || !is_array($data)) return false;
        // compare versions
        if (strcmp($data[$lookup_index]['latest_plugin_version'], $version_string_to_compare) > 0) $new_version_available = true;
        // check whether present ZC version is compatible with the latest available plugin version
        $zc_version = PROJECT_VERSION_MAJOR . '.' . preg_replace('/[^0-9.]/', '', PROJECT_VERSION_MINOR);
        if ($strict_zc_version_compare) $zc_version = PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR;
        if (!in_array('v'. $zc_version, $data[$lookup_index]['zcversions'])) $new_version_available = false;
        return ($new_version_available) ? $data[$lookup_index] : false;
    }
}

// Version Checking
// Respect the admin setting for version checking to prevent checking this if the store is disabled. (typically set because the version checker may generate warnings/errors.
if ($zencart_com_plugin_id != 0 && SHOW_VERSION_UPDATE_IN_HEADER && (!defined($module_constant . '_PLUGIN_CHECK') || constant($module_constant . '_PLUGIN_CHECK') !== 'false')) {
    $new_version_details = plugin_version_check_for_updates($zencart_com_plugin_id, ${$module_constant . "_current_version"});
    if ((empty($_GET['gID']) && empty($configuration_group_id) || isset($_GET['gID']) && $_GET['gID'] == $configuration_group_id) && $new_version_details != FALSE) {
        $messageStack->add("Version ".$new_version_details['latest_plugin_version']." of " . $new_version_details['title'] . ' is available at <a href="' . $new_version_details['link'] . '" target="_blank">[Details]</a>', 'caution');
    }
}
