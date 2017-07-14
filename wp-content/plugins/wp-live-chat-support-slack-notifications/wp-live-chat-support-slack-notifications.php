<?php
/*
Plugin Name: WP Live Chat Support - Slack Notifications
Plugin URL: http://wp-livechat.com
Description: An add-on for WP Live Chat Support that lets you notify your slack room of a new incoming chat.
Version: 1.0.02
Author: WP-LiveChat
Author URI: http://wp-livechat.com
Contributors: WP-LiveChat, CodeCabin_, NickDuncan, Jarryd Long, dylanauty
Text Domain: wp-live-chat-support-slack-notifications
Domain Path: /languages
*/

/**
 * 1.0.02 2016-09-20 Low Priority
 * Tested on WordPress 4.6.1
 * Moved Settings to tab
 *
 * 1.0.01 2016-02-03 Low Priority
 * Offline message capability available when using both the Basic or Pro versions of WP Live Chat Support
 * Tested on WordPress 4.4
 * 
 * 1.0.0
 * Launched!
 */

if(!defined('WPLC_SN_PLUGIN_DIR')) {
	define('WPLC_SN_PLUGIN_DIR', dirname(__FILE__));
}

global $wplc_sn_version;
$wplc_sn_version = "1.0.02";

/* hooks */
add_action('wplc_hook_initiate_chat','wplc_sn_notification');
add_action('wplc_hook_missed_chat','wplc_sn_notification_missed');
add_action('wplc_hook_offline_message','wplc_sn_notification_offline');

add_filter("wplc_filter_setting_tabs","wplc_sn_settings_tab_heading");
add_action("wplc_hook_settings_page_more_tabs","wplc_sn_settings_tab_content");

add_action('wplc_hook_admin_settings_save','wplc_sn_save_settings');
add_action('admin_enqueue_scripts', 'wplc_sn_admin_js');
/* init */
add_action("init","wplc_sn_first_run_check");
add_action("admin_head","wplc_sn_check_if_plugins_active");
add_action("admin_head","wplc_sn_check_main_version_number");


/**
* Call to slack
*
* @since        1.0.0
* @param        string  $message    the message that is pushed to the room
* @param        string  $room       the room that the message is pushed to
* @param        string  $icon       the icon of the bot that appears with the message
* @param        string  $webhook    the webhook URL - must always start with "//" and not "http" or "https". example: "//website.com"
* @return       bool
*
*/
function wplc_slack($message, $room = "general", $icon = ":speaker:", $webhook = false) {
    if (!$webhook) { return false; }

    if (substr($webhook,0,2) == "//") { $webhook = "https:".$webhook; }

    $room = ($room) ? $room : "general";
    $data = "payload=" . json_encode(array(
            "channel"       =>  "#{$room}",
            "text"          =>  $message,
            "icon_emoji"    =>  $icon
        ));

    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem"); //  ok
    $result = curl_exec($ch);
    if ($result == FALSE) {
        echo curl_error($ch);
        return;
    }
    curl_close($ch);
    
    return $result;
}


/**
* Notify the slack channel of an incoming chat
*
* @since        1.0.00
* @param        array $data data from the chat: name, email
* @return       void
*
*/
function wplc_sn_notification($data) {

    $wplc_sn_settings = get_option("WPLC_SN_SETTINGS");
    if ($wplc_sn_settings['wplc_enable_sn_notifications'] == 1) {

        $webhook = stripslashes($wplc_sn_settings['wplc_enable_sn_webhook']);

        if (!$webhook) { return; }
        if (isset($wplc_sn_settings['wplc_enable_sn_channel'])) { $room = stripslashes($wplc_sn_settings['wplc_enable_sn_channel']); } else { $room = "general"; }
        if (isset($wplc_sn_settings['wplc_enable_sn_icon'])) { $icon = stripslashes($wplc_sn_settings['wplc_enable_sn_icon']); } else { $icon = ":speaker:"; }

        wplc_slack(
            sprintf(__("Incoming chat from *%s* (Email: *%s*).\n<%s|Click here> to answer the chat.","wp-live-chat-support-slack-notifications"),
                $data['name'],
                $data['email'],
                admin_url( 'admin.php?page=wplivechat-menu')

                ),
            $room,
            $icon,
            $webhook
        );
    }

}
/**
* Notify the slack channel of a missed chat
*
* @since        1.0.00
* @param        array $data data from the chat: name
* @return       void
*
*/
function wplc_sn_notification_missed($data) {
    if (!$data) { return; }
    $wplc_sn_settings = get_option("WPLC_SN_SETTINGS");
    if ($wplc_sn_settings['wplc_enable_sn_missed_notifications'] == 1) {

        /* get chat details */

        $webhook = stripslashes($wplc_sn_settings['wplc_enable_sn_webhook']);
        if (!$webhook) { return; }
        if (isset($wplc_sn_settings['wplc_enable_sn_channel'])) { $room = stripslashes($wplc_sn_settings['wplc_enable_sn_channel']); } else { $room = "general"; }
        if (isset($wplc_sn_settings['wplc_enable_sn_icon'])) { $icon = stripslashes($wplc_sn_settings['wplc_enable_sn_icon']); } else { $icon = ":speaker:"; }


        wplc_slack(
            sprintf(__("The following chat was not answered:\nName: *%s* \nEmail: *%s*\n\n<%s|Click here> to view more information.","wp-live-chat-support-slack-notifications"),
                $data['name'],
                $data['email'],
                admin_url( 'admin.php?page=wplivechat-menu-missed-chats')

                ),
            $room,
            $icon,
            $webhook
        );
    }

}



/**
* Notify the slack channel of an offline message (only available to users on the WP Live Chat Pro add-on) - Checks for this are done in the "wplc_slack" function
*
* @since        1.0.00
* @param        array $data data from the chat: name, email, url, message
* @return       void
*
*/
function wplc_sn_notification_offline($data) {

    
    $wplc_sn_settings = get_option("WPLC_SN_SETTINGS");
    if ($wplc_sn_settings['wplc_enable_sn_offline_notifications'] == 1) {

        $webhook = stripslashes($wplc_sn_settings['wplc_enable_sn_webhook']);
        if (!$webhook) { return; }
        if (isset($wplc_sn_settings['wplc_enable_sn_channel'])) { $room = stripslashes($wplc_sn_settings['wplc_enable_sn_channel']); } else { $room = "general"; }
        if (isset($wplc_sn_settings['wplc_enable_sn_icon'])) { $icon = stripslashes($wplc_sn_settings['wplc_enable_sn_icon']); } else { $icon = ":speaker:"; }

        wplc_slack(
            sprintf(__("New live chat offline message received from %s\nName: *%s* \nEmail: *%s*\nMessage: *%s*\n\n<%s|Click here> to view more information.","wp-live-chat-support-slack-notifications"),
                $data['url'],
                $data['name'],
                $data['email'],
                $data['msg'],
                admin_url( 'admin.php?page=wplivechat-menu-offline-messages')

                ),
            $room,
            $icon,
            $webhook
        );
    }

}

/**
* Check if if the version of WP Live Chat Support is correct for this add-on.
*
* @since        1.0.0
* @param       
* @return       void
*
*/
function wplc_sn_check_main_version_number() {
    global $wplc_version;
    if (isset($wplc_version)) {
        if (intval(str_replace(".","",$wplc_version)) < 5012) {
            echo "<div class='error'>";
            echo "<p>".sprintf( __( '<strong><a href="%1$s" title="Update WP Live Chat Support">Please update your version of WP Live Chat Support</strong></a> for the <strong>WP Live Chat Support - Slack Notifications</strong> add-on to work.', 'wp-live-chat-support-slack-notifications' ),
                "plugin-install.php?tab=search&s=wp+live+chat+support"
                )."</p>";
            echo "</div>";

        }
    }
        
        
    
}

/**
* Check if WP Live Chat Support is installed and active
*
* @since        1.0.0
* @param       
* @return       void
*
*/
function wplc_sn_check_if_plugins_active() {
    if (!is_plugin_active('wp-live-chat-support/wp-live-chat-support.php')) {
        
        echo "<div class='error'>";
        echo "<p>".sprintf( __( '<strong><a href="%1$s" title="Install WP Live Chat Support">WP Live Chat Support</strong></a> is required for the <strong>WP Live Chat Support - Slack Notifications</strong> add-on to work. Please install and activate it.', 'wp-live-chat-support-slack-notifications' ),
            "plugin-install.php?tab=search&s=wp+live+chat+support"
            )."</p>";
        echo "</div>";
        
    }

}

/**
* Check if this is the first time the user has run the plugin. If yes, set the default settings
*
* @since       	1.0.0
* @param       
* @return		void
*
*/
function wplc_sn_first_run_check() {

	if (!get_option("WPLC_SN_FIRST_RUN")) {
		/* set the default settings */
        $wplc_sn_data['wplc_enable_sn_notifications'] = 0;
        $wplc_sn_data['wplc_enable_sn_channel'] = "general";
        $wplc_sn_data['wplc_enable_sn_icon'] = ":speaker:";
        $wplc_sn_data['wplc_enable_sn_webhook'] = false;
      

        update_option('WPLC_SN_SETTINGS', $wplc_sn_data);
        update_option("WPLC_SN_FIRST_RUN",true);
	}
}





/**
* Latch onto the default POST handling when saving live chat settings
*
* @since        1.0.0
* @param       
* @return       void
*
*/
function wplc_sn_save_settings() {
    if (isset($_POST['wplc_save_settings'])) {
        if (isset($_POST['wplc_enable_sn_notifications'])) {
            $wplc_sn_data['wplc_enable_sn_notifications'] = esc_attr($_POST['wplc_enable_sn_notifications']);
        } else {
            $wplc_sn_data['wplc_enable_sn_notifications'] = 0;
        }
        if (isset($_POST['wplc_enable_sn_missed_notifications'])) {
            $wplc_sn_data['wplc_enable_sn_missed_notifications'] = esc_attr($_POST['wplc_enable_sn_missed_notifications']);
        } else {
            $wplc_sn_data['wplc_enable_sn_missed_notifications'] = 0;
        }
        if (isset($_POST['wplc_enable_sn_offline_notifications'])) {
            $wplc_sn_data['wplc_enable_sn_offline_notifications'] = esc_attr($_POST['wplc_enable_sn_offline_notifications']);
        } else {
            $wplc_sn_data['wplc_enable_sn_offline_notifications'] = 0;
        }
        if (isset($_POST['wplc_enable_sn_channel'])) {
            $wplc_sn_data['wplc_enable_sn_channel'] = esc_attr($_POST['wplc_enable_sn_channel']);
        }
        if (isset($_POST['wplc_enable_sn_icon'])) {
            $wplc_sn_data['wplc_enable_sn_icon'] = esc_attr($_POST['wplc_enable_sn_icon']);
        }
        if (isset($_POST['wplc_enable_sn_webhook'])) {
            $wplc_sn_data['wplc_enable_sn_webhook'] = esc_attr($_POST['wplc_enable_sn_webhook']);
        }
        update_option('WPLC_SN_SETTINGS', $wplc_sn_data);

    }
}

/**
 * Add Settings Tab Heading
 *
 * @since        1.0.02
 * @param       
 * @return       void
*/
function wplc_sn_settings_tab_heading($tab_array){
    $tab_array['wplc_tab_sn'] = array(
      "href" => "#wplc_tab_sn",
      "icon" => 'fa fa-slack',
      "label" => __("Slack Notifications","wp-live-chat-support-slack-notifications")
    );
    return $tab_array;
}

/**
 * Add Settings Tab Content
 *
 * @since        1.0.02
 * @param       
 * @return       void
*/
function wplc_sn_settings_tab_content(){
    echo "<div id='wplc_tab_sn'>";
    echo wplc_sn_settings();
    echo "</div>";
}

/**
* Display the settings section
*
* @since        1.0.0
* @param       
* @return       void
*
*/
function wplc_sn_settings() {
    $wplc_sn_settings = get_option("WPLC_SN_SETTINGS");
    $content = "<h3>" . __("Chat Notification Settings","wp-live-chat-support-slack-notifications") . ":</h3>";
    $content .= "<p><strong>".sprintf( __( 'Please ensure you have enabled <a href="%1$s" target="_BLANK">Incoming Webhooks</a> for this to work.', 'wp-live-chat-support-slack-notifications' ),
                "https://my.slack.com/services/new/incoming-webhook/"
                )."</strong></p>";
    $content .= "<table class='form-table' width='700'>";
    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Notify me of new chats:","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type=\"checkbox\" value=\"1\" name=\"wplc_enable_sn_notifications\" ";
    if(isset($wplc_sn_settings['wplc_enable_sn_notifications'])  && $wplc_sn_settings['wplc_enable_sn_notifications'] == 1 ) { $content .= "checked"; }
    $content .= " />";
    $content .= "      </td>";
    $content .= "  </tr>";

    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Notify me of missed chats:","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type=\"checkbox\" value=\"1\" name=\"wplc_enable_sn_missed_notifications\" ";
    if(isset($wplc_sn_settings['wplc_enable_sn_missed_notifications'])  && $wplc_sn_settings['wplc_enable_sn_missed_notifications'] == 1 ) { $content .= "checked"; }
    $content .= " />";
    $content .= "      </td>";
    $content .= "  </tr>";

    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Notify me of offline messages:","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type=\"checkbox\" value=\"1\" name=\"wplc_enable_sn_offline_notifications\" ";
    if(isset($wplc_sn_settings['wplc_enable_sn_offline_notifications'])  && $wplc_sn_settings['wplc_enable_sn_offline_notifications'] == 1 ) { $content .= "checked"; }
    $content .= " />";
    $content .= "      </td>";
    $content .= "  </tr>";
    
    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Slack Channel","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type='text' name=\"wplc_enable_sn_channel\" value='";
    if(isset($wplc_sn_settings['wplc_enable_sn_channel'])) { $content .= stripslashes($wplc_sn_settings['wplc_enable_sn_channel']); }
    $content .= "' />";
    $content .= " <small><em> ".__("(excluding the \"#\")","wp-live-chat-support-slack-notifications")."</em></small>";
    $content .= "      </td>";
    $content .= "  </tr>";
    
    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Slack WebHook","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type='text' id=\"wplc_enable_sn_webhook\" name=\"wplc_enable_sn_webhook\" value='";
    if(isset($wplc_sn_settings['wplc_enable_sn_webhook'])) { $content .= stripslashes($wplc_sn_settings['wplc_enable_sn_webhook']); }
    $content .= "' />";
    $content .= " <small><em>".sprintf( __( '<a href="%1$s" target="_BLANK">Where do I find this?</a>', 'wp-live-chat-support-slack-notifications' ),
                "https://api.slack.com/incoming-webhooks"
                )."</em></small>";
    $content .= "      </td>";
    $content .= "  </tr>";


    $content .= "  <tr>";
    $content .= "      <td width='400' valign='top'>".__("Slack Icon","wp-live-chat-support-slack-notifications")."</td>";
    $content .= "      <td>";
    $content .= "          <input type='text' name=\"wplc_enable_sn_icon\" value='";
    if(isset($wplc_sn_settings['wplc_enable_sn_icon'])) { $content .= stripslashes($wplc_sn_settings['wplc_enable_sn_icon']); }
    $content .= "' />";
    $content .= " <small><em>".sprintf( __( '<a href="%1$s" target="_BLANK">Click here for a complete list of available icons.</a>', 'wp-live-chat-support-slack-notifications' ),
                "http://www.emoji-cheat-sheet.com/"
                )."</em></small>";    
    $content .= "      </td>";
    $content .= "  </tr>";

   $content .= "</table>";

   return $content;
}

/**
* Register and enqueue the admin JS to control the webhook URL and ensure it starts with "//"
*
* @since        1.0.0
* @param       
* @return       void
*
*/
function wplc_sn_admin_js() {
    if (isset($_GET['page']) && $_GET['page'] == "wplivechat-menu-settings") {
        global $wplc_sn_version;
        wp_register_script( 'wplc-sn-admin', plugins_url('js/wplc_sn_admin.js', __FILE__), false, $wplc_sn_version );
        wp_enqueue_script( 'wplc-sn-admin' );
    }
}