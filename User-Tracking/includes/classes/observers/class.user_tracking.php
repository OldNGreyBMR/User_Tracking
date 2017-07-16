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
        $this->attach($this, ['NOTIFY_FOOTER_END']);
    }

    function update(&$callingClass, $notifier, $paramsArray)
    {
        if (!defined('ZEN_CONFIG_USER_TRACKING') || ZEN_CONFIG_USER_TRACKING != 'true') return;
        if (!function_exists('zen_update_user_tracking')) return;

        if ($notifier == 'NOTIFY_FOOTER_END') {
            global $session_started, $spider_flag;

            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD == 1) {
                zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD == 2 && $session_started) {
                zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD == 3 && !$spider_flag) {
                zen_update_user_tracking();
            }
            if (CONFIG_USER_TRACKING_TRACK_TYPE_RECORD == 4) {
                zen_update_user_tracking();
            }
        }
    }

}
