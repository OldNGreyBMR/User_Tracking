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
//  UPDATED 2018-01-02 mc12345678 http://mc12345678.com
  require 'includes/application_top.php';
  require DIR_WS_CLASSES . 'currencies.php';

  $currencies = new currencies();
  require DIR_WS_INCLUDES . 'geoip.inc'; // <- Updated file usually available from: https://raw.github.com/maxmind/geoip-api-php/tree/master/src/geoip.inc

  $gi = geoip_open(DIR_WS_INCLUDES . 'GeoIP.dat',GEOIP_STANDARD); // <- Updated file usually available from: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

  // Default values could be moved to a database field.
  $default = array();
  $default['user_filter_search'] = 'ShowAll';
  $default['SpiderYes'] = false;

// Start User Tracking - Spider Mod 1 of 7 Copied from admin\whos_online.php Zen Cart V1.5.0
  function zen_check_bot($checking) {
    return empty($checking);
  }

  $boxes = array();

  // End User Tracking - Spider Mod 1 of 7
  $date_arr = $date_year = $date_month = $date_day = array();

  $today = getdate();
  $today_month = (int)$today['mon'];
  $today_year = (int)$today['year'];

  if ($today_month <= 0)
  {
    $multi_year = abs((int)($today_month/12));
    $today_month += 12 * (1 + $multi_year);
    $today_year = $today_year - (1 + $multi_year);
  }


  $currentTime = time();
  $headerPosts = '';

  $start_date['year'] = $start_date_year_val = (int)$today_year;
  $start_date['month'] = $start_date_month_val = (int)$today_month;
  $start_date['day'] = $start_date_day_val = (int)$today['mday'];


  if (isset($_POST['Report']) || (isset($_POST['action']) && $_POST['action'] == 'process')) {
    $start_date['month'] = (int)$_POST['sdate_month'];
    $start_date['day'] = (int)$_POST['sdate_day'];
    $start_date['year'] = (int)$_POST['sdate_year'];
  } elseif (isset($_POST['DateChange']) || isset($_POST['sessionSubmit']) || isset($_POST['sessionData'])) {
    if (empty($_POST['DateChange'])) {
      $_POST['DateChange'] = '';
    }
    
    switch ($_POST['DateChange']) {
      case trim(TEXT_BACK_TO . ' ' . TEXT_TODAY):
        $new_time = $_POST['time'] - ((int)(($_POST['time'] - $currentTime) / 86400) + 1) * 86400;
        break;
      case TEXT_BACK_TO . ' ' . date("l M d, Y", $_POST['time'] - 86400):
      case trim(TEXT_BACK_TO):
        $new_time = $_POST['time'] - 86400;
        break;
      case TEXT_FORWARD_TO . date("l M d, Y", $_POST['time'] + 86400):
      case trim(TEXT_FORWARD_TO):
        $new_time = $_POST['time'] + 86400;
        break;
      case trim(TEXT_FORWARD_TO . TEXT_TODAY):
        $new_time = $_POST['time'] + ((int)(($currentTime - $_POST['time']) / 86400)) * 86400;
        break;
      default:
        $new_time = $_POST['time'];
        break;
    }

    $start_date['year'] = (int)date("y", $new_time)+2000;
    $start_date['month'] = (int)date("m", $new_time);
    $start_date['day'] = (int)date("d", $new_time);
  }

  $headerPosts .= zen_draw_hidden_field('sdate_month', (int)$start_date['month']);
  $headerPosts .= zen_draw_hidden_field('sdate_day', (int)$start_date['day']);
  $headerPosts .= zen_draw_hidden_field('sdate_year', (int)$start_date['year']);

  $hidden_time = mktime (0,0,0, $start_date['month'],$start_date['day'],$start_date['year']/*,-1*/);
  if (!isset($_POST['time'])) {
    $_POST['time'] = $hidden_time;
  }

  $headerPosts .= zen_draw_hidden_field('time', $hidden_time);

  $time_frame = $hidden_time; //$_POST['time'];

  $newTime = array();
  $newTime['back_today']['span'] = $time_frame - 86400;
  $newTime['back_today']['cur_is'] = '<';
  $newTime['back_today']['val'] = $time_frame - ((int)(($time_frame - $currentTime) / 86400) + 1) * 86400;
  $newTime['back']['val'] = $time_frame - 86400;
  $newTime['forward']['span'] = $time_frame + 86400;
  $newTime['forward']['cur_is'] = '>';
  $newTime['forward']['val'] = $time_frame + 86400;
  $newTime['forward_today']['span'] = $time_frame + 172800;
  $newTime['forward_today']['cur_is'] = '>';
  $newTime['forward_today']['val'] = $time_frame + ((int)(($currentTime - $time_frame) / 86400)) * 86400;

  $newTime['back_today']['link'] = '';

  //Show move to today if current view is found to be greater than 2 days ahead of current time.
  if ($currentTime < $newTime['back_today']['span'])
  {
    $newTime['back_today']['link'] = zen_draw_input_field('DateChange', TEXT_BACK_TO . ' ' . TEXT_TODAY, 'class="buttonDate_back_today"', false, 'submit') . ' | ';
  }

  //Show move backward one day.
  $newTime['back']['link'] = zen_draw_input_field('DateChange', TEXT_BACK_TO . ' ' . date("l M d, Y", $newTime['back']['val']), 'class="buttonDate_back"', false, 'submit');

  $newTime['forward']['link'] = '';

  //Show move forward one day if current view is greater than 1 days behind current time.
  if ($currentTime > $newTime['forward']['span'])
  {
    $newTime['forward']['link'] = ' | ' . zen_draw_input_field('DateChange', TEXT_FORWARD_TO . date("l M d, Y", $newTime['forward']['val']), 'class="buttonDate_forward"', false, 'submit');
  }

  $newTime['forward_today']['link'] = '';

  //Show move to today if current view is greater than 2 days behind current time.
  if ($currentTime > $newTime['forward_today']['span'])
  {
    $newTime['forward_today']['link'] = ' | ' . zen_draw_input_field('DateChange', TEXT_FORWARD_TO . TEXT_TODAY, 'class="buttonDate_forward_today"', false, 'submit');
  }

  $navLinks = $newTime['back_today']['link'] . $newTime['back']['link'] . $newTime['forward']['link'] . $newTime['forward_today']['link'];

  // Start User Tracking - Spider Mod 2 of 7
  $displaySpider = $default['SpiderYes'];

  if (isset($_POST['SpiderYes'])) {
    if ($_POST['SpiderYes'] == 'ShowSpiders') {
      $displaySpider = true;
    } elseif ($_POST['SpiderYes'] == 'HideSpiders') {
      $displaySpider = false;
    }
  }

  $headerPosts .= zen_draw_hidden_field('SpiderYes', !empty($_POST['SpiderYes']) ? $_POST['SpiderYes'] : '');

  $MinValue = sprintf('%02d', 1);

  if (isset($_POST['MinPick']) && (int)$_POST['MinPick'] > 0) {
    $MinValue = sprintf('%02d', (int)$_POST['MinPick']);
  }

  $headerPosts .= zen_draw_hidden_field('MinPick', !empty($_POST['MinPick']) ? $_POST['MinPick'] : '');

  if (isset($_POST['sessionSubmit'])) {
    if (defined('TEXT_BUTTON_DELETE') && strlen(TEXT_BUTTON_DELETE) > 0 && substr($_POST['sessionSubmit'],0,strlen(trim(TEXT_BUTTON_DELETE))) == trim(TEXT_BUTTON_DELETE)) {
      if (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS == 'true') {
        $_POST['delsession'] = $_POST['sessionData'];
      }
    }
  }

  $user_filter_search = $default['user_filter_search'];

  if (isset($_POST['UserFilteredWordSearch'])) {
    $user_filter_search = 'ShowAll';

    if ($_POST['UserFilteredWordSearch'] == 'ShowOnly') {
      $user_filter_search = 'ShowOnly';
    } elseif ($_POST['UserFilteredWordSearch'] == 'HideOnly') {
      $user_filter_search = 'HideOnly';
    }
  }

  $headerPosts .= zen_draw_hidden_field('UserFilteredWordSearch', $user_filter_search);

  $user_filter_search_words = array();
  if (defined('CONFIG_USER_TRACKING_USER_FILTER_WORDS')) {
    $user_filter_search_words = array_map('trim', explode(',', CONFIG_USER_TRACKING_USER_FILTER_WORDS));
  }

// End User Tracking - Spider Mod 2 of 7
  for ($i=1; $i<32; $i++) {
    $date_arr['day'][] = $date_day[] = array('id' => sprintf('%02d', $i), 'text' => sprintf('%02d', $i));
  }

  for ($i=1; $i<13; $i++) {
    $date_arr['month'][] = $date_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
  }

  for ($i=1; $i <10; $i++) {
    $min_vals[] = array('id' => sprintf('%02d', $i), 'text' => sprintf('%02d', $i));
  }

  $last_year = $today['year'] - 5;
  $first_year = $today['year'];

  for ($i=$first_year; $i > $last_year; $i--) {
    $date_arr['year'][] = $date_year[] = array('id' => sprintf('%02d', $i), 'text' => sprintf('%02d', $i));
  }

  $admin_range_delete = '';

  if (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS == 'true'){  //v1.4.3 9 of 15
//  echo "<p>" . TEXT_DISPLAY_START . CONFIG_USER_TRACKING_SESSION_LIMIT . TEXT_DISPLAY_END;
  //Begin of v1.4.3 10 of 15
    $admin_range_delete = "<p>" . TEXT_DISPLAY_START . CONFIG_USER_TRACKING_SESSION_LIMIT . TEXT_DISPLAY_END;
    $admin_range_delete .= TEXT_PURGE_START . ' ' .zen_draw_input_field('purge', TEXT_PURGE_RECORDS, 'class="buttonpurge buttonDelete"', false, 'submit') . TEXT_PURGE_END;
  }

  $admin_ip_delete = '';

  if ((CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP == 'true') && (defined('CONFIG_USER_TRACKING_IP_EXCLUSION') ? (CONFIG_USER_TRACKING_IP_EXCLUSION != 'your IP' && CONFIG_USER_TRACKING_IP_EXCLUSION != '') : true)){
    $admin_ip_delete = '<br />' . TEXT_DELETE_IP . CONFIG_USER_TRACKING_EXCLUDED . ' ' .zen_draw_input_field('delip', TEXT_PURGE_RECORDS, 'class="buttonpurge buttonDelete"', false, 'submit');
  }

  $deleted_span_text = '';

  if (isset($_POST['purge']) && (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_RECORDS == 'true' )) //v1.4.3 1 of 15
    {
// JTD:10/27/05
//    $db->Execute("DELETE FROM " . TABLE_USER_TRACKING . " where time_last_click < '"  . (time() - ($purge * 3600))."'");
//    $db->Execute('DELETE FROM ' . TABLE_USER_TRACKING . " WHERE time_last_click < '" . ($currentTime - (CONFIG_USER_TRACKING_PURGE_NUMBER * 60 * CONFIG_USER_TRACKING_PURGE_UNITS))."'"); //v1.4.3 2 of 15
    $user_tracking_observe->removeStarting($currentTime);

    $deleted_span_text = '<p><font color="red">' . TEXT_HAS_BEEN_PURGED . '</font></p>';
  }

  $deleted_ip_text = '';

  if (isset($_POST['delip']) && (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_IP == 'true' )) //v1.4.3 3 of 15
  {
//    echo CONFIG_USER_TRACKING_EXCLUDED . ' has been deleted. <p>';
    $excluded_ips = explode(",", CONFIG_USER_TRACKING_EXCLUDED);
    foreach ($excluded_ips as $skip_ip)
    {
      $db->Execute('DELETE FROM ' . TABLE_USER_TRACKING . " WHERE ip_address = '" . (trim($skip_ip)) . "'");
      $deleted_ip_text .= '<br />' . "\n" . $skip_ip . ' from ' . CONFIG_USER_TRACKING_EXCLUDED . ' has been deleted.<br />';
    }
  }

  $deleted_session_text = '';

  if (isset($_POST['delsession']) && $_POST['delsession'] && (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true' || CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS == 'true' )) //v1.4.3 4 of 15
  {
    $db->Execute("DELETE FROM " . TABLE_USER_TRACKING . " WHERE session_id = '" . $_POST['delsession'] . "'");
//    echo $_POST['delsession'] . TEXT_HAS_BEEN_DELETED . ' has been deleted. <p>';
    $deleted_session_text = $_POST['delsession'] . TEXT_HAS_BEEN_DELETED . ' has been deleted. <br />';
  }

  $whos_online =
    $db->Execute("SELECT customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, page_desc," .
                   " session_id, referer_url, customers_host_address FROM " . TABLE_USER_TRACKING  .
                   " WHERE time_entry > " . $time_frame .
                   " AND time_entry < " . ($time_frame + 86400) .
                   " ORDER BY time_last_click desc");

  // Populate the User Tracking data from the who's online related information.
  $results = 0;
  //Begin of v1.4.3 11 of 15
  $spiderCount = 0;
  $num_sessions = 0;
  //End of v1.4.3 11 of 15
  $user_tracking = array();

  while (!$whos_online->EOF) {
    if ($user_filter_search == 'HideOnly' && !empty($user_tracking) && !empty($user_tracking[$whos_online->fields['session_id']]) && array_key_exists('filterwordfound', $user_tracking[$whos_online->fields['session_id']])) {
      $whos_online->MoveNext();
      continue;
    }
    $user_tracking[$whos_online->fields['session_id']]['session_id']=$whos_online->fields['session_id'];
    $user_tracking[$whos_online->fields['session_id']]['ip_address']=$whos_online->fields['ip_address'];
    $user_tracking[$whos_online->fields['session_id']]['customers_host_address']=$whos_online->fields['customers_host_address'];
    $user_tracking[$whos_online->fields['session_id']]['referer_url']=$whos_online->fields['referer_url'];
    $user_tracking[$whos_online->fields['session_id']]['customer_id']=$whos_online->fields['customer_id'];

    if ($whos_online->fields['full_name'] != 'Guest') {
      $user_tracking[$whos_online->fields['session_id']]['full_name'] = '<font color="0000ff"><b>' . $whos_online->fields['full_name'] . '</b></font>';
    }

    if (!isset($user_tracking[$whos_online->fields['session_id']]['filterwordfound']) || !array_key_exists('filterwordfound', $user_tracking[$whos_online->fields['session_id']])) {
      foreach ($user_filter_search_words as $filter_word) {
        if (stripos($whos_online->fields['last_page_url'],$filter_word) !== false) {
          $user_tracking[$whos_online->fields['session_id']]['filterwordfound'] = true;
          break;
        }
      }
    }

    $user_tracking[$whos_online->fields['session_id']]['last_page_url'][$whos_online->fields['time_last_click']] = $whos_online->fields['last_page_url'];

    $user_tracking[$whos_online->fields['session_id']]['page_desc'][$whos_online->fields['time_last_click']] = $whos_online->fields['page_desc'];

    if (empty($user_tracking[$whos_online->fields['session_id']]['time_entry'])
     || ($user_tracking[$whos_online->fields['session_id']]['time_entry'] > $whos_online->fields['time_entry']))
    {
      $user_tracking[$whos_online->fields['session_id']]['time_entry'] = $whos_online->fields['time_entry'];
    }

    if (empty($user_tracking[$whos_online->fields['session_id']]['end_time'])
      || ($user_tracking[$whos_online->fields['session_id']]['end_time'] < $whos_online->fields['time_entry']))
    {
      $user_tracking[$whos_online->fields['session_id']]['end_time'] = $whos_online->fields['time_entry'];
    }

    $results ++;

    $whos_online->MoveNext();
  } // End while list

  //Begin of v1.4.3 12 of 15
  //$listed = 0;
  if (!empty($user_tracking) && $results) {
    foreach ($user_tracking as $ut)
    {
      $is_a_bot=zen_check_bot($ut['session_id']);
      if ($is_a_bot) { // Count the "number" of spiders but not as a session
        $spiderCount ++;
      } else {
        $num_sessions ++; // Count only those visits that were session attributable
      }
      //$listed++;
    }
  }


?><!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" type="text/javascript" src="includes/menu.js"></script>
<script language="javascript" type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
      function init()
      {
        cssjsmenu('navbar');
        if (document.getElementById)
        {
          var kill = document.getElementById('hoverJS');
          kill.disabled = true;
        }
      }
  // -->
</script>
<style type="text/css">
  .buttonDelete {
    color: #FF0000;
  }
  .buttonView {
    color: #008000;
  }
  .UTBox .infoBoxHeading {
    background-color: #FFFFFF;
  }
  .UTBox-cart .infoBoxHeading {
    background-color: #e7e6e0;
  }
  .UTBoxHeading {
    background-color: #FFFFFF;
  }
  #UTBox-cart .infoBoxHeading {
    background-color: #e7e6e0;
  }
  #centerboxcol {
    background-color: #FFFFFF;
  }
  table > tbody > tr > td > table > tbody > tr > .UTBox > table:first-child {
    display: none;
  }
  table > tbody > tr > td > table > tbody > tr > .UTBox > table:nth-child(2) > tbody > tr > td > table:first-child {
    display: none;
  }
  div.reportBox{
    background:#d7d6cc;
    border:1px solid #d7d6cc;
    margin-top:1em;
  }

</style>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<?php
if (!empty($_POST['sessionData'])) {
  $sessionData = $_POST['sessionData'];
?>
<script>
window.onload = function() {
    var el = document.getElementById("<?php echo $sessionData; ?>");
    el.scrollIntoView(true);
}
</script>
<?php
}

  $heading = array(array('text' => '&nbsp;'));

  $HTMLparams = array();
  $HTMLparams['bgcolor'] = 'bgcolor="ffffff"';

  $emptybgheading = array(0=>array(0=>array(0=>array('params'=> 'class="UTBoxHeading"',
                          'text' => '&nbsp;'), 'params' => 'class="UTBoxHeading"', 'text' => '&nbsp;')));
  $emptyheading = array(0=>array(0=>array(0=>array(
                          'text' => '&nbsp;'), 'text' => '&nbsp;')));

  $col = array();
  $row = array();

  // Create a specific box for the header.
  $boxes['header'] = new box_ut;
  $boxes['header']->table_width = '80%';
  $boxes['header']->table_cellpadding = '0';
  $boxes['header']->table_parameters = 'bgcolor="999999"';

  // Box for the body of the page.
  $boxes['body'] = new box_ut;
  $boxes['body']->table_width = '100%';
  $boxes['body']->table_cellpadding = '4';
  $boxes['body']->table_cellspacing = '4';
  $boxes['body']->table_parameters = $HTMLparams['bgcolor'] . ' align="center"';

  $col['body'] = array();
  $row['body'] = array();

  // Initialize body2 table
  $boxes['body2'] = new box_ut;
  $boxes['body2']->table_width = '100%';
  $boxes['body2']->table_cellpadding = '0';
  $boxes['body2']->table_cellspacing = '0';
  $boxes['body2']->table_parameters = $HTMLparams['bgcolor'];

  // Initialize body2 table
  $col['body2'] = array();
  $row['body2'] = array();

  $boxes['body3'] = new box_ut;
  $boxes['body3']->table_width = '100%';
  $boxes['body3']->table_cellpadding = '0';
  $boxes['body3']->table_cellspacing = '0';
  $boxes['body3']->table_parameters = $HTMLparams['bgcolor'];

  // Initialize body3 table
  $col['body3'] = array();
  $row['body3'] = array();

  $boxes['center'] = new box_ut;
  $boxes['center']->table_cellpadding = '0';
  $boxes['center']->table_parameters = $HTMLparams['bgcolor'];
  $boxes['center']->table_width = "95%";

  $col['center'] = array();
  $row['center'] = array();

  $col['time'] = array();
  $row['time'] = array();

  $boxes['table'] = array(); // Table of various boxes.

  $col['header'] = array();
  $column = array();
  // Build the header to the page below the ZC menus.
  $col['header'][] = array('params' => 'class="pageHeading"',
                    'text' => HEADING_TITLE);
  $col['header'][] = array('params'=>'class="main"',
                    'text' => ENTRY_START_DATE);
  $col['header'][] = array('params'=>'class="main"',
                    'text' => '<span style="white-space: nowrap;">' . zen_draw_pull_down_menu('sdate_month', $date_arr['month']/*$date_month*/, $start_date['month']) .
                              zen_draw_pull_down_menu('sdate_day', $date_arr['day']/*$date_day*/, $start_date['day']) .
                              zen_draw_pull_down_menu('sdate_year', $date_arr['year']/*$date_year*/, $start_date['year']) .
                              (zen_not_null(ENTRY_START_DATE_TEXT)
                                ? '<span class="inputRequirement">' . ENTRY_START_DATE_TEXT . '</span>'
                                : '') . '</span>',
                   );
  $col['header'][] = array('params' => 'class="Spiders"',
                    'align' => 'left',
                    'text' => '<span style="white-space: nowrap;">' . zen_draw_radio_field('SpiderYes', 'HideSpiders', (empty($displaySpider)), NULL, ( (isset($displaySpider) && $displaySpider == true) ? 'onClick="this.form.submit();"' : '' ) . 'id="HideSpiders"')
                    . TEXT_HIDE_SPIDERS . '</span>'
                    . ' ' . '<span style="white-space: nowrap;">' . zen_draw_radio_field('SpiderYes', 'ShowSpiders', isset($displaySpider) && $displaySpider == true, NULL, ((empty($displaySpider)) ? 'onClick="this.form.submit();"' : '' ) . 'id="ShowSpiders"')
                    . TEXT_SHOW_SPIDERS . '</span>'
                    . (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD == '3'? TEXT_OPTION3_SPIDER_HIDE : TEXT_SPIDER_HIDE_OTHERS)
                   );
  $col['header'][] = array('params' => 'class="UserFilterSearch"',
                           'align' => 'left',
                           'text' => '<span style="white-space: nowrap;">' . zen_draw_radio_field('UserFilteredWordSearch', 'ShowAll', $user_filter_search == 'ShowAll', NULL, (($user_filter_search !== 'ShowAll') ? 'onClick="this.form.submit();"' : '') . 'id="ShowAllFiltered"')
                           . TEXT_USER_FILTER_ALL . '</span>'
                           . ' ' . '<span style="white-space: nowrap;">' . zen_draw_radio_field('UserFilteredWordSearch', 'HideOnly', $user_filter_search == 'HideOnly', NULL, (($user_filter_search !== 'HideOnly') ? 'onClick="this.form.submit();"' : '') . 'id="HideOnlyFiltered"')
                           . TEXT_USER_FILTER_HIDE . '</span>'
                           . ' ' . '<span style="white-space: nowrap;">' . zen_draw_radio_field('UserFilteredWordSearch', 'ShowOnly', $user_filter_search == 'ShowOnly', NULL, (($user_filter_search !== 'ShowOnly') ? 'onClick="this.form.submit();"' : '') . 'id="ShowOnlyFiltered"')
                           . TEXT_USER_FILTER_ONLY . '</span>'
                           );
  $col['header'][] = array('params' => 'class="MinView"',
                    'align' => 'left',
                    'text' => TEXT_SHOW_MINIMUM_PAGE_VIEWS . zen_draw_pull_down_menu('MinPick', $min_vals, $MinValue, 'onchange="this.form.submit();"')
                   );
  $col['header'][] = array('params' => 'class="main"',
                    'align' => 'left',
                    'text' => zen_draw_input_field('Report', TEXT_BUTTON_REFRESH, 'class="buttonView"' , false, 'submit'),
                  );

  $row['header'] = array();
  $row['header'][] = array_merge($col['header'], array('params' => $HTMLparams['bgcolor'])); // Add a row of data to the table.

  // Create a specific box for the header.
  // Create a table of boxes.
  $boxes['table']['header'] = $boxes['header']->infoBox($emptybgheading, $row['header']);


  $headingbody = array();

  // initialize the body columns and rows.


          $col['body2'][] = array('params' => $HTMLparams['bgcolor'] . ' class="UTBox"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                  'text' => zen_draw_hidden_field('action', 'process') . zen_draw_hidden_field('time', $hidden_time, '') . /*'<span class="UTBox">' .*/ $boxes['table']['header'] /*. '</span>'*/
                                 );
          $row['body2'][] = array_merge($col['body2'], array('params' => $HTMLparams['bgcolor']));
          $col['body2'] = array();

//  echo zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, ''/*'onsubmit="return check_form(user_tracking_stats);"'*/, 'post', ''/*'onsubmit="return check_form(user_tracking_stats);"'*/) . zen_draw_hidden_field('action', 'process');

          $col['body3'][] = array('params' => 'class="smallText"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                'text' => (zen_not_null($deleted_span_text) || zen_not_null($deleted_ip_text) || zen_not_null($deleted_session_text) ? $deleted_span_text . $deleted_ip_text . $deleted_session_text : '&nbsp;'),
                                 );

// Header to results of deletion / Keep "above"
          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor'] . ' test-data="t2"'));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.


          $col['body3'][] = array('params' => 'class="smallText"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                  'text' => EXPLANATION,
                                 );

          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.

          $col['body3'][] = array('params' => 'class="smallText"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                  'text' => /*zen_draw_hidden_field('action', 'process') . */'<b>' . TEXT_SELECT_VIEW .': </b>' .
                                    $navLinks . $headerPosts,
                                 );

          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.

          $col['body3'][] = array('params' => 'class="smallText"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                  'text' => $admin_range_delete,
                                 );

          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.

          $col['body3'][] = array('params' => 'class="smallText"',
                                  'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', ''),
                                  'text' => $admin_ip_delete,
                                 );

          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.




          $col['body3'][] = array('params' => 'class="smallText" colspan="7"',
                                  'text' => sprintf(TEXT_NUMBER_OF_CUSTOMERS, $results) . sprintf(TEXT_NUMBER_OF_USERS, $num_sessions) . sprintf(TEXT_NUMBER_OF_SPIDERS, $spiderCount),
                               );
          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array(); // Clear $col['body3'] for new column data.

?><!--        <table border="0" width="100%" cellspacing="0" cellpadding="0"> -->
<!--    <tr>
      <td valign="top" align="center">
        <table border="0" width="95%" cellspacing="0" cellpadding="2">-->
<?php
  $head = array();
  $head['center'] = array();

  // now let's display it

  $listed=0;
  //Begin of v1.4.3 14 of 15
  //$num_sessions = 0;
  //$spiderCount = 0;
    //End of v1.4.3 14 of 15

  if (!empty($user_tracking) && $results) {
  /* Begin v1.4.3b  (Moved statement to within test) */
     // reset($user_tracking);
  /* End v1.4.3b */
foreach ($user_tracking as $ut) {
    if ($listed++ >= CONFIG_USER_TRACKING_SESSION_LIMIT) {
      break;
    }
// Start User Tracking - Spider Mod 4 of 7
    $is_a_bot=zen_check_bot($ut['session_id']);
//    if ($is_a_bot) {
      //$spiderCount ++;
//    } else {
      //$num_sessions ++;
//    }
    if (empty($is_a_bot) || empty($is_a_bot) && empty($displaySpider) || $displaySpider/* || $displaySpider || (!$displaySpider && !$is_a_bot) */) {
// End User Tracking - Spider Mod 4 of 7
      if ($MinValue > count($ut['last_page_url'])) {
        continue;
      }
      // If supposed to hide all of the users that attempted a word, then when one is found, skip to the next.
      $local_filter_found = array_key_exists('filterwordfound', $ut);

      if ($user_filter_search == 'HideOnly' && $local_filter_found) {
        continue;
      }
      // If desired to show only the users that attempted a word, then kick out those that did not attempt one of those words.
      if ($user_filter_search == 'ShowOnly' && !$local_filter_found) {
        continue;
      }
      $time_online = ($currentTime - $ut['time_entry']);
      if (!isset($ut['full_name']) || !zen_not_null($ut['full_name']))
      {
        $ut['full_name'] = "Guest";
      }

      $customer_link = ($local_filter_found ? TEXT_USER_FILTER_PREFIX : '') . $ut['full_name'];

      if($ut['full_name'] != "Guest")
      {
        $stripped_name = strip_tags($ut['full_name']);
        $exploded_name = explode(" ", $stripped_name);
        $customer_link = "<a href='" . zen_href_link(FILENAME_CUSTOMERS, "search=" . end($exploded_name), $request_type) . "'>" . ( $local_filter_found ? TEXT_USER_FILTER_PREFIX : '') . $ut['full_name'] . "</a>";
      }

      /* Generate the time table */
      $dit=$currentTime - $ut['end_time']; // Idle time
      $dtt=$ut['end_time'] - $ut['time_entry']; // Total Time

      $col['time'] = array();

      $row['time'] = array();

      $col['time'][] = array('params' => 'class="dataTableContent"',
                        'align' => 'right" valign="top',
                        'text' => '<b>' . TABLE_HEADING_ENTRY_TIME . '</b>'
                       );
      $col['time'][] = array('params' => 'class="dataTableContent" colspan="2" valign="top"',
                        'text' => date('H:i:s', $ut['time_entry'])
                       );
      $col['time'][] = array('params' => 'class="dataTableContent"',
                        'align' => 'right" valign="top',
                        'text' => '<b>' . TEXT_IDLE_TIME . '</b>'
                       );
      $col['time'][] = array('params' => 'class="dataTableContent" colspan="2" valign="top"',
                        'text' => sprintf("%02d:%02d:%02d",$dit/3600, ($dit % 3600)/60, $dit % 60)
                       );
      $row['time'][] = array_merge($col['time'], array('params' => $HTMLparams['bgcolor']));

      $col['time'] = array();

      $col['time'][] = array('params' => 'class="dataTableContent"',
                        'align' => 'right" valign="top',
                        'text' => '<b>' . TABLE_HEADING_END_TIME . '</b>'
                       );
      $col['time'][] = array('params' => 'class="dataTableContent" colspan="2" valign="top"',
                        'text' => date('H:i:s', $ut['end_time'])
                       );
      $col['time'][] = array('params' => 'class="dataTableContent"',
                        'align' => 'right" valign="top',
                        'text' => '<b>' . TEXT_TOTAL_TIME . '</b>'
                       );
      $col['time'][] = array('params' => 'class="dataTableContent" colspan="2" valign="top"',
                        'text' => sprintf("%02d:%02d:%02d",$dtt/3600, ($dtt % 3600)/60, $dtt % 60)
                       );
      $row['time'][] = array_merge($col['time'], array('params' => $HTMLparams['bgcolor']));

      //$heading = array(array('text' => '&nbsp;'));

      $boxes['time_table'] = new box_ut; // Shows time periods related to visitor.
      $boxes['time_table']->table_parameters = 'bgcolor="999999"';
      $boxes['table']['time_table'] = $boxes['time_table']->infoBox($emptybgheading, $row['time']);

      $col['center'] = array();

      $col['center'][] = array( 'params' => 'class="dataTableHeadingContent' . ($local_filter_found ? ' lookupAttention' : '') . '" colspan="5"',
                                'text' => TABLE_HEADING_SESSION_ID,
                              );
      $col['center'][] = array( 'params' => 'class="dataTableHeadingContent" colspan="1" width="150"',
                                'text' => TEXT_USER_SHOPPING_CART,
                              );
      $row['center'][] = array_merge($col['center'], array('params'=>'class="dataTableHeadingRow"'));

      $col['center'] = array();


      $col['center'][] = array( 'params' => 'class="dataTableHeadingContent' . ($local_filter_found ? ' lookupAttention' : '') . '" colspan="5"',
                                'text' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', '') .
                                /*zen_draw_hidden_field('action', 'process') . */
                                zen_draw_hidden_field('sessionData', $ut['session_id'], '') .
                                $headerPosts .
                                '<a id="' . $ut['session_id'] . '"></a>' .
                                $customer_link .
                                ",&nbsp;" .
                                $ut['session_id'] .
                                (((CONFIG_USER_TRACKING_ADMIN_CAN_DELETE == 'true') || (CONFIG_USER_TRACKING_ADMIN_CAN_DELETE_SESSIONS == 'true' ))
                                    ? zen_draw_input_field('sessionSubmit', TEXT_BUTTON_DELETE, 'class="buttonDelete"', false, 'submit')
                                    : '') .
                                zen_draw_input_field('sessionSubmit', TEXT_BUTTON_VIEW, 'class="buttonView"' , false, 'submit') .
                                '</form>',
                              );

     //Begin of v1.4.3 15 of 15  ?>
<?php   //End of v1.4.3 15 of 15

    // shopping cart decoding
      $session_expired = FALSE;
      $session_data_query = $db->Execute("select value from " . TABLE_SESSIONS . " WHERE sesskey = '" . $ut['session_id'] . "' and expiry > '" . $currentTime . "'", false, false, 0, true);

      $session_data = '';

      if ($session_data_query->RecordCount() > 0) {
        $session_data = trim($session_data_query->fields['value']);
      } else {
        if (file_exists(zen_session_save_path() . '/sess_' . $ut['session_id'])) {
          $session_data_file = @file(zen_session_save_path() . '/sess_' . $ut['session_id']);
          $session_data = trim($session_data_file[0]);
        } else {
          $session_expired = TRUE;
        }
      }

      if ($session_data != '') {
        $session_data = base64_decode($session_data);
      }

      $datatest = array();

      $cart = "";
      $referer_url = "";
    //$num_sessions ++; // User Tracking - Spiders Mod 5 of 7
//    $_SESSION['cart'] = array();

      $orig_session = $_SESSION;
//echo '<br />session data before: ' . print_r($_SESSION, true) . '<br />';
      if ($length = strlen($session_data)) {
      //unset($_SESSION['admin_id']);

        $start_cart = strpos($session_data, 'cart|O');

        if ($start_cart !== false) {
          $end_cart = (int)strpos($session_data, '|', $start_cart + 6);
          $end_cart = (int)strrpos(substr($session_data, 0, $end_cart), ';}');

          $session_data_cart = substr($session_data, $start_cart, ($end_cart - $start_cart + 2));

          session_decode($session_data_cart);
        }

        $start_currency = strpos($session_data, 'currency|s');

        if ($start_currency !== false) {

          $session_data_currency = substr($session_data, $start_currency, (strpos($session_data, ';', $start_currency) - $start_currency + 1));

          session_decode($session_data_currency);
        }
      }
//echo '<br />session data after: ' . print_r($_SESSION, true) . '<br />';
//echo '<br />session_id: ' . $ut['value']['session_id'] . '<br />';
      $contents = array();

      if (isset($_SESSION['cart']) && is_object($_SESSION['cart'])) {
        $products = $_SESSION['cart']->get_products();
        for ($i = 0, $n = count($products); $i < $n; $i++) {
          $contents[] = array('text' => $products[$i]['quantity'] . ' x ' . '<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . zen_get_product_path($products[$i]['id']) . '&pID=' . $products[$i]['id']) . '">' . $products[$i]['name'] . '</a>');
        // Need to add attributes as potential data to display.
// cPath=23&pID=74
        }

        if (count($products) > 0) {
          $contents[] = array('text' => zen_draw_separator('pixel_black.gif', '100%', '1'));
          $contents[] = array('align' => 'right', 'text'  => TEXT_SHOPPING_CART_SUBTOTAL . ' ' . $currencies->format($_SESSION['cart']->show_total(), true, $_SESSION['currency']));
        } else {
          $contents[] = array('text' => '&nbsp;Empty Cart');
        }
      } elseif ($session_expired) {
        $contents[] = array('align' => 'center', 'text' => 'session expired');
      } else {
        $admin_user = zen_get_users(isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0);
        $contents[] = array('align' => 'center', 'text' => 'active admin group: ' . $admin_user[0]['profileName']);
      }

      //zen_session_start();
      $_SESSION = $orig_session;

      // $heading = array(array('text' => '&nbsp;'));

      if (!empty($contents) )
      {

//        echo '            <td rowspan="4" valign="top" class="UTBox-cart">' . "\n";

        $boxes['cart'] = new box_ut;
        $boxes['cart']->table_parameters = 'bgcolor="999999"';

        ?><!--<span class="UTBox-cart">--><?php
        $col['center'][] = array( 'params' => 'rowspan="4" valign="top" class="UTBox-cart"', // dataTableRowSelected"',
                                  'text' => $boxes['cart']->infoBox($emptyheading, $contents),
                                );

//        echo $boxes['cart']->infoBox($emptyheading, $contents);
//        echo '<!--</span>-->            </td>' . "\n";
      }
      else
      {
//        echo '            <td rowspan="4" valign="top" class="dataTableContent" align="center">session expired' . "\n";
//        echo '            </td>' . "\n";
        $col['center'][] = array( 'params' => 'class="dataTableContent" rowspan="4"',
                                  'align' => 'center" valign="top',
                                  'text' => 'session expired' . "\n",
                                );
      }

?>

<!--              </tr> -->
<?php  $row['center'][] = array_merge($col['center'], array('params'=>'class="dataTableRowSelected"'));

       $col['center'] = array();

       $col['center'][] = array('params' => 'class="dataTableContent"',
                                'align' => 'right" valign="top',
                                'text' => '<b>' . TEXT_CLICK_COUNT . '</b>',
                                );
       $col['center'][] = array('params' => 'valign="top" class="dataTableContent"',
                                'text' => '<font color="FF0000"><b>' . count($ut['last_page_url']) . '</b></font>',
                                );
       $col['center'][] = array('align' => 'center',
                                'params' => 'colspan="2" rowspan="4" class="dataTableContent UTBox"',
                                'text' => $boxes['table']['time_table'],
                                );

?><!--                      <tr>
        <td class="dataTableContent" align="right" valign="top"><b>--><?php /*echo TEXT_CLICK_COUNT;*/ ?><!--</b></td>
        <td class="dataTableContent" valign="top"><font color="FF0000"><b>--><?php /*echo count($ut['last_page_url']);*/?><!--</b></font></td>
        <td class="dataTableContent UTBox" colspan="2" rowspan="4" align="center">-->
        <!--<span class="UTBox">--><?php
        //echo $boxes['table']['time_table'];
      ?><!--</span></td>
                          </tr>-->
                          <?php $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

                          $col['center'] = array();

                          $col['center'][] = array('params' => 'class="dataTableContent"',
                                                   'align' => 'right" valign="top',
                                                   'text' => '<b>' . TABLE_HEADING_COUNTRY . '</b>',
                                                   );
                          ?>
              <!--<tr>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo TABLE_HEADING_COUNTRY; ?></b></td>
<?php $flag = strtolower(geoip_country_code_by_addr($gi, $ut['ip_address']));
      $cn = geoip_country_name_by_addr($gi, $ut['ip_address']);
      if ($flag == '') $flag = 'unknown';
      if ($cn == '') $cn = 'unknown';
      $col['center'][] = array('params' => 'class="dataTableContent" valign="top"',
                               'text' => zen_image(DIR_WS_FLAGS . $flag . '.gif', $cn) . '&nbsp;' . $cn,
                              );?>
        <td class="dataTableContent" valign="top"><?php echo zen_image(DIR_WS_FLAGS . $flag . '.gif', $cn); ?>&nbsp;<?php echo $cn; ?></td>
       </tr>-->
       <?php
       $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

       $col['center'] = array();

       $col['center'][] = array('params' => 'class="dataTableContent"',
                                'align' => 'right" valign="top',
                                'text' => '<b>' . TABLE_HEADING_IP_ADDRESS . '</b>',
                               );
       $col['center'][] = array('params' => 'class="dataTableContent" valign="top"',
                                'text' => '<a href="' . USER_TRACKING_WHOIS_URL . $ut['ip_address'] . '" target="_new">' . $ut['ip_address'] . '</a>',
                               );

       $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

       $col['center'] = array();

       $col['center'][] = array('params' => 'class="dataTableContent"',
                                'align' => 'right" valign="top',
                                'text' => '<b>' . TABLE_HEADING_HOST . '</b>',
                               );
       $col['center'][] = array('params' => 'class="dataTableContent" valign="top" colspan="5"',
                                'text' => (strlen($ut['customers_host_address']) == 0 ? '&nbsp;': $ut['customers_host_address']),
                               );

       $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

       $col['center'] = array();

       ?><!--
              <tr>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo TABLE_HEADING_IP_ADDRESS; ?></b></td>
        <td class="dataTableContent" valign="top"><a href="<?php echo USER_TRACKING_WHOIS_URL; ?><?php echo $ut['ip_address']; ?>" target="_new"><?php echo $ut['ip_address']; ?></a></td>
       </tr>
       <tr>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo TABLE_HEADING_HOST; ?></b></td>
        <td class="dataTableContent" valign="top"><?php echo $ut['customers_host_address']/*echo gethostbyaddr($ut['value']['ip_address']) too slow under WINDOWS */; ?></td>
       </tr>
       <tr>
        <td class="dataTableContent" align="right" valign="top"><b><?php echo TEXT_ORIGINATING_URL; ?></b></td>-->
<?php
      $ref_name = chunk_split($referer_url,40,"<br>");

       $col['center'][] = array('params' => 'class="dataTableContent"',
                                'align' => 'right" valign="top',
                                'text' => '<b>' . TEXT_ORIGINATING_URL . '</b>',
                               );
       $col['center'][] = array('align' => 'left" valign="top',
                                'params' => 'class="dataTableContent" colspan="5"',
                                'text' => '<a href="' . htmlspecialchars($ut['referer_url']) . '" target="_new">' . htmlspecialchars($ut['referer_url']) . '</a>&nbsp;',
                               );

       $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

       $col['center'] = array();


?>
<!--        <td class="dataTableContent" align="left" valign="top" colspan="3"><?php echo '<a href="'.htmlspecialchars($ut['referer_url']).'" target="_new">'. htmlspecialchars($ut['referer_url']) .'</a>'; ?>&nbsp;</td>
       </tr>
       <tr>
        <td class="dataTableContent"></td>
        <td class="dataTableContent" colspan="3">-->
 <!--       <table border="0" cellspacing="1" cellpadding="2" bgcolor="999999" width="100%">-->
<?php

      $row['visited'] = array();

      if (/*$_GET['viewsession'] == $ut['session_id'] || $viewsession == $ut['session_id'] ||*/ isset($_POST['sessionData']) && $_POST['sessionData'] == $ut['session_id'] ){
        unset($_POST['sessionData']);

        foreach ($ut['last_page_url'] as $key => $pu)
        {
          $du = $ut['page_desc'][$key];
          $column = array();

          $column[] = array('params' => 'class="dataTableContent" nowrap',
                            'align' => 'right" valign="top',
                            'text' => date('H:i:s', $key) . ':'
                           );
          $column[] = array('params' => 'class="dataTableContent" nowrap',
                            'align' => 'left" valign="top',
                            'text' => '&nbsp;<a href="' . $pu . '" target="_new">' . (($du!='') ? $du : '') .'</a>&nbsp'
                           );
          $column[] = array('params' => 'width="100%"',
                            'align' => 'left',
                            'text' => '<a href="' . $pu . '" target="_new">' . chunk_split($pu,100,"<br />") . '</a>'
                           );
    ?>
        <!--  <tr bgcolor="ffffff">
            <td class="dataTableContent" valign="top" align="right"><?php echo date('H:i:s', $key); ?>:</td>
            <td class="dataTableContent" nowrap valign="top" align="left">&nbsp;<a href="<?php echo $pu; ?>" target="_new"><?php if ($du!=''){ echo $du;} ?></a>&nbsp;</td>
            <td class="dataTableContent" width="100%" align="left"><a href="<?php echo $pu; ?>" target="_new"><?php echo chunk_split($pu,40,"<br>"); ?></a></td>
          </tr> -->
<?php
          $row['visited'][] = array_merge($column, array('params' => $HTMLparams['bgcolor']));
        }
      }
      $boxes['visited'] = new box_ut;
      $boxes['visited']->table_cellspacing = '1';
      $boxes['visited']->table_parameters = 'bgcolor="999999" class="UTBox"';
      if (count($row['visited']) > 0) {


       $col['center'][] = array('params' => 'colspan="3"',
                                'text' => $boxes['visited']->infoBox($emptybgheading, $row['visited']),
                               );

       $row['center'][] = array_merge($col['center'], array('params' => $HTMLparams['bgcolor']));

       $col['center'] = array();

      }

/*      echo'
      </td>
     </tr> ';*/
// Start User Tracking - Spider Mod 6 of 7
    }
// End User Tracking - Spider Mod 6 of 7
  }
}


?>
<!--       <tr>
        <td class="smallText" colspan="7"><?php echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, $results); /*Start User Tracking - Spider Mod 7 of 7 */ echo sprintf(TEXT_NUMBER_OF_USERS, $num_sessions); echo sprintf(TEXT_NUMBER_OF_SPIDERS, $spiderCount); /*End User Tracking - Spider Mod 7 of 7 */ ?></td>
       </tr>
      </table>--><?php
?><!--</td>
     </tr>-->
<?php
          $col['body3'][] = array('params' => 'id="centerboxcol"',
                                  'align' => 'center" valign="top',
                                  'text' => $boxes['center']->infoBox($emptybgheading, $row['center']),
                                 );

          $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));

          $col['body3'] = array();

  // Data to show the number of views, users, etc. to be displayed at the end of the page.
  $col['body3'][] = array('params' => 'colspan="7"',
                           'text' => sprintf(TEXT_NUMBER_OF_CUSTOMERS, $results) .
                           sprintf(TEXT_NUMBER_OF_USERS, $num_sessions) .
                           sprintf(TEXT_NUMBER_OF_SPIDERS, $spiderCount)
                          );

      $row['body3'][] = array_merge($col['body3'], array('params' => $HTMLparams['bgcolor']));
      $col['body3'] = array();


          ?>
<!--    <tr>
      <td class="smallText" colspan="7">
      <br />-->
<?php
// Start User Tracking - Ver 1.4.2 Mod 1 of
//  echo '<b>' . TEXT_SELECT_VIEW .': </b>';

  //Display links to move forward and backwards in time on the database.
//  echo $navLinks;

   ?><!--</form>
      </td>
     </tr> -->
<?php // From above row to be added to body2


?>
<!--    </table>
   </td>
   </tr>
  </table>-->
  <?php
    $col['body2'][] = array('text' => $boxes['body3']->infoBox($emptybgheading, $row['body3']),
                         );
    $row['body2'][] = $col['body2'];
    $col['body2'] = array();


//    echo $boxes['body2']->infoBox($head['center'], $row['body2']);
  ?>
<!--  </td>-->
<!-- body_text_eof //-->
<!-- </tr>
</table>-->
<?php

  $col['body'][] = array('params' => 'valign="top" width="100%"',
                         'text' => $boxes['body2']->infoBox($emptybgheading, $row['body2'])
                        );
  $row['body'][] = $col['body'];
  $col['body'] = array();


    // End of page to display direction of time travel
  $col['body'][] = array('params' => 'class="smallText" colspan="7"',
                         'form' => zen_draw_form('user_tracking_stats', FILENAME_USER_TRACKING, '', 'post', '') /*. zen_draw_hidden_field('action', 'process')*/,
                         'text' => '<b>' . TEXT_SELECT_VIEW .': </b>' . $navLinks . $headerPosts,
                        );

  $row['body'][] = array_merge($col['body'], array('params' => $HTMLparams['bgcolor']));

  $col['body'] = array();

  echo $boxes['body']->infoBox($emptybgheading, $row['body']); // Display the entire table makeup.

?>
<!-- </form> -->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
