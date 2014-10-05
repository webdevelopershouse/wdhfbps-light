<?php
/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder Light
* Version                 : 1.0
* File                    : wdhfbps.admin.php
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Light Admin Forms Class.
*/

if (!class_exists("WDHFormBuilderwithPaymentSystemAdmin")){
        class WDHFormBuilderwithPaymentSystemAdmin{
            
            private $WDHFBPS_Display;
            private $WdhEditFieldDb;
            private $wdhLibs;

            function WDHFormBuilderwithPaymentSystemAdmin(){// Constructor.
                global $wdhPluginIsStart;
                
                if (is_admin()){
                    
                    if (class_exists("WdhEditFieldDb")){
                        $this->WdhEditFieldDb = new WdhEditFieldDb();
                    }
                    
                    if (class_exists("wdhLibs")){
                        $this->wdhLibs = new wdhLibs();
                    }
                    
                    if ($this->isPageOk()){
                        $this->WDHFBPS_Display = new WDHFBPSDisplay();
                        
                        add_action('admin_enqueue_scripts', array(&$this, 'addCSS'));
                        add_action('admin_enqueue_scripts', array(&$this, 'addJS'));
                    }
                    
                    $this->startAdmin();
                    
                    // Add Shortcode Buttons
                    if (!current_user_can('edit_forms') 
                            && !current_user_can('edit_pages')){
                        return;
                    }

                    if (get_user_option('rich_editing') == 'true'){
                        if (!has_action( 'admin_head', array (&$this, 'addTinyMCEJSData') )) {
                            add_action('admin_head', array (&$this, 'addTinyMCEJSData'));
                        }
                        add_filter('mce_external_plugins', array (&$this, 'createPlugin'), 5);
                        add_filter('mce_buttons', array (&$this, 'addButton'), 5);
                    }
                }
                
            }
            
            // TinyMCE Plugin
            function addTinyMCEJSData(){
                $dataJS = array();
                $wdhfbpsData = array();
                
                $wdhfbpsData['title'] = WDHFBPS_TITLE;
                $wdhfbpsData['url'] = WDHFBPS_URL;
                
                array_push($dataJS, '<script type="text/javascript">');
                array_push($dataJS, "   if (typeof WDHFBPS_PLUGIN == 'undefined') {");
                array_push($dataJS, "       var WDHFBPS_PLUGIN = '".json_encode($wdhfbpsData)."';");
                array_push($dataJS, "   }");
                array_push($dataJS, '</script>');
                
                echo implode("", $dataJS);
            }
            
            function createPlugin($plugin){
                
                $plugin['WDHFBPS'] =  WDHFBPS_URL.'stuff/js/wdhfbps.shortcode.plugin.js';
                
                return $plugin;
            }
            
            function addButton($buttons){
                array_push($buttons, '', 'WDHFBPS');
                
                return $buttons;
            }
            
            function addCSS(){
                // Register Styles.
                wp_register_style('WDHFBPS_WDHFormBuilderwithPaymentSystem_CSS', plugins_url('stuff/css/wdh.im.FBPS.css', __FILE__));                
                wp_register_style('WDHFBPS_Google_Fonts', 'http://fonts.googleapis.com/css?family=Quattrocento+Sans:normal400,italic400,bold700,bold700italic');          
                
                // Enqueue Styles.
                wp_enqueue_style('WDHFBPS_WDHFormBuilderwithPaymentSystem_CSS');
                wp_enqueue_style('WDHFBPS_Google_Fonts');
            }
            
            function addJS(){
                // Register JavaScript.
                wp_register_script('WDHFBPS_Forms', plugins_url('stuff/js/jquery.wdh.im.FBPS.Forms.js', __FILE__), array('jquery'));
                wp_register_script('WDHFBPS_JSON2', plugins_url('wdhedflwl/js/json2.js', __FILE__), array('jquery'));
                wp_register_script('WDHFBPS_BACKENDJS', plugins_url('stuff/js/wdhfbps-admin.js', __FILE__), array('jquery'));
                
                // Enqueue JavaScript.
                if (!wp_script_is('jquery', 'queue')){
                    wp_enqueue_script('jquery');
                }
                
                if (!wp_script_is('jquery-ui-sortable', 'jquery')){
                    wp_enqueue_script('jquery-ui-sortable');
                }
                
                if (!wp_script_is('jquery-ui-resizable', 'jquery')){
                    wp_enqueue_script('jquery-ui-resizable');
                }
                
                wp_enqueue_script('WDHFBPS_Forms');
                wp_enqueue_script('WDHFBPS_JSON2');
                wp_enqueue_script('WDHFBPS_BACKENDJS');                
            }
            
            function startAdmin(){// Admin start.
                $this->defConstants();
                
                if (is_admin()){
                    if ($this->isPageOk()){
                        $this->createTables();
                    }
                }
            }
            
            // Pages            
            function displayFormsPage(){// Prints out the settings page.
                $this->WDHFBPS_Display->Forms();
            }
            
            function defConstants(){// Constants define.
                global $wpdb;

                // Paths
                if (!defined('WDHFBPS_Path')) {
                    define('WDHFBPS_Path', ABSPATH.'wp-content/plugins/wdhfbps/');
                }
                
                if (!defined('WDHFBPS_URL')) {
                    define('WDHFBPS_URL', WP_PLUGIN_URL.'/wdhfbps/');
                }
                
                // Tables
                if (!defined('WDHFBPS_Forms_table')) { // Forms
                    define('WDHFBPS_Forms_table', $wpdb->prefix.'wdhfbps_forms');
                }
                
                if (!defined('WDHFBPS_Forms_records_table')) { // Forms Fields
                    define('WDHFBPS_Forms_records_table', $wpdb->prefix.'wdhfbps_forms_records');
                }
                
                if (!defined('WDHFBPS_Forms_fields_table')) { // Forms Fields
                    define('WDHFBPS_Forms_fields_table', $wpdb->prefix.'wdhfbps_forms_fields');
                }
                
                if (!defined('WDHFBPS_Forms_fields_values_table')) { // Forms Fields Values
                    define('WDHFBPS_Forms_fields_values_table', $wpdb->prefix.'wdhfbps_forms_fields_values');
                }
                
                if (!defined('WDHFBPS_Forms_Submits_table')) { // Forms Submits
                    define('WDHFBPS_Forms_Submits_table', $wpdb->prefix.'wdhfbps_forms_submits');
                }
                
                if (!defined('WDHFBPS_Users_table')) { // Users
                    define('WDHFBPS_Users_table', $wpdb->prefix.'wdhfbps_users');
                }
                
            }
            
            function isPageOk(){// Valid Admin Page.
                if (isset($_GET['page'])){
                    if ($_GET['page'] == 'wdhfbps'){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            
            function createTables(){// Tables init.
                $db_version = get_option('WDHFBPS_db_version');
                global $wdhFBPS_CONFIG;
                
                if ($wdhFBPS_CONFIG['plugin_version'] != $db_version){
                    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
               
                    $sql_forms = "CREATE TABLE " . WDHFBPS_Forms_table . " (
                                        id int NOT NULL AUTO_INCREMENT,
                                        form_width INT DEFAULT '".$wdhFBPS_CONFIG['FORM_WIDTH']."' NOT NULL,
                                        name VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_NAME']."' COLLATE utf8_unicode_ci NOT NULL,
                                        display_type VARCHAR(10) DEFAULT '".$wdhFBPS_CONFIG['FORM_DISPLAY_TYPE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        mode VARCHAR(10) DEFAULT '".$wdhFBPS_CONFIG['FORM_MODE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        design VARCHAR(10) DEFAULT '".$wdhFBPS_CONFIG['FORM_DESIGN']."' COLLATE utf8_unicode_ci NOT NULL,
                                        popup_button VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_POPUP_BUTTON']."' COLLATE utf8_unicode_ci NOT NULL,
                                        class VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_CLASS']."' COLLATE utf8_unicode_ci NOT NULL,
                                        css TEXT DEFAULT '".$wdhFBPS_CONFIG['FORM_CSS']."' COLLATE utf8_unicode_ci NOT NULL,
                                        msg_sent VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_MESSAGES_SUCCESFULL']."' COLLATE utf8_unicode_ci NOT NULL,
                                        msg_failed VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_MESSAGES_FAILED']."' COLLATE utf8_unicode_ci NOT NULL,
                                        msg_class VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_MESSAGES_CLASS']."' COLLATE utf8_unicode_ci NOT NULL,
                                        msg_css VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_MESSAGES_CSS']."' COLLATE utf8_unicode_ci NOT NULL,
                                        sender_email VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_SENDER_EMAIL']."' COLLATE utf8_unicode_ci NOT NULL,
                                        admin_email_notification VARCHAR(6) DEFAULT '".$wdhFBPS_CONFIG['FORM_ADMIN_EMAIL_NOTIFICATION']."' COLLATE utf8_unicode_ci NOT NULL,
                                        admin_email VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_ADMIN_EMAIL']."' COLLATE utf8_unicode_ci NOT NULL,
                                        admin_subject VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_ADMIN_SUBJECT']."' COLLATE utf8_unicode_ci NOT NULL,
                                        admin_email_template TEXT COLLATE utf8_unicode_ci NOT NULL,
                                        user_email_notification VARCHAR(6) DEFAULT '".$wdhFBPS_CONFIG['FORM_USER_EMAIL_NOTIFICATION']."' COLLATE utf8_unicode_ci NOT NULL,
                                        user_email_template TEXT COLLATE utf8_unicode_ci NOT NULL,
                                        user_email_subject VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_USER_EMAIL_SUBJECT']."' COLLATE utf8_unicode_ci NOT NULL,
                                        use_smtp VARCHAR(6) DEFAULT '".$wdhFBPS_CONFIG['FORM_USE_SMTP']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_host VARCHAR(16) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_HOST']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_port VARCHAR(16) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_PORT']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_email VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_EMAIL']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_username VARCHAR(16) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_USERNAME']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_password VARCHAR(16) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_PASSWORD']."' COLLATE utf8_unicode_ci NOT NULL,
                                        smtp_ssl_connection VARCHAR(16) DEFAULT '".$wdhFBPS_CONFIG['FORM_SMTP_SSL_CONNECTION']."' COLLATE utf8_unicode_ci NOT NULL,
                                        user_role VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_USER_ROLE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_font_family VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_FONT_FAMILY']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_own_font VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_OWN_FONT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_FONT_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_style VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_FONT_STYLE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_align VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_ALIGN']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_weight VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_TEXT_FONT_WEIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_MARGIN_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_MARGIN_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_MARGIN_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_MARGIN_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_PADDING_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_PADDING_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_PADDING_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_PADDING_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_background_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_BACKGROUND_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_BORDER_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_BORDER_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_border_type VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_BORDER_TYPE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_radius INT DEFAULT '".$wdhFBPS_CONFIG['FORM_BOX_BORDER_RADIUS']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        js_wdhedfp_after_save TEXT DEFAULT '".$wdhFBPS_CONFIG['FORM_JS_HOOK_AFTER_SAVE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        datac datetime NOT NULL,
                                        UNIQUE KEY id (id)
                                    );";
                    
                    $sql_forms_records = "CREATE TABLE " . WDHFBPS_Forms_records_table . " (
                                        id int NOT NULL AUTO_INCREMENT,
                                        cat_id int DEFAULT 0 NOT NULL,
                                        datac datetime NOT NULL,
                                        UNIQUE KEY id (id)
                                    );";
                  
                    $sql_forms_fields = "CREATE TABLE " . WDHFBPS_Forms_fields_table . " (
                                        id int NOT NULL AUTO_INCREMENT,
                                        cat_id int DEFAULT 0 NOT NULL,
                                        name TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        second_name TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        edit_type VARCHAR(20) DEFAULT 'text' COLLATE utf8_unicode_ci NOT NULL,
                                        width int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_WIDTH']."' NOT NULL,
                                        height int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_HEIGHT']."' NOT NULL,
                                        label_width int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH']."' NOT NULL,
                                        label_height int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT']."' NOT NULL,
                                        input_width int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH']."' NOT NULL,
                                        input_height int DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT']."' NOT NULL,
                                        zoom int DEFAULT 12 NOT NULL,
                                        is_required VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_email VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_url VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_phone VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_alpha VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_numeric VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_alphanumeric VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_date VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_unique VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        is_adult_video VARCHAR(6) DEFAULT 'false' COLLATE utf8_unicode_ci NOT NULL,
                                        values_list TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        display_type VARCHAR(16) DEFAULT 'in_content' COLLATE utf8_unicode_ci NOT NULL, 
                                        display_position INT(9) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
                                        display_label_position INT(9) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
                                        display_input_position INT(9) DEFAULT 0 COLLATE utf8_unicode_ci NOT NULL,
                                        label_value TEXT DEFAULT '' DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        label_link VARCHAR(200) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        label_class VARCHAR(256) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        label_css TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        input_class VARCHAR(256) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        input_css TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        input_values VARCHAR(256) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        tooltip_text TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        text_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_font_family VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_FAMILY']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_own_font VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_OWN_FONT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_style VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_STYLE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_align VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_ALIGN']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_font_weight VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_margin_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_padding_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_background_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_BACKGROUND_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_border_type VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_TYPE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_border_radius INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_RADIUS']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_label_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_label_font_family VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_FAMILY']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_label_own_font VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_OWN_FONT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_label_font_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_label_font_style VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_STYLE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_label_align VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_label_font_weight VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_WEIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_margin_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_margin_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_margin_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_margin_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_padding_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_padding_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_padding_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_padding_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_background_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BACKGROUND_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_label_border_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_label_border_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_label_border_type VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_TYPE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_label_border_radius INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_RADIUS']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        text_input_font_family VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_FAMILY']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_own_font VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_OWN_FONT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_font_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_font_style VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_STYLE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_align VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        text_input_font_weight VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_WEIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_margin_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_margin_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_margin_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_margin_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_padding_left INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_LEFT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_padding_right INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_RIGHT']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_padding_top INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_TOP']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_padding_bottom INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_BOTTOM']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_background_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_input_border_color VARCHAR(7) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_COLOR']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_input_border_size INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        box_input_border_type VARCHAR(256) DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_TYPE']."' COLLATE utf8_unicode_ci NOT NULL,
                                        box_input_border_radius INT DEFAULT '".$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_RADIUS']."' COLLATE utf8_unicode_ci NOT NULL,    
                                        datac datetime NOT NULL,
                                        UNIQUE KEY id (id)
                                    );";
                    
                    $sql_forms_fields_values = "CREATE TABLE " . WDHFBPS_Forms_fields_values_table . " (
                                        id int NOT NULL AUTO_INCREMENT,
                                        field_id int DEFAULT 0 NOT NULL,
                                        cat_id int DEFAULT 0 NOT NULL,
                                        customer_id int DEFAULT 0 NOT NULL,
                                        post_id int DEFAULT 0 NOT NULL,
                                        value TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        datac datetime NOT NULL,
                                        UNIQUE KEY id (id)
                                    );";
                    
                    $sql_users = "CREATE TABLE " . WDHFBPS_Users_table . " (
                                        id int NOT NULL AUTO_INCREMENT,
                                        ref_id int DEFAULT 0 NOT NULL,
                                        user_id int DEFAULT 0 NOT NULL,
                                        form_id int DEFAULT 0 NOT NULL,
                                        username TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        email TEXT DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                        usertype VARCHAR(120) DEFAULT 'subscriber' COLLATE utf8_unicode_ci NOT NULL,
                                        amount VARCHAR(25) DEFAULT '0' COLLATE utf8_unicode_ci NOT NULL,
                                        expiration_date date NOT NULL,
                                        UNIQUE KEY id (id)
                                    );";
                    
                    dbDelta($sql_forms);
                    dbDelta($sql_forms_records);
                    dbDelta($sql_forms_fields);
                    dbDelta($sql_forms_fields_values);
                    dbDelta($sql_users);
                    
                    if ($db_version != $wdhFBPS_CONFIG['plugin_version']){
                        
                       if ($db_version == "") {
                            add_option('WDHFBPS_db_version', $wdhFBPS_CONFIG['plugin_version']);
                            $this->createForm();
                        }
                        else{
                            update_option('WDHFBPS_db_version', $wdhFBPS_CONFIG['plugin_version']);
                        }
                    }
                }
                
            }
            
            function createForm(){
                global $wpdb, $wdhFBPS_CONFIG;
                
                $form = $wpdb->get_row('SELECT id,name FROM '.WDHFBPS_Forms_table.' WHERE id="1"');
                
                if ($wpdb->num_rows < 1){
                    // Add Form
                    $formData = array('name' => WDHFBPS_FBPS_FORM,
                                      'admin_email_template' => $wdhFBPS_CONFIG['FORM_ADMIN_EMAIL_TEMPLATE'],
                                      'user_email_template' => $wdhFBPS_CONFIG['FORM_USER_EMAIL_TEMPLATE']);
                    $wpdb->insert(WDHFBPS_Forms_table,$formData);
                    
                    // Fields 
                    // ------------------------------------------
                    
                    $valueList = $this->generateFieldJSON(WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_VALUES);
                    $titleParagraph = $this->generateFieldJSON(WDHFBPS_FBPS_CATEGORY_FIELD_DEFAULT_TEXT);
                    
                    // ------------------------------------------
                    // Name Field
                    $nameTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CUSTOMER_NAME);
                    $datac = date('Y-m-d H:i:s');
                    $lastPosition = $this->getLastPosition();
                    $width = $wdhFBPS_CONFIG['FORM_FIELD_WIDTH'];
                    $height = $wdhFBPS_CONFIG['FORM_FIELD_HEIGHT'];
                    $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                    $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                    $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                    $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                    $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                    $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                    $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                    $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                    $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                    $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                    $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                    $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                    
                    $nameData = array('name' => $nameTranslation,
                                    'cat_id' => '1',
                                    'values_list' => $valueList,
                                    'display_position' => $lastPosition,
                                    'datac' => $datac,
                                    'edit_type' => 'text',
                                    'is_required' => 'true',
                                    'is_email' => 'false',
                                    'is_url' => 'false',
                                    'is_phone' => 'false',
                                    'is_alpha' => 'false',
                                    'is_numeric' => 'false',
                                    'is_alphanumeric' => 'false',
                                    'is_unique' => 'false',
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'label_value' => $titleParagraph);
                    
                    $wpdb->insert(WDHFBPS_Forms_fields_table, $nameData) or die(mysql_error());
                    // ------------------------------------------
                    // Email Field
                    $emailTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CUSTOMER_EMAIL);
                    $datac = date('Y-m-d H:i:s');
                    $lastPosition = $this->getLastPosition();
                    $width = $wdhFBPS_CONFIG['FORM_FIELD_WIDTH'];
                    $height = $wdhFBPS_CONFIG['FORM_FIELD_HEIGHT'];
                    $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                    $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                    $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                    $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                    $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                    $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                    $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                    $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                    $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                    $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                    $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                    $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                    
                    $emailData = array('name' => $emailTranslation,
                                    'cat_id' => '1',
                                    'values_list' => $valueList,
                                    'display_position' => $lastPosition,
                                    'datac' => $datac,
                                    'edit_type' => 'text',
                                    'is_required' => 'true',
                                    'is_email' => 'true',
                                    'is_url' => 'false',
                                    'is_phone' => 'false',
                                    'is_alpha' => 'false',
                                    'is_numeric' => 'false',
                                    'is_alphanumeric' => 'false',
                                    'is_unique' => 'false',
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'label_value' => $titleParagraph);
                    
                    $wpdb->insert(WDHFBPS_Forms_fields_table, $emailData) or die(mysql_error());
                    // ------------------------------------------
                    // Phone Field
                    $phoneTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CUSTOMER_PHONE);
                    $datac = date('Y-m-d H:i:s');
                    $lastPosition = $this->getLastPosition();
                    $width = $wdhFBPS_CONFIG['FORM_FIELD_WIDTH'];
                    $height = $wdhFBPS_CONFIG['FORM_FIELD_HEIGHT'];
                    $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                    $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                    $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                    $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                    $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                    $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                    $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                    $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                    $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                    $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                    $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                    $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                    
                    $phoneData = array('name' => $phoneTranslation,
                                    'cat_id' => '1',
                                    'values_list' => $valueList,
                                    'display_position' => $lastPosition,
                                    'datac' => $datac,
                                    'edit_type' => 'text',
                                    'is_required' => 'true',
                                    'is_email' => 'false',
                                    'is_url' => 'false',
                                    'is_phone' => 'true',
                                    'is_alpha' => 'false',
                                    'is_numeric' => 'false',
                                    'is_alphanumeric' => 'false',
                                    'is_unique' => 'false',
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'label_value' => $titleParagraph);
                    
                    $wpdb->insert(WDHFBPS_Forms_fields_table, $phoneData) or die(mysql_error());
                    // ------------------------------------------
                    // Message Field
                    $messageTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CUSTOMER_MESSAGE);
                    $datac = date('Y-m-d H:i:s');
                    $lastPosition = $this->getLastPosition();
                    $width = $wdhFBPS_CONFIG['FORM_FIELD_WIDTH'];
                    $height = $wdhFBPS_CONFIG['FORM_FIELD_HEIGHT'];
                    $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                    $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                    $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                    $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                    $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                    $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                    $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                    $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                    $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                    $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                    $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                    $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                    $height = 40;
                    $label_height = 40;
                    $input_height = 40;
                    
                    
                    $messageData = array('name' => $messageTranslation,
                                    'cat_id' => '1',
                                    'values_list' => $valueList,
                                    'display_position' => $lastPosition,
                                    'datac' => $datac,
                                    'edit_type' => 'textarea',
                                    'is_required' => 'false',
                                    'is_email' => 'false',
                                    'is_url' => 'false',
                                    'is_phone' => 'false',
                                    'is_alpha' => 'false',
                                    'is_numeric' => 'false',
                                    'is_alphanumeric' => 'false',
                                    'is_unique' => 'false',
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'label_value' => $titleParagraph);
                    
                    $wpdb->insert(WDHFBPS_Forms_fields_table, $messageData) or die(mysql_error());
                    // ------------------------------------------
                    // Submit Field
                    $submitTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CUSTOMER_SUBMIT);
                    $datac = date('Y-m-d H:i:s');
                    $lastPosition = $this->getLastPosition();
                    $width = $wdhFBPS_CONFIG['FORM_FIELD_WIDTH'];
                    $height = $wdhFBPS_CONFIG['FORM_FIELD_HEIGHT'];
                    $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                    $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                    $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                    $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                    $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                    $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                    $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                    $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                    $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                    $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                    $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                    $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                    $input_border_size = 0;
                    $input_background_color = '000';
                    $input_text_color = 'fff';
                    $input_text_align = 'center';
                    $height = 25;
                    $label_height = 25;
                    $input_height = 25;
                    $width = 100;
                    $label_width = 68;
                    $input_width = 30;

                    $submitData = array('name' => $submitTranslation,
                                    'cat_id' => '1',
                                    'values_list' => $valueList,
                                    'display_position' => $lastPosition,
                                    'datac' => $datac,
                                    'edit_type' => 'submit',
                                    'is_required' => 'false',
                                    'is_email' => 'false',
                                    'is_url' => 'false',
                                    'is_phone' => 'true',
                                    'is_alpha' => 'false',
                                    'is_numeric' => 'false',
                                    'is_alphanumeric' => 'false',
                                    'is_unique' => 'false',
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'label_value' => $titleParagraph);
                    
                    $wpdb->insert(WDHFBPS_Forms_fields_table, $submitData) or die(mysql_error());
                }
            }
             
            function changeLanguage(){
                $wdhfbps_language_now = $_POST['language'];
                
                echo $wdhfbps_language_now;
                
                update_option('WDHFBPS_language', $wdhfbps_language_now);
                die();
            }
            
            // Categories
            function showCategories(){
                global $wpdb;
                
                $categoryHTML = '';
                $i = 0;
                $category = $wpdb->get_row('SELECT id,name FROM '.WDHFBPS_Forms_table.' WHERE id="1"');
                $categoryHTML = $category->id.'@@'.$category->name;
                
                echo $categoryHTML;
                die();
            }
            
            // Update Fields Position
            function updateFieldsPosition(){
               global $wpdb; 
               
               $positions = (array)$_POST['positions'];
               
               foreach($positions as $element){
                   $element = (array)$element;
                   $data = array('display_position' => $element[position]);
                   $where = array('id' => $element[id]);
                   $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);
               }
               
               echo 'success';
               die();
            }
            
            // Update Fields Position
            function updateNewFieldsPosition(){
               global $wpdb; 
               
               $positions = (array)$_POST['positions'];
               
               foreach($positions as $element){
                   $element = (array)$element;
                   $data = array('display_position' => $element['position']);
                   $where = array('id' => $element['id']);
                   $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);
               }
               
               echo 'success';
               die();
            }
            
            // Update Fields Input Position
            function updateNewFieldsInputPosition(){
               global $wpdb;
               
               $positions = (array)$_POST['positions'];
               
               $labelPosition = 0;
               $inputPosition = 1;
               $id = $_POST['field_id'];
               
               foreach($positions as $element){
                   $element = (array)$element;
                   
                   if($element['type'] == 'label'){
                       $labelPosition = $element['position'];
                   } else{
                       $inputPosition = $element['position'];
                   }
               }
               
               $data = array('display_input_position' => $inputPosition,
                             'display_label_position' => $labelPosition);
               $where = array('id' => $id);
               $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);

               echo 'success';
               die();
            }
            
            // Update Form Size
            function updateNewFormSize(){
               global $wpdb; 
               
               $sizeWidth = $_POST['width'];
               
               $data = array('form_width' => $sizeWidth);
               $where = array('id' => '1');
               $wpdb->update(WDHFBPS_Forms_table, $data, $where);
               
               echo 'success';
               die();
            }
            
             // Update Fields Size
            function updateNewFormFieldsSize(){
               global $wpdb; 
               
               $sizeWidth = $_POST['width'];
               $sizeHeight = $_POST['height'];
               $id = $_POST['field_id'];
               
               $data = array('width' => $sizeWidth,
                             'height' => $sizeHeight);
               $where = array('id' => $id);
               $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);
               
               echo 'success';
               die();
            }
            
            // Update Fields Label Size
            function updateNewFormFieldsLabelSize(){
               global $wpdb; 
               
               $sizeWidth = $_POST['width'];
               $sizeHeight = $_POST['height'];
               $id = $_POST['field_id'];
               
               $data = array('label_width' => $sizeWidth,
                             'label_height' => $sizeHeight);
               $where = array('id' => $id);
               $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);
               
               echo 'success';
               die();
            }
            
            // Update Fields Input Size
            function updateNewFormFieldsInputSize(){
               global $wpdb; 
               
               $sizeWidth = $_POST['width'];
               $sizeHeight = $_POST['height'];
               $id = $_POST['field_id'];
               
               $data = array('input_width' => $sizeWidth,
                             'input_height' => $sizeHeight);
               $where = array('id' => $id);
               $wpdb->update(WDHFBPS_Forms_fields_table, $data, $where);
               
               echo 'success';
               die();
            }
            
            // Fields
            function showFieldsSettings(){
                global $wpdb;
                
                $fields = $wpdb->get_results('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where cat_id="1" ORDER by display_position ASC, id ASC');
                $fieldsHTML = array();
                $wdhfbps_language_now = get_option('WDHFBPS_language');
                $use_shortcode        = '';
                $display_position     = '';
                $in_content           = '';
                $field_label_checkbox = '';
                $field_value_checkbox = '';
                $display_before       = '';
                $display_after        = '';
                
                if (count($fields) > 0){
                    $i=0;
                    foreach ($fields as $field){
                        
                        $use_shortcode = '';
                        $in_content    = '';
                        
                        if ($field->name){
                            
                            if ($i%2 == 1) {
                                $fieldsClass = 'wdhfbps-field1';
                            } else {
                                $fieldsClass = 'wdhfbps-field2';
                            }
                            
                            if ($field->display_type == 'use_shortcode'){
                                $use_shortcode = "selected";
                                $display_position = "display:none";
                            } else {
                                $in_content = "selected";
                                $display_position = "";
                            }
                            
                            if ($field->display_position == 'before'){
                                $display_before = "selected";
                            } else {
                                $display_after = "selected";
                            }
                            
                            $fieldDate = json_decode(stripslashes($field->name));
                            $translation = str_replace('"','#',stripslashes($field->name));
                            $fieldname = $fieldDate->$wdhfbps_language_now;
                            $fieldValuesList = '';
                            $fieldValues = json_decode(stripslashes($field->values_list));
                            $translationValues = str_replace('"','#',stripslashes($field->values_list));
                            
                            if($this->isJSON($field->values_list)){
                                $fieldValuesList = $fieldValues->$wdhfbps_language_now;
                            }
                            
                            // Tile & Paragraph
                            $fieldTitleParagraph = '';
                            $translationTitleParagraph = str_replace('"','#',stripslashes($field->label_value));
                            
                            if ($field->edit_type == 'title' || $field->edit_type == 'paragraph') {
                                if($this->isJSON($field->label_value)){
                                    $fieldTitleParagraphData = json_decode(stripslashes($field->label_value));
                                    $fieldTitleParagraph = $fieldTitleParagraphData->$wdhfbps_language_now;
                                }
                            }
                            
                            array_push($fieldsHTML, '<div class="wdhfbps-field-settings '.$fieldsClass.' wdhfbps-is-sortable" id="wdhfbps-field-'.$field->id.'">');
                            array_push($fieldsHTML, '   <div class="wdhfbps-head wdhfbps-open" id="field-title-'.$field->id.'">
                                                            <div class="wdhfbps-head-title">'.$fieldname.'</div>
                                                            <div id="field-loader-'.$field->id.'" class="wdhfbps-loader">&nbsp;</div>
                                                            <div id="field-success-'.$field->id.'" class="wdhfbps-success">[ '.WDHFBPS_FBPS_CATEGORY_FIELD_SAVED.' ]</div>
                                                        </div>');
                            array_push($fieldsHTML, '<div class="wdhfbps-content" id="field-content-'.$field->id.'">');
                            array_push($fieldsHTML, '   <div class="box">
                                                            <div class="title-box">
                                                            '.WDHFBPS_FBPS_CATEGORY_FIELD_NAME.' :
                                                            </div>
                                                            <input type="text" name="field-name-'.$field->id.'" id="field-name-'.$field->id.'" onkeyup="wdhfbpsPreviewName('.$field->id.',this.value);" onblur="wdhfbpsPreviewName('.$field->id.',this.value);" class="field-box wdhfbps-field-name" value="'.$fieldname.'" />
                                                            <input type="hidden" name="field-name-translation-'.$field->id.'" id="field-name-translation-'.$field->id.'" value="'.$translation.'" />
                                                            <input type="hidden" name="field-category-'.$field->id.'" id="field-category-'.$field->id.'" value="1" />
                                                            <input type="hidden" name="field-values-translation-'.$field->id.'" id="field-values-translation-'.$field->id.'" value="'.$translationValues.'" />
                                                            <input type="hidden" name="field-title-paragraph-translation-'.$field->id.'" id="field-title-paragraph-translation-'.$field->id.'" value="'.$translationTitleParagraph.'" />
                                                        </div>
                                                        <div class="box">
                                                            <div class="title-box">
                                                            '.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE.' :
                                                            </div>
                                                            <div class="check-box">
                                                                <select name="field-type-'.$field->id.'" id="field-type-'.$field->id.'" onchange="wdhfbpsChangeFieldType('.$field->id.',this.value);" class="field-box" style="margin-top: 2px;">
                                                                    <option disabled="disabled">'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_COMMON.'</option>
                                                                    <option value="text" '.$this->isSelectedType("text", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TEXT.'</option>
                                                                    <option value="title" '.$this->isSelectedType("title", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TITLE.'</option>
                                                                    <option value="paragraph" '.$this->isSelectedType("paragraph", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_PARAGRAPH.'</option>
                                                                    <option value="username" '.$this->isSelectedType("username", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_USERNAME.'</option> 
                                                                    <option value="password" '.$this->isSelectedType("password", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_PASSWORD.'</option>
                                                                    <option value="textarea" '.$this->isSelectedType("textarea", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_TEXTAREA.'</option>
                                                                    <option value="select" '.$this->isSelectedType("select", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_SELECT.'</option>
                                                                    <option value="radio" '.$this->isSelectedType("radio", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_RADIO.'</option>
                                                                    <option value="checkbox" '.$this->isSelectedType("checkbox", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_CHECKBOX.'</option>
                                                                    <option value="captcha" '.$this->isSelectedType("captcha", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_CAPTCHA.'</option>
                                                                    <option value="submit" '.$this->isSelectedType("submit", $field->edit_type).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_SUBMIT.'</option>
                                                                    
                                                                </select>
                                                            </div>
                                                            <div class="check-box">&nbsp;</div>
                                                        </div>
                                                        <div class="box wdhfbps-filter-selected">
                                                            <div class="title-box">
                                                            '.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER.' :
                                                            </div>
                                                            <select name="field-filter-'.$field->id.'" id="field-filter-'.$field->id.'" class="field-box">
                                                                <option value="no">'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_NO.'</option>
                                                                <option value="is_email" '.$this->filterIsSelected($field->is_email).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_EMAIL.'</option>
                                                                <option value="is_phone" '.$this->filterIsSelected($field->is_phone).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_PHONE.'</option>
                                                                <option value="is_url" '.$this->filterIsSelected($field->is_url).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_URL.'</option>
                                                                <option value="is_date" '.$this->filterIsSelected($field->is_date).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_DATE.'</option>
                                                                <option value="is_alpha" '.$this->filterIsSelected($field->is_alpha).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_ALPHA.'</option>
                                                                <option value="is_numeric" '.$this->filterIsSelected($field->is_numeric).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_NUMERIC.'</option>
                                                                <option value="is_alphanumeric" '.$this->filterIsSelected($field->is_alphanumeric).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_ALPHANUMERIC.'</option>
                                                                <option value="is_unique" '.$this->filterIsSelected($field->is_unique).'>'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_IS_UNIQUE.'</option>    
                                                            </select>
                                                        </div>
                                                        <div class="box wdhfbps-required-selected">
                                                            <div class="check-box">
                                                                <div style="width:350px; margin-left:157px; float:left; margin-bottom:5px; margin-top:10px;">
                                                                    <input type="checkbox" id="field-is-required-'.$field->id.'" '.$this->requiredIsChecked($field->is_required).' /> '.WDHFBPS_FBPS_CATEGORY_FIELD_CAN_BE_EMPTY.'
                                                                </div>
                                                            </div>
                                                        </div>');
                            
                            $styleDisplay = 'none';
                            
                            if ($field->edit_type == 'title' || $field->edit_type == 'paragraph') {
                                $styleDisplay = 'block';
                            }
                            array_push($fieldsHTML, '<div class="box wdhfbps-title-paragraph-selected" style="display:'.$styleDisplay.';">
                                                        <div class="title-box">
                                                        '.WDHFBPS_FBPS_CATEGORY_FIELD_YOUR_TEXT.' :
                                                        </div>
                                                        <textarea name="field-title-paragraph-'.$field->id.'" id="field-title-paragraph-'.$field->id.'" class="field-box" onkeyup="wdhfbpsPreviewTitleParagraph('.$field->id.',this.value);" onblur="wdhfbpsPreviewTitleParagraph('.$field->id.',this.value);" onpaste="wdhfbpsPreviewTitleParagraph('.$field->id.',this.value);">'.$fieldTitleParagraph.'</textarea>
                                                    </div>');
                            
                            $styleDisplay = 'none';
                            
                            if ($field->edit_type == 'select' || $field->edit_type == 'radio' || $field->edit_type == 'checkbox') {
                                $styleDisplay = 'block';
                            }
                            array_push($fieldsHTML, '<div class="box wdhfbps-select-radio-checkbox-selected" style="display:'.$styleDisplay.';">
                                                        <div class="title-box">
                                                        '.WDHFBPS_FBPS_CATEGORY_FIELD_VALUES_LIST.' :
                                                        </div>
                                                        <input type="text" value="'.$fieldValuesList.'" name="field-values-list-'.$field->id.'" id="field-values-list-'.$field->id.'" class="field-box" value="'.$field->values_list.'" onkeyup="wdhfbpsPreviewValues('.$field->id.',this.value);" onblur="wdhfbpsPreviewValues('.$field->id.',this.value);" placeholder="'.WDHFBPS_FBPS_CATEGORY_FIELD_FILTER_VALUES.'"/>
                                                    </div>');
                            
                            $styleDisplay = '';
                            
                            if ($field->edit_type == 'map' || $field->edit_type == 'video') {
                                $styleDisplay = 'block';
                            }
                            array_push($fieldsHTML, '<div class="box wdhfbps-map-video-selected" style="display:'.$styleDisplay.';">
                                                        <div class="title-box">
                                                        '.WDHFBPS_FBPS_CATEGORY_FIELD_WIDTH.' :
                                                        </div>
                                                        <input type="text" name="field-width-'.$field->id.'" id="field-width-'.$field->id.'" class="field-small-box" value="'.$field->width.'" /> %
                                                    </div>
                                                    <div class="box wdhfbps-map-video-selected">
                                                        <div class="title-box">
                                                        '.WDHFBPS_FBPS_CATEGORY_FIELD_HEIGHT.' :
                                                        </div>
                                                        <input type="text" name="field-height-'.$field->id.'" id="field-height-'.$field->id.'" class="field-small-box" value="'.$field->height.'" /> px
                                                    </div>');
                            
                            $styleDisplay = '';
                            
                            if ($field->edit_type == 'map') { 
                                $styleDisplay = 'block';
                            }
                            array_push($fieldsHTML, '<div class="box wdhfbps-map-selected" style="display:'.$styleDisplay.';">
                                                        <div class="title-box">
                                                        '.WDHFBPS_FBPS_CATEGORY_FIELD_ZOOM.' :
                                                        </div>
                                                        <input type="text" name="field-zoom-'.$field->id.'" id="field-zoom-'.$field->id.'" class="field-small-box" value="'.$field->zoom.'" />
                                                    </div>');
                           
                            array_push($fieldsHTML, '<div class="wdhfbps-buttons">
                                                        <input type="button" class="wdhfbps-button" id="field-submit-'.$field->id.'" onclick="wdhfbpsSaveField('.$field->id.');" value="'.WDHFBPS_FBPS_CUSTOMER_SAVE.'"/>
                                                        <input type="button" class="wdhfbps-button" id="field-delete-'.$field->id.'" onclick="wdhfbpsDeleteField('.$field->id.');" value="'.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE.'" />
                                                    </div>
                                                    ');
                            array_push($fieldsHTML, '</div>');
                            array_push($fieldsHTML, '</div>');
                            $i++;
                        }
                    }
                    
                } else {
                    array_push($fieldsHTML, '<div class="wdhfbps-field-settings" style="margin-top:10px;" id="wdhfbps-fields-no-fields-1">');
                    array_push($fieldsHTML, WDHFBPS_FBPS_CUSTOMER_NO_FIELDS);
                    array_push($fieldsHTML, '</div>');
                }
                
                return implode('',$fieldsHTML);
            }
            
            function isJSON($string){
                json_decode($string);
                return (json_last_error() == JSON_ERROR_NONE);
            }
            
            // Is Selected Type
            function isSelectedType($type, $value){
                $isSelected = '';
                
                if ($type == $value){
                    $isSelected = 'selected="selected"';
                }
                
                return $isSelected;
            }
            
            // Is Checked Filter
            function filterIsChecked($value){
                $isChecked = '';
                
                if ($value == 'true'){
                    $isChecked = 'checked="checked"';
                }
                
                return $isChecked;
            }
            
            // Is Checked Filter
            function requiredIsChecked($value){
                $isChecked = '';
                
                if ($value == 'false'){
                    $isChecked = 'checked="checked"';
                }
                
                return $isChecked;
            }
            
            // Is Selected Filter
            function filterIsSelected($value){
                $isSelected = '';
                
                if ($value == 'true'){
                    $isSelected = 'selected="selected"';
                }
                
                return $isSelected;
            }
            // Display Forms
            function displayForms(){
                echo $this->showForms();
                
                die();
            }
            
            function saveField(){
                global $wpdb;
                global $wdhFBPS_CONFIG;
                $fieldID = $_POST['id'];
                $nameTranslation = str_replace('\\','',$_POST['name']);
                $nameTranslation = str_replace('#','"',$nameTranslation);
                $valueList = $_POST['values_list'];
                $valueList = str_replace('\\','',$valueList);
                $valueList = str_replace('#','"',$valueList);
                $titleParagraph = $_POST['title_paragraph'];
                $titleParagraph = str_replace('\\','',$titleParagraph);
                $titleParagraph = str_replace('#','"',$titleParagraph);
                $width = $_POST['width'];
                $height = $_POST['height'];
                $label_height = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT'];
                $input_height = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT'];
                $label_width = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH'];
                $input_width = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH'];
                $input_border_size = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE'];
                $input_background_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR'];
                $input_text_color = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR'];
                $input_text_align = $wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN'];
                $label_text_align = $wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN'];
                $label_text_font_size = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE'];
                $label_text_font_weight = $wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT'];
                $field_margin_bottom = $wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM'];
                
                if ($_POST['edit_type'] == 'textarea'){
                    
                    if ($height < 40) {
                        $height = 40;
                        $label_height = 40;
                        $input_height = 40;
                    }
                }
                
                if ($_POST['edit_type'] == 'radio' || $_POST['edit_type'] == 'checkbox' || $_POST['edit_type'] == 'title' || $_POST['edit_type'] == 'paragraph'){
                    $input_border_size = 0;
                    $input_background_color = 'inherit';
                }
                
                if ($_POST['edit_type'] == 'title'){
                    $label_text_font_size = 18;
                    $label_text_font_weight = 'bold';
                }
                
                if ($_POST['edit_type'] == 'paragraph'){
                    $label_text_font_size = 14;
                }
                
                if ($_POST['edit_type'] == 'captcha'){
                    $input_width = 15;
                }
                
                if ($_POST['edit_type'] == 'submit'){
                    $input_border_size = 0;
                    $input_background_color = '000';
                    $input_text_color = 'fff';
                    $input_text_align = 'center';
                    $height = 25;
                    $label_height = 25;
                    $input_height = 25;
                    $width = 100;
                    $label_width = 68;
                    $input_width = 30;
                }
                
                $data_array = array('name' => $nameTranslation,
                                    'edit_type' => $_POST['edit_type'],
                                    'is_required' => $_POST['is_required'],
                                    'is_email' => $_POST['is_email'],
                                    'is_url' => $_POST['is_url'],
                                    'is_phone' => $_POST['is_phone'],
                                    'is_alpha' => $_POST['is_alpha'],
                                    'is_numeric' => $_POST['is_numeric'],
                                    'is_alphanumeric' => $_POST['is_alphanumeric'],
                                    'is_unique' => $_POST['is_unique'],
                                    'width' => $width,
                                    'height' => $height,
                                    'label_height' => $label_height,
                                    'input_height' => $label_height,
                                    'label_width' => $label_width,
                                    'input_width' => $input_width,
                                    'box_input_border_size' => $input_border_size,
                                    'box_input_background_color' => $input_background_color,
                                    'box_margin_bottom' => $field_margin_bottom,
                                    'text_input_color' => $input_text_color,
                                    'text_label_font_weight' => $label_text_font_weight,
                                    'text_label_font_size' => $label_text_font_size,
                                    'text_input_align' => $input_text_align,
                                    'text_label_align' => $label_text_align,
                                    'zoom' => $_POST['zoom'],
                                    'label_value' => $titleParagraph,
                                    'values_list' => $valueList
                                    );
                $where = array('id' => $fieldID);
                $wpdb->update( WDHFBPS_Forms_fields_table, $data_array, $where );
                
                echo 'success';
                die();
            }
            
            function deleteField(){
                global $wpdb;
                $fieldId = $_POST['id'];
                
                // Delete Forms
                $wpdb->query("DELETE FROM ".WDHFBPS_Forms_fields_table." WHERE id = '".$fieldId."' ");
                // Delete Fields Value
                $wpdb->query("DELETE FROM ".WDHFBPS_Forms_fields_values_table." WHERE field_id = '".$fieldId."' ");
                echo 'success';
                die();
            }
            
            function generateFieldJSON($value){
                $languages = array();
                $field = array();
                $languages = $this->wdhLibs->getLanguagesArray();
                
                foreach ($languages as $language){
                    $field[$language] = $value;
                }
                
                return json_encode($field);
            }
            
            
            function generateEmptyFieldJSON(){
                $languages = array();
                $field = array();
                $languages = $this->wdhLibs->getLanguagesArray();
                
                foreach ($languages as $language){
                    $field[$language] = '';
                }
                
                return json_encode($field);
            }
            
            function getLastPosition(){
                global $wpdb;
                
                $max = 0;
                $fields = $wpdb->get_results('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where cat_id="1"');
                
                return count($fields);
            }
            
            function addField(){
                global $wpdb;
                global $wdhFBPS_CONFIG;
                
                $fieldName = $_POST['name'];
                $fieldNameTranslation = $this->generateFieldJSON($fieldName);
                $valuesList = $this->generateEmptyFieldJSON();
                $fieldTitleParagraphTranslation = $this->generateFieldJSON(WDHFBPS_FBPS_CATEGORY_FIELD_DEFAULT_TEXT);
                $datac = date('Y-m-d H:i:s');
                $lastPosition = $this->getLastPosition();
                $wpdb->query("INSERT INTO ".WDHFBPS_Forms_fields_table."  (name,cat_id,values_list,display_position,datac,height) VALUES ('".$fieldNameTranslation."','1','".$valuesList."','".$lastPosition."','".$datac."','".$wdhFBPS_CONFIG['FORM_FIELD_HEIGHT']."')") or die(mysql_error());
                $fieldId = $wpdb->insert_id;
                
                echo $fieldId.'#@#'.$fieldName.'#@#'.$fieldNameTranslation.'#@#'.'success'.'#@#'.$valuesList.'#@#'.$lastPosition.'#@#'.$fieldTitleParagraphTranslation.'#@#'.WDHFBPS_FBPS_CATEGORY_FIELD_DEFAULT_TEXT;
                die();
            }
            
            // SETTINGS
            function displayFormSettings(){

                echo $this->formSettings();
                
                die();
            }
            
            function formSettings(){
                
                $categoriesHTML = array();
                // Form General Settings                 
                array_push($categoriesHTML,             $this->formGeneralSettings());
                // Fields
                array_push($categoriesHTML,             $this->showCustomFieldsSettings());
                
                return implode('', $categoriesHTML);
            }
            
            // Visual Editor
            function displayVisualEditor(){
                
                if (isset($_POST['cat_id'])) {
                    echo $this->visualEditor($_POST['cat_id']);
                }
                
                die();
            }
            
            function visualEditor(){
                
                $categoriesHTML = array();
                // Form General Settings                 
                array_push($categoriesHTML,             $this->formVisualEditor());
                
                return implode('', $categoriesHTML);
            }

            // Visual Editor
            function displayMessages(){
                
                if (isset($_POST['cat_id'])) {
                    echo $this->messagesSettings($_POST['cat_id']);
                }
                
                die();
            }
            
            function messagesSettings(){
                
                $categoriesHTML = array();
                // Form Messages Settings
                array_push($categoriesHTML,             $this->showFormFormMessagesSettings());
                // Email Messages Settings
                array_push($categoriesHTML,             $this->showFormEmailMessagesSettings());
                // Use SMTP Settings
                array_push($categoriesHTML,             $this->showSMTPSettings());
                 
                return implode('', $categoriesHTML);
            }
            
            function showSMTPSettings(){ //SMTP Settings
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                
                $SMTPDisplayStyle = '';
                
                $showppvHTML = array();
                array_push($showppvHTML, '      <div id="smtp-all-1" style="'.$SMTPDisplayStyle.'">');
                array_push($showppvHTML ,'          <div class="wdhfbps-field-new-title">');
                array_push($showppvHTML ,'              <div class="wdhfbps-header"><span class="wdhfbps-header-text">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USE_SMTP.'</span>');
                                                            //  SMTP Settings
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'use_smtp';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'switch';

                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_USE_SMTP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $wdhFILTER['is_email']        = false;
                                                            
                                                            $SMTPID = 'smtp-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = 'if(window.valueNow == true || window.valueNow == \"true\"){ $jWDH(\"#'.$SMTPID.'\").slideDown(500); } else { $jWDH(\"#'.$SMTPID.'\").slideUp(100); }';
                                                            // DISPLAY
                    array_push($showppvHTML ,'              <span class="wdhfbps-button-switch">');
                    array_push($showppvHTML ,               $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </span>');
                    array_push($showppvHTML ,'          </div>');
                    array_push($showppvHTML ,'      </div>');
                    
                    $styleSMTPVisibility = '';
                    
                    if ($category->use_smtp == 'true'){
                        $styleSMTPVisibility = 'style="display:block;"';
                    }
                    
                    array_push($showppvHTML ,'          <div id="smtp-1" class="wdhfbps-settings-content" '.$styleSMTPVisibility.'>');
                    
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                 array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_HOST.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value">');

                                        // SMTP :SMTP Host
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_host';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'text';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_HOST_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = false;
                                        
                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                    array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_PORT.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value">');

                                        // SMTP :SMTP Port
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_port';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'text';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_PORT_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = false;
                                        
                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                    array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_EMAIL.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value">');

                                        // SMTP :SMTP Email
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_email';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'text';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_EMAIL_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = true;
                                        
                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                    array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_USERNAME.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value">');

                                        // SMTP :SMTP Username
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_username';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'text';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_USERNAME_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = false;
                                        
                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                    array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_PASSWORD.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value">');

                                        // SMTP :SMTP Password
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_password';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'text';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_PASSWORD_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = false;
                                        
                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML, '       <br class="wdhfbps-clear">');
                    array_push($showppvHTML ,'              <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_SSL_CONNECTION.'</div>');
                    array_push($showppvHTML ,'              <div class="wdhfbps-value" style="width:100px;">');

                                        /// SMTP : SSL Conection
                                        $wdhDB['table']         = WDHFBPS_Forms_table;
                                        $wdhFIELD['field_name'] = 'smtp_ssl_connection';
                                        $wdhFIELD['json_value'] = '';
                                        $wdhFIELD['edit']       = true;
                                        $wdhFIELD['conditions'] = array( 
                                            0 => array(
                                                 'field_label' => 'id',
                                                 'field_value' => '1',
                                                 'field_condition' => '' // Allways must be EMPTY
                                            )
                                        );
                                        $wdhINPUT['type'] = 'switch';

                                        // TOOLTIP
                                        $wdhTOOLTIP['text']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_SMTP_SSL_CONNECTION_INFO;
                                        $wdhTOOLTIP['position']            = 'right';
                                        // FILTER
                                        $wdhFILTER['is_required']          = true;
                                        $wdhFILTER['is_email']             = false;

                                        $wdhINPUT['js_wdhedfp_after_save'] = '';
                                        $wdhINPUT['js_wdhedfp_onchange']   = '';
                                            // DISPLAY
                    array_push($showppvHTML ,                   $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                    array_push($showppvHTML ,'              </div>');
                    array_push($showppvHTML ,'      </div>');
                    array_push($showppvHTML ,'  </div>');
                    return implode('',$showppvHTML);
            }
            
            function formVisualEditor(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $fields = $wpdb->get_results('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where cat_id="1" ORDER by display_position ASC, id ASC');
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-container wdh-edfp-form" id="wdhfbps-form-1" style="width:'.$form->form_width.'px;color:#'.$form->text_color.';font-family:'.$form->text_font_family.';font-family:'.$form->text_own_font.';font-size:'.$form->text_font_size.'px;font-style:'.$form->text_font_style.';font-weight:'.$form->text_font_weight.';text-align:'.$form->text_align.';margin-left:'.$form->box_margin_left.'%;margin-right:'.$form->box_margin_right.'%;margin-top:'.$form->box_margin_top.'px;margin-bottom:'.$form->box_margin_bottom.'px;padding-left:'.$form->box_padding_left.'%;padding-right:'.$form->box_padding_right.'%;padding-top:'.$form->box_padding_top.'px;padding-bottom:'.$form->box_padding_bottom.'px;background-color:#'.$form->box_background_color.';border-color:#'.$form->box_border_color.';border-width:'.$form->box_border_size.'px;border-style:'.$form->box_border_type.';border-radius:'.$form->box_border_radius.'px;width:'.$form->form_width.'px;">');
                
                if(isset($fields)){
                    foreach($fields as $field){
                        $inputWidth = intval($field->box_input_padding_left)+intval($field->box_input_padding_right)+intval($field->input_width);
                        $inputHeight = intval($field->input_height);
                        $labelFontFamily = 'font-family:'.$field->text_label_font_family.' !important';
                        $inputFontFamily = 'font-family:'.$field->text_input_font_family.' !important';
                        
                        if ($field->text_label_own_font != ''){
                            $labelFontFamily = 'font-family:'.$field->text_label_own_font.' !important';
                        }
                        
                        if ($field->text_input_own_font != ''){
                            $inputFontFamily = 'font-family:'.$field->text_input_own_font.' !important';
                        }
                        
                        $minHeight = '';
                        
                        if ($field->edit_type == 'radio' || $field->edit_type == 'checkbox' || $field->edit_type == 'file' || $field->edit_type == 'image' || $field->edit_type == 'html_editor' || $field->edit_type == 'post_content'){
                           $minHeight = 'height:inherit;min-'; 
                        }
                        
                        $field->label_css = $field->label_css.'width:'.$field->label_width.'px;color:#'.$field->text_label_color.';'.$labelFontFamily.';font-size:'.$field->text_label_font_size.'px;font-style:'.$field->text_label_font_style.';font-weight:'.$field->text_label_font_weight.';text-align:'.$field->text_label_align.';margin-left:'.$field->box_label_margin_left.'%;margin-right:'.$field->box_label_margin_right.'%;margin-top:'.$field->box_label_margin_top.'px;margin-bottom:'.$field->box_label_margin_bottom.'px;padding-left:'.$field->box_label_padding_left.'%;padding-right:'.$field->box_label_padding_right.'%;padding-top:'.$field->box_label_padding_top.'px;padding-bottom:'.$field->box_label_padding_bottom.'px;background-color:#'.$field->box_label_background_color.';border-color:#'.$field->box_label_border_color.';border-width:'.$field->box_label_border_size.'px;border-style:'.$field->box_label_border_type.';border-radius:'.$field->box_label_border_radius.'px;width:'.$field->label_width.'%;min-height:'.$field->label_height.'px;';
                        $field->input_css = $field->input_css.'color:#'.$field->text_input_color.';'.$inputFontFamily.';font-size:'.$field->text_input_font_size.'px;font-style:'.$field->text_input_font_style.';font-weight:'.$field->text_input_font_weight.';text-align:'.$field->text_input_align.';padding-left:'.$field->box_input_padding_left.'%;padding-right:'.$field->box_input_padding_right.'%;padding-top:'.$field->box_input_padding_top.'px;padding-bottom:'.$field->box_input_padding_bottom.'px;background-color:#'.$field->box_input_background_color.';border-color:#'.$field->box_input_border_color.';border-width:'.$field->box_input_border_size.'px;border-style:'.$field->box_input_border_type.';border-radius:'.$field->box_input_border_radius.'px;'.';border-width:0px;'.$minHeight.'height:'.$inputHeight.'px;margin:0px !important;width:100%;';
                                
                        array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-form-field-container wdh-field wdhfbps-is-sortable" id="wdhfbps-form-1-field-'.$field->id.'" style="width:'.$field->width.'px;color:#'.$field->text_color.';font-family:'.$field->text_font_family.';font-family:'.$field->text_own_font.';font-size:'.$field->text_font_size.'px;font-style:'.$field->text_font_style.';font-weight:'.$field->text_font_weight.';text-align:'.$field->text_align.';margin-left:'.$field->box_margin_left.'%;margin-right:'.$field->box_margin_right.'%;margin-top:'.$field->box_margin_top.'px;margin-bottom:'.$field->box_margin_bottom.'px;padding-left:'.$field->box_padding_left.'%;padding-right:'.$field->box_padding_right.'%;padding-top:'.$field->box_padding_top.'px;padding-bottom:'.$field->box_padding_bottom.'px;background-color:#'.$field->box_background_color.';border-color:#'.$field->box_border_color.';border-width:'.$field->box_border_size.'px;border-style:'.$field->box_border_type.';border-radius:'.$field->box_border_radius.'px;width:'.$field->width.'%;height:inherit;min-height:'.$field->height.'px;">');
                        array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-buttons-each">');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(0, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_SETTINGS_PANEL_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-label-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(1, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_LABEL_SETTINGS_PANEL_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(2, 1, '.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_INPUT_SETTINGS_PANEL_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-copy-button wdh-tooltip" onclick="copyFormFieldBox('.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_COPY_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-paste-button wdh-tooltip" onclick="pasteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_PASTE_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-duplicate-button wdh-tooltip" onclick="duplicateFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_DUPLICATE_INFO.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-get-id-button wdh-tooltip"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_GET_FIELD_ID_INFO.' '.$field->id.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-delete-button wdh-tooltip" onclick="deleteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE.'</span></span></div>');
                        array_push($cpGeneralSettingsHTML, '        </div>');
                        
                        if($field->display_label_position<1){
                            $label_position_class = ' wdhfbps-field-position-0-type-label';
                            $input_position_class = ' wdhfbps-field-position-1-type-input';
                            
                            switch ($field->edit_type){
                                default:
                                    array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', $field->label_width, $label_position_class));
                                    array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input"  style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;">');
                                    array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', $field->label_width, $input_position_class));
                                    array_push($cpGeneralSettingsHTML, '        </div>');
                                    break;
                            }
                        } else{
                            $input_position_class = ' wdhfbps-field-position-0-type-input';
                            $label_position_class = ' wdhfbps-field-position-1-type-label';
                            switch ($field->edit_type){
                                default:
                                    array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input" style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;">');
                                    array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', $field->label_width, $input_position_class));
                                    array_push($cpGeneralSettingsHTML, '        </div>');
                                    array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', $field->label_width, $label_position_class));
                                    break;
                            }
                        }
                        array_push($cpGeneralSettingsHTML, '    </div>');
                        $field->label_css = '';
                        $field->input_css = '';
                    }
                }
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-form-edit-buttons">');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-button wdhfbps-form-field-edit-button-all wdh-tooltip" onclick="genEditFormFieldBox(3,1,1);"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_ALL_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-button wdhfbps-form-field-label-edit-button-all wdh-tooltip" onclick="genEditFormFieldBox(4,1,1);"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_LABEL_ALL_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-edit-button-all wdh-tooltip" onclick="genEditFormFieldBox(5,1,1);"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_INPUT_ALL_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-button wdhfbps-form-edit-button-all wdh-tooltip" onclick="wdhfbpsEditForm(0,1);"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '    </div>'); 
                array_push($cpGeneralSettingsHTML, ' </div>'); 
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-edit-box"><div class="wdhfbps-loader" style="margin-top:85px;"></div></div>');
                
                return implode('', $cpGeneralSettingsHTML);
            }
            
            function formFieldVisualEditor(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $cpGeneralSettingsHTML = array();
                $fieldID = $_POST['copy_field_id'];
                $pasteFieldID = $_POST['paste_field_id'];
                $updateData = array();
                
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"');
                $data_array = array('width' => $field->width,
                                    'height' =>  $field->height,
                                    'label_width' => $field->label_width,
                                    'label_height' =>  $field->label_height,
                                    'input_width' => $field->input_width,
                                    'input_height' =>  $field->input_height,
                                    'text_color' =>  $field->text_color,
                                    'text_font_family' =>  $field->text_font_family,
                                    'text_own_font' =>  $field->text_own_font,
                                    'text_font_size' =>  $field->text_font_size,
                                    'text_font_style' =>  $field->text_font_style,
                                    'text_align' =>  $field->text_align,
                                    'text_font_weight' =>  $field->text_font_weight,
                                    'box_margin_left' =>  $field->box_margin_left,
                                    'box_margin_right' =>  $field->box_margin_right,
                                    'box_margin_top' =>  $field->box_margin_top,
                                    'box_margin_bottom' =>  $field->box_margin_bottom,
                                    'box_padding_left' =>  $field->box_padding_left,
                                    'box_padding_right' =>  $field->box_padding_right,
                                    'box_padding_top' =>  $field->box_padding_top,
                                    'box_padding_bottom' =>  $field->box_padding_bottom,
                                    'box_background_color' =>  $field->box_background_color,
                                    'box_border_color' =>  $field->box_border_color,
                                    'box_border_size' =>  $field->box_border_size,
                                    'box_border_type' =>  $field->box_border_type,
                                    'box_border_radius' =>  $field->box_border_radius,
                                    'text_label_color' =>  $field->text_label_color,
                                    'text_label_font_family' =>  $field->text_label_font_family,
                                    'text_label_own_font' =>  $field->text_label_own_font,
                                    'text_label_font_size' =>  $field->text_label_font_size,
                                    'text_label_font_style' =>  $field->text_label_font_style,
                                    'text_label_align' =>  $field->text_label_align,
                                    'text_label_font_weight' =>  $field->text_label_font_weight,
                                    'box_label_margin_left' =>  $field->box_label_margin_left,
                                    'box_label_margin_right' =>  $field->box_label_margin_right,
                                    'box_label_margin_top' =>  $field->box_label_margin_top,
                                    'box_label_margin_bottom' =>  $field->box_label_margin_bottom,
                                    'box_label_padding_left' =>  $field->box_label_padding_left,
                                    'box_label_padding_right' =>  $field->box_label_padding_right,
                                    'box_label_padding_top' =>  $field->box_label_padding_top,
                                    'box_label_padding_bottom' =>  $field->box_label_padding_bottom,
                                    'box_label_background_color' =>  $field->box_label_background_color,
                                    'box_label_border_color' =>  $field->box_label_border_color,
                                    'box_label_border_size' =>  $field->box_label_border_size,
                                    'box_label_border_type' =>  $field->box_label_border_type,
                                    'box_label_border_radius' =>  $field->box_label_border_radius,
                                    'text_input_color' =>  $field->text_input_color,
                                    'text_input_font_family' =>  $field->text_input_font_family,
                                    'text_input_own_font' =>  $field->text_input_own_font,
                                    'text_input_font_size' =>  $field->text_input_font_size,
                                    'text_input_font_style' =>  $field->text_input_font_style,
                                    'text_input_align' =>  $field->text_input_align,
                                    'text_input_font_weight' =>  $field->text_input_font_weight,
                                    'box_input_margin_left' =>  $field->box_input_margin_left,
                                    'box_input_margin_right' =>  $field->box_input_margin_right,
                                    'box_input_margin_top' =>  $field->box_input_margin_top,
                                    'box_input_margin_bottom' =>  $field->box_input_margin_bottom,
                                    'box_input_padding_left' =>  $field->box_input_padding_left,
                                    'box_input_padding_right' =>  $field->box_input_padding_right,
                                    'box_input_padding_top' =>  $field->box_input_padding_top,
                                    'box_input_padding_bottom' =>  $field->box_input_padding_bottom,
                                    'box_input_background_color' =>  $field->box_input_background_color,
                                    'box_input_border_color' =>  $field->box_input_border_color,
                                    'box_input_border_size' =>  $field->box_input_border_size,
                                    'box_input_border_type' =>  $field->box_input_border_type,
                                    'box_input_border_radius' =>  $field->box_input_border_radius
                                    );
                $where = array('id' => $pasteFieldID);
                $wpdb->update( WDHFBPS_Forms_fields_table, $data_array, $where );
                
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$pasteFieldID.'"');
                $inputWidth = intval($field->box_input_padding_left)+intval($field->box_input_padding_right)+intval($field->input_width);
                $labelFontFamily = 'font-family:'.$field->text_label_font_family.' !important';
                $inputFontFamily = 'font-family:'.$field->text_input_font_family.' !important';

                if ($field->text_label_own_font != ''){
                    $labelFontFamily = 'font-family:'.$field->text_label_own_font.' !important';
                }

                if ($field->text_input_own_font != ''){
                    $inputFontFamily = 'font-family:'.$field->text_input_own_font.' !important';
                }
                        
                $minHeight = '';

                if ($field->edit_type == 'radio' || $field->edit_type == 'checkbox' || $field->edit_type == 'file' || $field->edit_type == 'image' || $field->edit_type == 'html_editor' || $field->edit_type == 'post_content'){
                   $minHeight = 'height:inherit;min-'; 
                }

                $field->label_css = $field->label_css.'width:'.$field->label_width.'px;color:#'.$field->text_label_color.';'.$labelFontFamily.';font-size:'.$field->text_label_font_size.'px;font-style:'.$field->text_label_font_style.';font-weight:'.$field->text_label_font_weight.';text-align:'.$field->text_label_align.';margin-left:'.$field->box_label_margin_left.'%;margin-right:'.$field->box_label_margin_right.'%;margin-top:'.$field->box_label_margin_top.'px;margin-bottom:'.$field->box_label_margin_bottom.'px;padding-left:'.$field->box_label_padding_left.'%;padding-right:'.$field->box_label_padding_right.'%;padding-top:'.$field->box_label_padding_top.'px;padding-bottom:'.$field->box_label_padding_bottom.'px;background-color:#'.$field->box_label_background_color.';border-color:#'.$field->box_label_border_color.';border-width:'.$field->box_label_border_size.'px;border-style:'.$field->box_label_border_type.';border-radius:'.$field->box_label_border_radius.'px;width:'.$field->label_width.'%;min-height:'.$field->label_height.'px;';
                $field->input_css = $field->input_css.'color:#'.$field->text_input_color.';'.$inputFontFamily.';font-size:'.$field->text_input_font_size.'px;font-style:'.$field->text_input_font_style.';font-weight:'.$field->text_input_font_weight.';text-align:'.$field->text_input_align.';padding-left:'.$field->box_input_padding_left.'%;padding-right:'.$field->box_input_padding_right.'%;padding-top:'.$field->box_input_padding_top.'px;padding-bottom:'.$field->box_input_padding_bottom.'px;background-color:#'.$field->box_input_background_color.';border-color:#'.$field->box_input_border_color.';border-width:'.$field->box_input_border_size.'px;border-style:'.$field->box_input_border_type.';border-radius:'.$field->box_input_border_radius.'px;'.';border-width:0px;'.$minHeight.'height:'.$field->input_height.'px;margin:0px;width:100%;';

                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-form-field-container wdh-field wdhfbps-is-sortable" id="wdhfbps-form-1-field-'.$field->id.'" style="width:'.$field->width.'px;color:#'.$field->text_color.';font-family:'.$field->text_font_family.';font-family:'.$field->text_own_font.';font-size:'.$field->text_font_size.'px;font-style:'.$field->text_font_style.';font-weight:'.$field->text_font_weight.';text-align:'.$field->text_align.';margin-left:'.$field->box_margin_left.'%;margin-right:'.$field->box_margin_right.'%;margin-top:'.$field->box_margin_top.'px;margin-bottom:'.$field->box_margin_bottom.'px;padding-left:'.$field->box_padding_left.'%;padding-right:'.$field->box_padding_right.'%;padding-top:'.$field->box_padding_top.'px;padding-bottom:'.$field->box_padding_bottom.'px;background-color:#'.$field->box_background_color.';border-color:#'.$field->box_border_color.';border-width:'.$field->box_border_size.'px;border-style:'.$field->box_border_type.';border-radius:'.$field->box_border_radius.'px;width:'.$field->width.'%;min-height:'.$field->height.'px;">');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-buttons-each">');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(0, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-label-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(1, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_LABEL_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(2, 1, '.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_INPUT_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-copy-button wdh-tooltip" onclick="copyFormFieldBox('.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_COPY_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-paste-button wdh-tooltip" onclick="pasteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_PASTE_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-duplicate-button wdh-tooltip" onclick="duplicateFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_DUPLICATE_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-get-id-button wdh-tooltip"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_GET_FIELD_ID_INFO.' '.$field->id.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-delete-button wdh-tooltip" onclick="deleteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '        </div>');

                if($field->display_label_position<1){
                    $label_position_class = ' wdhfbps-field-position-0-type-label';
                    $input_position_class = ' wdhfbps-field-position-1-type-input';

                    switch ($field->edit_type){
                        case "radio":
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input" style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                        default:
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input"  style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                    }
                } else{
                    $input_position_class = ' wdhfbps-field-position-0-type-input';
                    $label_position_class = ' wdhfbps-field-position-1-type-label';
                    switch ($field->edit_type){
                        default:
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input" style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                    }
                }
                array_push($cpGeneralSettingsHTML, '    </div>');
                $field->label_css = '';
                $field->input_css = '';
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formDuplicateField(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $cpGeneralSettingsHTML = array();
                $fieldID = $_POST['field_id'];
                $data_array = array();
                
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"');
                $data_array = array('display_input_position' => isset($field->display_input_position) ? $field->display_input_position: '',
                                    'display_label_position' => isset($field->display_label_position) ? $field->display_label_position: '',  
                                    'tooltip_text' => isset($field->tooltip_text) ? $field->tooltip_text: '',
                                    'is_adult_video' => isset($field->is_adult_video) ? $field->is_adult_video: '',
                                    'is_unique' => isset($field->is_unique) ? $field->is_unique: '',
                                    'second_name' => isset($field->second_name) ? $field->second_name: '',
                                    'datac' => isset($field->datac) ? $field->datac: '',
                                    'input_values' => isset($field->input_values) ? $field->input_values: '',
                                    'input_css' => isset($field->input_css) ? $field->input_css: '',
                                    'input_class' => isset($field->input_class) ? $field->input_class: '',
                                    'label_css' => isset($field->label_css) ? $field->label_css: '',
                                    'label_class' => isset($field->label_class) ? $field->label_class: '',
                                    'label_link' => isset($field->label_link) ? $field->label_link: '',  
                                    'label_value' => isset($field->label_value) ? $field->label_value: '',
                                    'display_position' => isset($field->display_position) ? $field->display_position: '',
                                    'display_type' => isset($field->display_type) ? $field->display_type: '',
                                    'values_list' => isset($field->values_list) ? $field->values_list: '',
                                    'is_date' => isset($field->is_date) ? $field->is_date: '',
                                    'is_alphanumeric' => isset($field->is_alphanumeric) ? $field->is_alphanumeric: '',
                                    'is_numeric' => isset($field->is_numeric) ? $field->is_numeric: '',
                                    'is_alpha' => isset($field->is_alpha) ? $field->is_alpha: '',
                                    'is_phone' => isset($field->is_phone) ? $field->is_phone: '',
                                    'is_url' => isset($field->is_url) ? $field->is_url: '',  
                                    'is_email' => isset($field->is_email) ? $field->is_email: '',
                                    'is_required' => isset($field->is_required) ? $field->is_required: '',
                                    'zoom' => isset($field->zoom) ? $field->zoom: '',
                                    'slider_range' => isset($field->slider_range) ? $field->slider_range: '',
                                    'slider_max' => isset($field->slider_max) ? $field->slider_max: '',
                                    'slider_min' => isset($field->slider_min) ? $field->slider_min: '',
                                    'edit_type' => isset($field->edit_type) ? $field->edit_type: '',
                                    'name' => isset($field->name) ? $field->name: '',
                                    'cat_id' => isset($field->cat_id) ? $field->cat_id: '',
                                    'width' => isset($field->width) ? $field->width: '',
                                    'height' => isset($field->height) ? $field->height: '',
                                    'label_width' => isset($field->label_width) ? $field->label_width: '',
                                    'label_height' => isset($field->label_height) ? $field->label_height: '',
                                    'input_width' => isset($field->input_width) ? $field->input_width: '',
                                    'input_height' => isset($field->input_height) ? $field->input_height: '',
                                    'text_color' => isset($field->text_color) ? $field->text_color: '',
                                    'text_font_family' => isset($field->text_font_family) ? $field->text_font_family: '',
                                    'text_own_font' => isset($field->text_own_font) ? $field->text_own_font: '',
                                    'text_font_size' => isset($field->text_font_size) ? $field->text_font_size: '',
                                    'text_font_style' => isset($field->text_font_style) ? $field->text_font_style: '',
                                    'text_align' => isset($field->text_align) ? $field->text_align: '',
                                    'text_font_weight' => isset($field->text_font_weight) ? $field->text_font_weight: '',
                                    'box_margin_left' => isset($field->box_margin_left) ? $field->box_margin_left: '',
                                    'box_margin_right' => isset($field->box_margin_right) ? $field->box_margin_right: '',
                                    'box_margin_top' => isset($field->box_margin_top) ? $field->box_margin_top: '',
                                    'box_margin_bottom' => isset($field->box_margin_bottom) ? $field->box_margin_bottom: '',
                                    'box_padding_left' => isset($field->box_padding_left) ? $field->box_padding_left: '',
                                    'box_padding_right' => isset($field->box_padding_right) ? $field->box_padding_right: '',
                                    'box_padding_top' => isset($field->box_padding_top) ? $field->box_padding_top: '',
                                    'box_padding_bottom' => isset($field->box_padding_bottom) ? $field->box_padding_bottom: '',
                                    'box_background_color' => isset($field->box_background_color) ? $field->box_background_color: '',
                                    'box_border_color' => isset($field->box_border_color) ? $field->box_border_color: '',
                                    'box_border_size' => isset($field->box_border_size) ? $field->box_border_size: '',
                                    'box_border_type' => isset($field->box_border_type) ? $field->box_border_type: '',
                                    'box_border_radius' => isset($field->box_border_radius) ? $field->box_border_radius: '',
                                    'text_label_color' => isset($field->text_label_color) ? $field->text_label_color: '',
                                    'text_label_font_family' => isset($field->text_label_font_family) ? $field->text_label_font_family: '',
                                    'text_label_own_font' => isset($field->text_label_own_font) ? $field->text_label_own_font: '',
                                    'text_label_font_size' => isset($field->text_label_font_size) ? $field->text_label_font_size: '',
                                    'text_label_font_style' => isset($field->text_label_font_style) ? $field->text_label_font_style: '',
                                    'text_label_align' => isset($field->text_label_align) ? $field->text_label_align: '',
                                    'text_label_font_weight' => isset($field->text_label_font_weight) ? $field->text_label_font_weight: '',
                                    'box_label_margin_left' => isset($field->box_label_margin_left) ? $field->box_label_margin_left: '',
                                    'box_label_margin_right' => isset($field->box_label_margin_right) ? $field->box_label_margin_right: '',
                                    'box_label_margin_top' => isset($field->box_label_margin_top) ? $field->box_label_margin_top: '',
                                    'box_label_margin_bottom' => isset($field->box_label_margin_bottom) ? $field->box_label_margin_bottom: '',
                                    'box_label_padding_left' => isset($field->box_label_padding_left) ? $field->box_label_padding_left: '',
                                    'box_label_padding_right' => isset($field->box_label_padding_right) ? $field->box_label_padding_right: '',
                                    'box_label_padding_top' => isset($field->box_label_padding_top) ? $field->box_label_padding_top: '',
                                    'box_label_padding_bottom' => isset($field->box_label_padding_bottom) ? $field->box_label_padding_bottom: '',
                                    'box_label_background_color' => isset($field->box_label_background_color) ? $field->box_label_background_color: '',
                                    'box_label_border_color' => isset($field->box_label_border_color) ? $field->box_label_border_color: '',
                                    'box_label_border_size' => isset($field->box_label_border_size) ? $field->box_label_border_size: '',
                                    'box_label_border_type' => isset($field->box_label_border_type) ? $field->box_label_border_type: '',
                                    'box_label_border_radius' => isset($field->box_label_border_radius) ? $field->box_label_border_radius: '',
                                    'text_input_color' => isset($field->text_input_color) ? $field->text_input_color: '',
                                    'text_input_font_family' => isset($field->text_input_font_family) ? $field->text_input_font_family: '',
                                    'text_input_own_font' => isset($field->text_input_own_font) ? $field->text_input_own_font: '',
                                    'text_input_font_size' => isset($field->text_input_font_size) ? $field->text_input_font_size: '',
                                    'text_input_font_style' => isset($field->text_input_font_style) ? $field->text_input_font_style: '',
                                    'text_input_align' => isset($field->text_input_align) ? $field->text_input_align: '',
                                    'text_input_font_weight' => isset($field->text_input_font_weight) ? $field->text_input_font_weight: '',
                                    'box_input_margin_left' => isset($field->box_input_margin_left) ? $field->box_input_margin_left: '',
                                    'box_input_margin_right' => isset($field->box_input_margin_right) ? $field->box_input_margin_right: '',
                                    'box_input_margin_top' => isset($field->box_input_margin_top) ? $field->box_input_margin_top: '',
                                    'box_input_margin_bottom' => isset($field->box_input_margin_bottom) ? $field->box_input_margin_bottom: '',
                                    'box_input_padding_left' => isset($field->box_input_padding_left) ? $field->box_input_padding_left: '',
                                    'box_input_padding_right' => isset($field->box_input_padding_right) ? $field->box_input_padding_right: '',
                                    'box_input_padding_top' => isset($field->box_input_padding_top) ? $field->box_input_padding_top: '',
                                    'box_input_padding_bottom' => isset($field->box_input_padding_bottom) ? $field->box_input_padding_bottom: '',
                                    'box_input_background_color' => isset($field->box_input_background_color) ? $field->box_input_background_color: '',
                                    'box_input_border_color' => isset($field->box_input_border_color) ? $field->box_input_border_color: '',
                                    'box_input_border_size' => isset($field->box_input_border_size) ? $field->box_input_border_size: '',
                                    'box_input_border_type' => isset($field->box_input_border_type) ? $field->box_input_border_type: '',
                                    'box_input_border_radius' => isset($field->box_input_border_radius) ? $field->box_input_border_radius: ''
                                    );
                $wpdb->insert( WDHFBPS_Forms_fields_table, $data_array );
                $fieldID = $wpdb->insert_id;
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"');
                
                $inputWidth = intval($field->box_input_padding_left)+intval($field->box_input_padding_right)+intval($field->input_width);
                $labelFontFamily = 'font-family:'.$field->text_label_font_family.' !important';
                $inputFontFamily = 'font-family:'.$field->text_input_font_family.' !important';

                if ($field->text_label_own_font != ''){
                    $labelFontFamily = 'font-family:'.$field->text_label_own_font.' !important';
                }

                if ($field->text_input_own_font != ''){
                    $inputFontFamily = 'font-family:'.$field->text_input_own_font.' !important';
                }
                        
                $minHeight = '';

                if ($field->edit_type == 'radio' || $field->edit_type == 'checkbox' || $field->edit_type == 'file' || $field->edit_type == 'image' || $field->edit_type == 'html_editor'){
                   $minHeight = 'min-'; 
                }

                $field->label_css = $field->label_css.'width:'.$field->label_width.'px;color:#'.$field->text_label_color.';'.$labelFontFamily.';font-size:'.$field->text_label_font_size.'px;font-style:'.$field->text_label_font_style.';font-weight:'.$field->text_label_font_weight.';text-align:'.$field->text_label_align.';margin-left:'.$field->box_label_margin_left.'%;margin-right:'.$field->box_label_margin_right.'%;margin-top:'.$field->box_label_margin_top.'px;margin-bottom:'.$field->box_label_margin_bottom.'px;padding-left:'.$field->box_label_padding_left.'%;padding-right:'.$field->box_label_padding_right.'%;padding-top:'.$field->box_label_padding_top.'px;padding-bottom:'.$field->box_label_padding_bottom.'px;background-color:#'.$field->box_label_background_color.';border-color:#'.$field->box_label_border_color.';border-width:'.$field->box_label_border_size.'px;border-style:'.$field->box_label_border_type.';border-radius:'.$field->box_label_border_radius.'px;width:'.$field->label_width.'%;min-height:'.$field->label_height.'px;';
                $field->input_css = $field->input_css.'color:#'.$field->text_input_color.';'.$inputFontFamily.';font-size:'.$field->text_input_font_size.'px;font-style:'.$field->text_input_font_style.';font-weight:'.$field->text_input_font_weight.';text-align:'.$field->text_input_align.';padding-left:'.$field->box_input_padding_left.'%;padding-right:'.$field->box_input_padding_right.'%;padding-top:'.$field->box_input_padding_top.'px;padding-bottom:'.$field->box_input_padding_bottom.'px;background-color:#'.$field->box_input_background_color.';border-color:#'.$field->box_input_border_color.';border-width:'.$field->box_input_border_size.'px;border-style:'.$field->box_input_border_type.';border-radius:'.$field->box_input_border_radius.'px;'.';border-width:0px;'.$minHeight.'height:'.$field->input_height.'px;margin:0px;width:100%;';

                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-form-field-container wdh-field wdhfbps-is-sortable" id="wdhfbps-form-1-field-'.$field->id.'" style="width:'.$field->width.'px;color:#'.$field->text_color.';font-family:'.$field->text_font_family.';font-family:'.$field->text_own_font.';font-size:'.$field->text_font_size.'px;font-style:'.$field->text_font_style.';font-weight:'.$field->text_font_weight.';text-align:'.$field->text_align.';margin-left:'.$field->box_margin_left.'%;margin-right:'.$field->box_margin_right.'%;margin-top:'.$field->box_margin_top.'px;margin-bottom:'.$field->box_margin_bottom.'px;padding-left:'.$field->box_padding_left.'%;padding-right:'.$field->box_padding_right.'%;padding-top:'.$field->box_padding_top.'px;padding-bottom:'.$field->box_padding_bottom.'px;background-color:#'.$field->box_background_color.';border-color:#'.$field->box_border_color.';border-width:'.$field->box_border_size.'px;border-style:'.$field->box_border_type.';border-radius:'.$field->box_border_radius.'px;width:'.$field->width.'%;min-height:'.$field->height.'px;">');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-edit-buttons-each">');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(0, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-label-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(1, 1, '.$field->id.');"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_LABEL_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-edit-button-each wdh-tooltip" onclick="genEditFormFieldBox(2, 1, '.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_INPUT_SETTINGS_PANEL_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-copy-button wdh-tooltip" onclick="copyFormFieldBox('.$field->id.');";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_COPY_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-paste-button wdh-tooltip" onclick="pasteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_PASTE_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-duplicate-button wdh-tooltip" onclick="duplicateFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_DUPLICATE_INFO.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-input-get-id-button wdh-tooltip"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_FIELD_GET_FIELD_ID_INFO.' '.$field->id.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '            <div class="wdhfbps-form-edit-button wdhfbps-form-field-delete-button wdh-tooltip" onclick="deleteFormFieldBox('.$field->id.', 1);";"><span class="wdh-tooltip"><span class="wdh-information">'.WDHFBPS_FBPS_CATEGORY_FIELD_DELETE.'</span></span></div>');
                array_push($cpGeneralSettingsHTML, '        </div>');

                if($field->display_label_position<1){
                    $label_position_class = ' wdhfbps-field-position-0-type-label';
                    $input_position_class = ' wdhfbps-field-position-1-type-input';

                    switch ($field->edit_type){
                        case "radio":
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input" style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                        default:
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input"  style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                    }
                } else{
                    $input_position_class = ' wdhfbps-field-position-0-type-input';
                    $label_position_class = ' wdhfbps-field-position-1-type-label';
                    switch ($field->edit_type){
                        default:
                            array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-input-container wdhfbps-is-label-input-sortable" id="wdhfbps-form-1-field-'.$field->id.'-input" style="margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';height:'.$field->input_height.'px;">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'input', '1', $field->label_width, $input_position_class));
                            array_push($cpGeneralSettingsHTML, '        </div>');
                            //array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-form-field-label-container" id="wdhfbps-form-1-field-'.$field->id.'-label">');
                            array_push($cpGeneralSettingsHTML,              $this->formGenerateField($field, 'label', '1', $field->label_width, $label_position_class));
                            //array_push($cpGeneralSettingsHTML, '        </div>');
                            break;
                    }
                }
                array_push($cpGeneralSettingsHTML, '    </div>');
                $field->label_css = '';
                $field->input_css = '';
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function getColumnsName($table){
                global $wpdb;
                $columnData = array();
                $columns = $wpdb->get_results("SHOW COLUMNS FROM ".$table, ARRAY_A);
                
                foreach($columns as $column){
                    array_push($columnData, $column['Field']);  
                }
                
                return $columnData;
            }
            
            function getRowValues($table,$field, $fieldValue){
                global $wpdb;
                $valuesData = array();
                $values = $wpdb->get_row('SELECT * FROM '.$table.' where '.$field.'="'.$fieldValue.'"');
                
                foreach($values as $keyData => $valueData){
                    array_push($valuesData,$valueData);
                }
                
                return $valuesData;
            }
            
            function urlString($string){
                $string = str_replace(' ', '',$string);
                
                return $string;
            }
        
            function getUserData($userID){
                global $wpdb;

                if (!defined('WDHFBPS_Users_table')) { // Users
                    define('WDHFBPS_Users_table', $wpdb->prefix.'wdhfbps_users');
                }
                
                $user = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Users_table.' where user_id="'.$userID.'"');

                return $user;
            }
            
            function formDuplicateFieldById($fieldID){
                global $wpdb;
                $data_array = array();
                
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"');
                $data_array = array('cat_id' => '1',
                                    'display_input_position' => isset($field->display_input_position) ? $field->display_input_position: '',
                                    'display_label_position' => isset($field->display_label_position) ? $field->display_label_position: '',  
                                    'tooltip_text' => isset($field->tooltip_text) ? $field->tooltip_text: '',
                                    'is_adult_video' => isset($field->is_adult_video) ? $field->is_adult_video: '',
                                    'is_unique' => isset($field->is_unique) ? $field->is_unique: '',
                                    'second_name' => isset($field->second_name) ? $field->second_name: '',
                                    'datac' => isset($field->datac) ? $field->datac: '',
                                    'input_values' => isset($field->input_values) ? $field->input_values: '',
                                    'input_css' => isset($field->input_css) ? $field->input_css: '',
                                    'input_class' => isset($field->input_class) ? $field->input_class: '',
                                    'label_css' => isset($field->label_css) ? $field->label_css: '',
                                    'label_class' => isset($field->label_class) ? $field->label_class: '',
                                    'label_link' => isset($field->label_link) ? $field->label_link: '',  
                                    'label_value' => isset($field->label_value) ? $field->label_value: '',
                                    'display_position' => isset($field->display_position) ? $field->display_position: '',
                                    'display_type' => isset($field->display_type) ? $field->display_type: '',
                                    'values_list' => isset($field->values_list) ? $field->values_list: '',
                                    'is_date' => isset($field->is_date) ? $field->is_date: '',
                                    'is_alphanumeric' => isset($field->is_alphanumeric) ? $field->is_alphanumeric: '',
                                    'is_numeric' => isset($field->is_numeric) ? $field->is_numeric: '',
                                    'is_alpha' => isset($field->is_alpha) ? $field->is_alpha: '',
                                    'is_phone' => isset($field->is_phone) ? $field->is_phone: '',
                                    'is_url' => isset($field->is_url) ? $field->is_url: '',  
                                    'is_email' => isset($field->is_email) ? $field->is_email: '',
                                    'is_required' => isset($field->is_required) ? $field->is_required: '',
                                    'zoom' => isset($field->zoom) ? $field->zoom: '',
                                    'slider_range' => isset($field->slider_range) ? $field->slider_range: '',
                                    'slider_max' => isset($field->slider_max) ? $field->slider_max: '',
                                    'slider_min' => isset($field->slider_min) ? $field->slider_min: '',
                                    'edit_type' => isset($field->edit_type) ? $field->edit_type: '',
                                    'name' => isset($field->name) ? $field->name: '',
                                    'width' => isset($field->width) ? $field->width: '',
                                    'height' => isset($field->height) ? $field->height: '',
                                    'label_width' => isset($field->label_width) ? $field->label_width: '',
                                    'label_height' => isset($field->label_height) ? $field->label_height: '',
                                    'input_width' => isset($field->input_width) ? $field->input_width: '',
                                    'input_height' => isset($field->input_height) ? $field->input_height: '',
                                    'text_color' => isset($field->text_color) ? $field->text_color: '',
                                    'text_font_family' => isset($field->text_font_family) ? $field->text_font_family: '',
                                    'text_own_font' => isset($field->text_own_font) ? $field->text_own_font: '',
                                    'text_font_size' => isset($field->text_font_size) ? $field->text_font_size: '',
                                    'text_font_style' => isset($field->text_font_style) ? $field->text_font_style: '',
                                    'text_align' => isset($field->text_align) ? $field->text_align: '',
                                    'text_font_weight' => isset($field->text_font_weight) ? $field->text_font_weight: '',
                                    'box_margin_left' => isset($field->box_margin_left) ? $field->box_margin_left: '',
                                    'box_margin_right' => isset($field->box_margin_right) ? $field->box_margin_right: '',
                                    'box_margin_top' => isset($field->box_margin_top) ? $field->box_margin_top: '',
                                    'box_margin_bottom' => isset($field->box_margin_bottom) ? $field->box_margin_bottom: '',
                                    'box_padding_left' => isset($field->box_padding_left) ? $field->box_padding_left: '',
                                    'box_padding_right' => isset($field->box_padding_right) ? $field->box_padding_right: '',
                                    'box_padding_top' => isset($field->box_padding_top) ? $field->box_padding_top: '',
                                    'box_padding_bottom' => isset($field->box_padding_bottom) ? $field->box_padding_bottom: '',
                                    'box_background_color' => isset($field->box_background_color) ? $field->box_background_color: '',
                                    'box_border_color' => isset($field->box_border_color) ? $field->box_border_color: '',
                                    'box_border_size' => isset($field->box_border_size) ? $field->box_border_size: '',
                                    'box_border_type' => isset($field->box_border_type) ? $field->box_border_type: '',
                                    'box_border_radius' => isset($field->box_border_radius) ? $field->box_border_radius: '',
                                    'text_label_color' => isset($field->text_label_color) ? $field->text_label_color: '',
                                    'text_label_font_family' => isset($field->text_label_font_family) ? $field->text_label_font_family: '',
                                    'text_label_own_font' => isset($field->text_label_own_font) ? $field->text_label_own_font: '',
                                    'text_label_font_size' => isset($field->text_label_font_size) ? $field->text_label_font_size: '',
                                    'text_label_font_style' => isset($field->text_label_font_style) ? $field->text_label_font_style: '',
                                    'text_label_align' => isset($field->text_label_align) ? $field->text_label_align: '',
                                    'text_label_font_weight' => isset($field->text_label_font_weight) ? $field->text_label_font_weight: '',
                                    'box_label_margin_left' => isset($field->box_label_margin_left) ? $field->box_label_margin_left: '',
                                    'box_label_margin_right' => isset($field->box_label_margin_right) ? $field->box_label_margin_right: '',
                                    'box_label_margin_top' => isset($field->box_label_margin_top) ? $field->box_label_margin_top: '',
                                    'box_label_margin_bottom' => isset($field->box_label_margin_bottom) ? $field->box_label_margin_bottom: '',
                                    'box_label_padding_left' => isset($field->box_label_padding_left) ? $field->box_label_padding_left: '',
                                    'box_label_padding_right' => isset($field->box_label_padding_right) ? $field->box_label_padding_right: '',
                                    'box_label_padding_top' => isset($field->box_label_padding_top) ? $field->box_label_padding_top: '',
                                    'box_label_padding_bottom' => isset($field->box_label_padding_bottom) ? $field->box_label_padding_bottom: '',
                                    'box_label_background_color' => isset($field->box_label_background_color) ? $field->box_label_background_color: '',
                                    'box_label_border_color' => isset($field->box_label_border_color) ? $field->box_label_border_color: '',
                                    'box_label_border_size' => isset($field->box_label_border_size) ? $field->box_label_border_size: '',
                                    'box_label_border_type' => isset($field->box_label_border_type) ? $field->box_label_border_type: '',
                                    'box_label_border_radius' => isset($field->box_label_border_radius) ? $field->box_label_border_radius: '',
                                    'text_input_color' => isset($field->text_input_color) ? $field->text_input_color: '',
                                    'text_input_font_family' => isset($field->text_input_font_family) ? $field->text_input_font_family: '',
                                    'text_input_own_font' => isset($field->text_input_own_font) ? $field->text_input_own_font: '',
                                    'text_input_font_size' => isset($field->text_input_font_size) ? $field->text_input_font_size: '',
                                    'text_input_font_style' => isset($field->text_input_font_style) ? $field->text_input_font_style: '',
                                    'text_input_align' => isset($field->text_input_align) ? $field->text_input_align: '',
                                    'text_input_font_weight' => isset($field->text_input_font_weight) ? $field->text_input_font_weight: '',
                                    'box_input_margin_left' => isset($field->box_input_margin_left) ? $field->box_input_margin_left: '',
                                    'box_input_margin_right' => isset($field->box_input_margin_right) ? $field->box_input_margin_right: '',
                                    'box_input_margin_top' => isset($field->box_input_margin_top) ? $field->box_input_margin_top: '',
                                    'box_input_margin_bottom' => isset($field->box_input_margin_bottom) ? $field->box_input_margin_bottom: '',
                                    'box_input_padding_left' => isset($field->box_input_padding_left) ? $field->box_input_padding_left: '',
                                    'box_input_padding_right' => isset($field->box_input_padding_right) ? $field->box_input_padding_right: '',
                                    'box_input_padding_top' => isset($field->box_input_padding_top) ? $field->box_input_padding_top: '',
                                    'box_input_padding_bottom' => isset($field->box_input_padding_bottom) ? $field->box_input_padding_bottom: '',
                                    'box_input_background_color' => isset($field->box_input_background_color) ? $field->box_input_background_color: '',
                                    'box_input_border_color' => isset($field->box_input_border_color) ? $field->box_input_border_color: '',
                                    'box_input_border_size' => isset($field->box_input_border_size) ? $field->box_input_border_size: '',
                                    'box_input_border_type' => isset($field->box_input_border_type) ? $field->box_input_border_type: '',
                                    'box_input_border_radius' => isset($field->box_input_border_radius) ? $field->box_input_border_radius: ''
                                    );
                $wpdb->insert( WDHFBPS_Forms_fields_table, $data_array );
            }
            
            function formControlPanel(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;

                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box" id="wdhfbps-form-edit-box-1">');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-header-text-1" style="display:none;" onclick="wdhfbpsSwitchEditForm(0,1);">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-header-box-1" onclick="wdhfbpsSwitchEditForm(1,1);">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-text-1">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value1 wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-size\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"fontStyle\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-weight\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'text_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"text-align\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-box-1">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                           $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Margin Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_background_color-----box_label_background_color-----box_input_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-style\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'box_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-radius\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-1">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value1">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_table;
                                                            $wdhFIELD['field_name'] = 'form_width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => '1',
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 940; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditForm(1, 1);">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                die();
            }
            
            function formFieldControlPanel(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['field_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-edit-box" id="wdhfbps-form-field-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormField(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormField(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_color-----text_input_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_family-----text_input_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-family\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_own_font-----text_input_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = false;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-family\",\"+window.valueNow+\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_size-----text_input_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-size\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_style-----text_input_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"fontStyle\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_weight-----text_input_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-weight\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-weight\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_align-----text_input_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.' .wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"text-align\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"text-align\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                           $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                           $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                           $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-style\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-radius\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"width\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, '.$field->id.');">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formFieldLabelControlPanel(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['field_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-label-edit-box" id="wdhfbps-form-field-label-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-label-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldLabel(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-label-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldLabel(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-label-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-size\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"fontStyle\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-weight\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"text-align\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-label-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"margin-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"padding-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-style\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-radius\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'label_width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"width\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'label_height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdh-form-field-label-id-1-'.$fieldID;
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, '.$field->id.');">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formFieldInputControlPanel(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['field_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-input-edit-box" id="wdhfbps-form-field-input-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-input-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldInput(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-input-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldInput(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-input-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\"#'.$formBoxThID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\", window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-family\", window.valueNow); $jWDH(\"#'.$formBoxThID.'\").css(\"font-family\", window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\"#'.$formBoxThID.'\").css(\"font-family\",\"+window.valueNow+\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\"#'.$formBoxThID.'\").css(\"font-size\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\"#'.$formBoxThID.'\").css(\"fontStyle\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"fontWeight\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"fontWeight\",window.valueNow); $jWDH(\"#'.$formBoxThID.'\").css(\"fontWeight\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-1-field-'.$fieldID.'-input select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"text-align\",window.valueNow); $jWDH(\"#'.$formBoxSecID.'\").css(\"text-align\",window.valueNow); $jWDH(\"#'.$formBoxThID.'\").css(\"text-align\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-input-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"marginLeft\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"marginRight\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"marginTop\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"marginBottom\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"paddingLeft\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"paddingRight\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"paddingTop\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"paddingBottom\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-width\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-style\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"border-radius\",window.valueNow+\"px\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"border-radius\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'input_width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"width\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'input_height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-1-field-'.$fieldID.'-input';
                                                            $formBoxSecID = 'wdhfbps-form-1-field-'.$fieldID.'-input .input-mini-field';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\"#'.$formBoxID.'\").css(\"height\",window.valueNow+\"px\"); $jWDH(\"#'.$formBoxSecID.'\").css(\"height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, '.$field->id.');">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formFieldControlPanelAll(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['form_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-edit-box" id="wdhfbps-form-field-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormField(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormField(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_color-----text_input_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_family-----text_input_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = false;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"font-family\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_own_font-----text_input_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = false;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\".'.$formBoxSecID.'\").css(\"font-family\",\"+window.valueNow+\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_size-----text_input_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\".'.$formBoxSecID.'\").css(\"font-size\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_style-----text_input_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"fontStyle\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_weight-----text_input_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-weight\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"font-weight\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_align-----text_input_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"text-align\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"text-align\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-style\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-radius\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"width\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"min-height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, 1);">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formFieldLabelControlPanelAll(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['form_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-label-edit-box" id="wdhfbps-form-field-label-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-label-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldLabel(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-label-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldLabel(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-label-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-size\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"fontStyle\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-weight\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_label_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"text-align\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-label-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"margin-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-left\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-right\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-top\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"padding-bottom\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-width\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-style\",\"\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_label_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-radius\",\"\"+window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'label_width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"width\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'label_height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-label-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, 1);">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function formFieldInputControlPanelAll(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                
                $fieldID = $_POST['form_id'];
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"'); 
                
                array_push($cpGeneralSettingsHTML, ' <div class="wdhfbps-form-edit-box wdhfbps-form-field-input-edit-box" id="wdhfbps-form-field-input-edit-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <h2 class="wdhfbps-selected" id="wdhfbps-edit-field-input-header-text-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldInput(0,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_TEXT.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <h2 id="wdhfbps-edit-field-input-header-box-'.$fieldID.'" onclick="wdhfbpsSwitchEditFormFieldInput(1,'.$fieldID.');">'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TITLE_BOX.'</h2>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column wdhfbps-selected" id="wdhfbps-field-input-text-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"color\",\"#\"+window.valueNow); $jWDH(\".'.$formBoxThID.'\").css(\"color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Family
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_family';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = 'Georgia, serif|Palatino Linotype, Book Antiqua, Palatino, serif|Times New Roman, Times, serif|Arial, Helvetica, sans-serif|Arial Black, Gadget, sans-serif|Comic Sans MS, cursive, sans-serif|Impact, Charcoal, sans-serif|Lucida Sans Unicode, Lucida Grande, sans-serif|Tahoma, Geneva, sans-serif|Trebuchet MS, Helvetica, sans-serif|Verdana, Geneva, sans-serif|Courier New, Courier, monospace|Lucida Console, Monaco, monospace';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_FAMILY_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"font-family\",window.valueNow); $jWDH(\".'.$formBoxThID.'\").css(\"font-family\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Own-Font
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_own_font';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'text';
                                                           
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_OWN_FONT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\".'.$formBoxSecID.'\").css(\"font-family\",\"+window.valueNow+\"); $jWDH(\".'.$formBoxThID.'\").css(\"font-family\",\"+window.valueNow+\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value wdh-is-slider" id="wdhfbps-option-value'.$fieldID.' wdh-is-slider">');
                                                            // Text: Font Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 7; // set slider min
                                                            $wdhINPUT['slider_max']   = 72; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\".'.$formBoxSecID.'\").css(\"font-size\",window.valueNow+\"px\"); $jWDH(\".'.$formBoxThID.'\").css(\"font-size\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Style
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_style';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_ITALIC.'@@italic|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_VALUE_REGULAR.'@@regular';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_STYLE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"fontStyle\",window.valueNow); $jWDH(\".'.$formBoxThID.'\").css(\"fontStyle\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Font-Weight
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_font_weight';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE1.'@@100|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE2.'@@200|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE3.'@@300|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE4.'@@400|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE5.'@@500|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE6.'@@600|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE7.'@@700|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_VALUE_BOLD.'@@bold';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_FONT_WEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"fontWeight\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"fontWeight\",window.valueNow); $jWDH(\".'.$formBoxThID.'\").css(\"fontWeight\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Text: Align
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'text_input_align';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_LEFT.'@@left|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_RIGHT.'@@right|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_VALUE_CENTER.'@@center';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_TEXT_OPTION_ALIGN_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container .wdh-field-option';
                                                            $formBoxThID = 'wdhfbps-form-field-input-container select';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"text-align\",window.valueNow); $jWDH(\".'.$formBoxSecID.'\").css(\"text-align\",window.valueNow); $jWDH(\".'.$formBoxThID.'\").css(\"text-align\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');// End column Text
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-column" id="wdhfbps-field-input-box-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"marginLeft\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"marginRight\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"marginTop\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Margin Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_margin_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_MARGIN_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"marginBottom\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Left
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_left';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_LEFT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"paddingLeft\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Right
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_right';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_RIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"paddingRight\",window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding-Top
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_top';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_TOP_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"paddingTop\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Padding Bottom
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_padding_bottom';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_PADDING_BOTTOM_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"paddingBottom\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Background-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_background_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BACKGROUND_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"background-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Size
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_size';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 10; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_SIZE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-width\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                             // Box: Border-type
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_type';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'select';
                                                            $wdhINPUT['values'] = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_SOLID.'@@solid|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOTTED.'@@dotted|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DASHED.'@@dashed|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_DOUBLE.'@@double|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_GROOVE.'@@groove|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_RIDGE.'@@ridge|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_INSET.'@@inset|'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_VALUE_OUTSET.'@@outset';
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_TYPE_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-style\",window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border-Color
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_color';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'colorpicker';
                                                            
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_COLOR_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-color\",\"#\"+window.valueNow);';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: Border Radius
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'box_input_border_radius';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 0; // set slider min
                                                            $wdhINPUT['slider_max']   = 70; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_BORDER_RADIUS_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"border-radius\",window.valueNow+\"px\"); $jWDH(\".'.$formBoxSecID.'\").css(\"border-radius\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'input_width';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 1; // set slider min
                                                            $wdhINPUT['slider_max']   = 100; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_WIDTH_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"width\",\"\"+window.valueNow+\"%\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-option" id="wdhfbps-option-'.$fieldID.'">');
                array_push($cpGeneralSettingsHTML, '        <label>'.WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT.':</label>');
                array_push($cpGeneralSettingsHTML, '        <div class="wdhfbps-option-value" id="wdhfbps-option-value'.$fieldID.'">');
                                                            // Box: width
                                                            $wdhDB['table'] = WDHFBPS_Forms_fields_table;
                                                            $wdhFIELD['field_name'] = 'input_height';
                                                            $wdhFIELD['json_value'] = '';
                                                            $wdhFIELD['edit']       = true;
                                                            $wdhFIELD['conditions'] = array( 
                                                                0 => array(
                                                                     'field_label' => 'cat_id',
                                                                     'field_value' => $fieldID,
                                                                     'field_condition' => '' // Allways must be EMPTY
                                                                )
                                                            );
                                                            $wdhINPUT['type'] = 'slider';
                                                            $wdhINPUT['slider_min']   = 20; // set slider min
                                                            $wdhINPUT['slider_max']   = 500; // set slider max
                                                            $wdhINPUT['slider_range'] = 1; // set slider step
    
                                                            // TOOLTIP
                                                            $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_VISUAL_EDITOR_BOX_OPTION_HEIGHT_INFO;
                                                            $wdhTOOLTIP['position'] = 'right';
                                                            // FILTER
                                                            $wdhFILTER['is_required']     = true;
                                                            $formBoxID = 'wdhfbps-form-field-input-container';
                                                            $formBoxSecID = 'wdhfbps-form-field-input-container input';
                                                            $wdhINPUT['js_wdhedfp_after_save'] = '';
                                                            $wdhINPUT['js_wdhedfp_onchange'] = '$jWDH(\".'.$formBoxID.'\").css(\"height\",window.valueNow+\"px\"); $jWDH(\".'.$formBoxSecID.'\").css(\"height\",window.valueNow+\"px\");';
                                                            // Display
                array_push($cpGeneralSettingsHTML,          $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                array_push($cpGeneralSettingsHTML, '        </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');
                array_push($cpGeneralSettingsHTML, '    </div>');//End column Box
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-close" onclick="wdhfbpsEditFormField(1, 1, 1);">x</div>');
                array_push($cpGeneralSettingsHTML, ' </div>');
                
                echo implode('', $cpGeneralSettingsHTML);
                
                die();
            }
            
            function getFieldsOptions($selectFieldID = 0){
                global $wpdb;
                
                $feeHTML = array();
                $fields = $wpdb->get_results('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where cat_id="1"');
                $label = '';
                
                foreach($fields as $field){
                    $wdhfbps_language_now = get_option('WDHFBPS_language');
                    
                    if(isset($field->name)) {
                        $fieldDate = json_decode(stripslashes($field->name));
                        $label = $fieldDate->$wdhfbps_language_now;
                    }
                
                    if ($selectFieldID == $field->id) {
                        array_push($feeHTML, '<option value="'.$field->id.'" selected="selected">'.$label.'</option>');
                    } else {
                        array_push($feeHTML, '<option value="'.$field->id.'">'.$label.'</option>');
                    }
                }
                
                return implode('', $feeHTML);
            }
            
            function formGenerateField($field, $return, $label_width, $new_css_class = ''){
                global $wpdb;
                
                $fieldHTML = array();
                $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"'); 
                
                
                if($return == 'label'){
                    $star = '';
                    if ($field->is_required == 'true') {
                        $star = '<span class="star">*</span>';
                    }
                    $name = 'wdhfbpsname';
                    $wdhfbps_language_now = get_option('WDHFBPS_language');
                    $fieldDate = json_decode(stripslashes($field->name));
                    $translation = str_replace('"','#',stripslashes($field->name));
                    $label = $fieldDate->$wdhfbps_language_now;
                    $second_label = $fieldDate->$wdhfbps_language_now;
                    $label_class = $field->label_class.$new_css_class;
                    $label_css = $field->label_css;
                    $label_width = $field->label_width;
                    
                    if ($field->edit_type == 'title' || $field->edit_type == 'paragraph'){
                        $fieldDate = json_decode(stripslashes($field->label_value));
                        $label = $fieldDate->$wdhfbps_language_now;
                    }
                    
                    
                    switch($field->edit_type){
                        case "title":
                            array_push($fieldHTML, $this->titleFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "paragraph":
                            array_push($fieldHTML, $this->paragraphFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "text":
                            array_push($fieldHTML, $this->textFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "username":
                            array_push($fieldHTML, $this->textFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "textarea":
                            array_push($fieldHTML, $this->textareaFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "select":
                            array_push($fieldHTML, $this->selectFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "radio":
                            array_push($fieldHTML, $this->radioFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "checkbox":
                            array_push($fieldHTML, $this->checkboxFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "password":
                            array_push($fieldHTML, $this->passwordFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "captcha":
                            array_push($fieldHTML, $this->captchaFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        case "submit":
                            array_push($fieldHTML, $this->submitFieldLabel($label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                        default:
                            array_push($fieldHTML, $this->textFieldLabel($star, $label, $field->id, $name, $label_class, $label_css, $label_width));
                            break;
                    }
                        
                    
                } else{
                    $name = 'wdhfbpsname';
                    $input_class = $field->input_class.$new_css_class.' input-mini-field';
                    $input_css = $field->input_css;
                    $wdhfbps_language_now = get_option('WDHFBPS_language');
                    $fieldDate = json_decode(stripslashes($field->name));
                    $label = $fieldDate->$wdhfbps_language_now;
                    $fieldDate = json_decode(stripslashes($field->values_list));
                    $options = $fieldDate->$wdhfbps_language_now;
                    
                    switch($field->edit_type){
                        case "title":
                            array_push($fieldHTML, $this->titleFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "paragraph":
                            array_push($fieldHTML, $this->paragraphFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "text":
                            array_push($fieldHTML, $this->textFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "username":
                            array_push($fieldHTML, $this->textFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "textarea":
                            array_push($fieldHTML, $this->textareaFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "select":
                            array_push($fieldHTML, $this->selectFieldInput($field->id, $name, $options, $input_class, $input_css));
                            break;
                        case "radio":
                            array_push($fieldHTML, $this->radioFieldInput($field->id, $name, $options, $input_class, $input_css));
                            break;
                        case "checkbox":
                            array_push($fieldHTML, $this->checkboxFieldInput($field->id, $name, $options, $input_class, $input_css));
                            break;
                        case "password":
                            array_push($fieldHTML, $this->passwordFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "captcha":
                            array_push($fieldHTML, $this->captchaFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                        case "submit":
                            array_push($fieldHTML, $this->submitFieldInput($field->id, $name, $label, $input_class, $input_css));
                            break;
                        default:
                            array_push($fieldHTML, $this->textFieldInput($field->id, $name, $input_class, $input_css));
                            break;
                    }
                       
                }
                return implode('', $fieldHTML);
            }
            
            function titleFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();

                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . '</label>');
                
                return implode('', $contentHTML);
                
            }
            
            function paragraphFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();

                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . '</label>');
                
                return implode('', $contentHTML);
                
            }
            
            function textFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();

                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');
                
                return implode('', $contentHTML);
                
            }
            
            function textareaFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();

                array_push($contentHTML, '        <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');

                return implode('', $contentHTML);
            }
            
            function selectFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();
                
                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');
               
                return implode('', $contentHTML);
            }
            
            function radioFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();
                
                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');

                return implode('', $contentHTML);
            }
            
            function checkboxFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css, $label_width){
                $contentHTML = array();

                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');

                return implode('', $contentHTML);
            }
        
            function passwordFieldLabel($star, $label,  $fieldID, $name, $label_class, $label_css, $label_width){
                global $wdhSettings;
                global $wdhFIELD;

                $contentHTML = array();
                
                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . '; width:'.$label_width.'%;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '">' . $label . $star . ':</label>');
                
                return implode('', $contentHTML);
            }
            
            function captchaFieldLabel($star, $label, $fieldID, $name, $label_class, $label_css){
                $contentHTML = array();
                $term_one    = rand(0, 9);
                $second_term = rand(0, 9);
                
                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . ' text-align:right;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '"><b>' . $term_one . '+' . $second_term . ' = </b>&nbsp;</label>');

                return implode('', $contentHTML);
            }
            
            function submitFieldLabel($label, $fieldID, $name, $label_class, $label_css){
                $contentHTML = array();
                
                array_push($contentHTML, '         <label class="wdhfbps-form-field-label-container wdhfbps-is-label-input-sortable ' . $label_class . '" style="' . $this->generateCSS($label_css) . ' text-align:right;" for="wdh-form-field-value-id-1-' . $fieldID . '-' . $name . '" id="wdh-form-field-label-id-1-' . $fieldID . '" onclick="genEditFormFieldBox(1, 1, '.$fieldID.');">&nbsp;</label>');

                return implode('', $contentHTML);
            }
            
            function titleFieldInput($fieldID, $name, $input_class, $input_css){
                $contentHTML = array();

                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '">');
                array_push($contentHTML, '         </div>');
                
                return implode('', $contentHTML);
            }
            
            function paragraphFieldInput($fieldID, $name, $input_class, $input_css){
                $contentHTML = array();

                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '">');
                array_push($contentHTML, '         </div>');
                
                return implode('', $contentHTML);
            }
            
            function textFieldInput($fieldID, $name, $input_class, $input_css){
                $contentHTML = array();

                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '" type="text" name="' . $name . '"/>');

                return implode('', $contentHTML);
            }
            
            function textareaFieldInput($fieldID, $name, $input_class, $input_css){
                $contentHTML = array();

                array_push($contentHTML, '        <textarea class="wdh-textarea wdh-get-value wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '" name="' . $name . '"></textarea>');

                return implode('', $contentHTML);
            }
            
            function selectFieldInput($fieldID, $name, $options, $input_class, $input_css){
                $contentHTML = array();
                $selecLabel  = '';
                $selecValue  = '';

                array_push($contentHTML, '         <select name="' . $name . '" id="wdh-form-field-value-id-1-' . $fieldID . '" class="wdh-select wdh-get-value wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '">');

                $optionsall = explode("|", $options);
                $i          = 0;

                foreach ($optionsall as $option) {

                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];

                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '         <option value="' . $selecValue . '">' . $selecLabel . '</option>');

                    $i++;

                }

                array_push($contentHTML, '         </select>');

                return implode('', $contentHTML);
            }
            
            function radioFieldInput($fieldID, $name, $options, $input_class, $input_css){
                $contentHTML = array();
                $selecLabel  = '';
                $selecValue  = '';

                $optionsall = explode("|", $options);

                $i = 0;

                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '">');
                array_push($contentHTML, '         <input class="wdh-radio wdh-get-value wdh-form-field-value-1" id="wdh-form-field-value-id-1-' . $fieldID . '" type="hidden" name="' . $name . '"/>');

                foreach ($optionsall as $option) {

                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-1">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-radio-1-' . $fieldID . '" class="wdh-radio wdh-get-value-radio-option" type="radio" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');
                    array_push($contentHTML, '             </div>');
                    $i++;
                }

                array_push($contentHTML, '         </div>');

                return implode('', $contentHTML);
            }
            
            function checkboxFieldInput($fieldID, $name, $options, $input_class, $input_css){
                $contentHTML = array();
                $selecLabel  = '';
                $selecValue  = '';

                array_push($contentHTML, '         <input class="wdh-checkbox wdh-get-value wdh-form-field-value-1" id="wdh-form-field-value-id-1-' . $fieldID . '" type="hidden" name="' . $name . '"/>');

                $optionsall = explode("|", $options);
                $i          = 0;
                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '">');

                foreach ($optionsall as $option) {



                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-1">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-checkbox-1-' . $fieldID . '" class="wdh-checkbox wdh-get-value-checkbox-option" type="checkbox" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');

                    array_push($contentHTML, '             </div>');

                    $i++;

                }

                array_push($contentHTML, '         </div>');

                return implode('', $contentHTML);
            }
        
            function passwordFieldInput($fieldID, $name, $input_class, $input_css){
                global $wdhSettings;

                $contentHTML = array();

                array_push($contentHTML, '         <input class="wdh-input-password wdh-filter-is-password wdh-get-value wdh-passoword wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '" type="password" name="' . $name . '"/>');
               
                return implode('', $contentHTML);
            }
            
            function captchaFieldInput($fieldID, $name, $input_class, $input_css){
                $contentHTML = array();
                $term_one    = rand(0, 9);
                $second_term = rand(0, 9);
                $total       = $term_one + $second_term;
                
                array_push($contentHTML, '         <input class="wdh-input wdh-filter-is-captcha wdh-get-value wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . ' width:30px;" maxlength="3" id="wdh-form-field-value-id-1-' . $fieldID . '" type="text" name="' . $name . '"/>');
                array_push($contentHTML, '         <input class="wdh-filter-is-recaptcha" id="wdh-form-field-recaptcha-id-1-' . $fieldID . '" value="' . $total . '" type="hidden" name="recaptca-' . $name . '"/>');

                return implode('', $contentHTML);
            }
            
            function submitFieldInput($fieldID, $name, $label, $input_class, $input_css) {
                $contentHTML = array();
                
                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-1 ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-1-' . $fieldID . '" type="submit" " name="' . $name . '" value="' . $label . '"/>');
                
                return implode('', $contentHTML);
           }
            
            function generateCSS($properties){
                $cssHTML = array();
                
                if(is_array($properties)){
                
                    if (!empty($properties)) {
                        foreach ($properties as $key => $value) {
                            array_push($cssHTML, '' . $key . ':' . $value . ';');
                        }
                    }
                } else{
                    array_push($cssHTML, $properties);
                }
                
                return implode('', $cssHTML);
            }
            
            function formGeneralSettings(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhFBPS_CONFIG;
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $cpGeneralSettingsHTML = array();
                
                $menu_item = 'menu-posts-wdhfbps_1';
                
                array_push($cpGeneralSettingsHTML, '<div class="wdhfbps-field-new-title">');
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-header-second">'.WDHFBPS_FBPS_CATEGORY_SETTINGS.'</div>');
                array_push($cpGeneralSettingsHTML, '</div>');
                array_push($cpGeneralSettingsHTML, '<div class="wdhfbps-forms-settings">');
                // Form NAME 
                array_push($cpGeneralSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_NAME.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'name';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     $wdhINPUT['type'] = 'text';

                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_NAME_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '$jWDH(\"#category-text-button-'.'1'.'\").html(window.valueNow);';
                                     
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                 // Display_type 
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_DISPLAY_TYPE_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'display_type';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'select';
                                     $wdhINPUT['values']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_DISPLAY_TYPE_VALUE_NORMAL.'@@normal|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_DISPLAY_TYPE_VALUE_POPUP.'@@popup';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_DISPLAY_TYPE_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '';
                                     $wdhINPUT['js_wdhedfp_onchange']   = '';

                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                 // Mode 
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_MODE_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'mode';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'select';
                                     $wdhINPUT['values']                =WDHFBPS_FBPS_CATEGORY_SETTINGS_MODE_VALUE_REGISTER.'@@register|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_MODE_VALUE_CONTACT.'@@contact|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_MODE_VALUE_LOGIN.'@@login';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_MODE_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;
                                     $userTypeID = 'user-type-all-1';
                                     $wdhINPUT['js_wdhedfp_after_save'] = 'if(window.valueNow != \"register\"){ $jWDH(\"#'.$userTypeID.'\").slideUp(100); } else { $jWDH(\"#'.$userTypeID.'\").slideDown(500);}';
                                     $wdhINPUT['js_wdhedfp_onchange'] = '';
                                                           
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                 // Popup_Button
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_POPUP_BUTTON_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'popup_button';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'text';
                                     $wdhINPUT['values']                = 'text';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_POPUP_BUTTON_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '';
                                     $wdhINPUT['js_wdhedfp_onchange']   = '';

                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                   // Class
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_CLASS_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'class';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'textarea';
                                     $wdhINPUT['values']                = '';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_CLASS_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '';
                                     $wdhINPUT['js_wdhedfp_onchange']   = '';

                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                   // Css
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_CSS_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'css';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'textarea';
                                     $wdhINPUT['values']                = '';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_CSS_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '';
                                     $wdhINPUT['js_wdhedfp_onchange']   = '';

                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                   // USER TYPE 
                 $userTypeDisplayStyle = '';
                 
                 if($category->mode != 'normal' && $category->mode != 'register'){
                     $userTypeDisplayStyle = 'display:none;';
                 }
                 array_push($cpGeneralSettingsHTML, '   <div id="user-type-all-1" style="'.$userTypeDisplayStyle.'">');
                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_TEXT.':</div>');

                                     // Define variables
                                     $wdhDB['table'] = WDHFBPS_Forms_table;
                                     $wdhFIELD['field_name'] = 'user_role';
                                     $wdhFIELD['json_value'] = '';
                                     $wdhFIELD['edit']       = true;
                                     $wdhFIELD['conditions'] = array( 
                                         0 => array(
                                              'field_label' => 'id',
                                              'field_value' => '1',
                                              'field_condition' => '' // Allways must be EMPTY
                                         )
                                     );
                                     
                                     $wdhINPUT['type'] = 'select';
                                     $wdhINPUT['values']                = WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_SUBSCRIBER.'@@subscriber|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_CONTRIBUTOR.'@@contributor|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_AUTHOR.'@@author|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_EDITOR.'@@editor|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_ADMINISTRATOR.'@@administrator|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_SUPER_ADMINISTRATOR.'@@super_administrator|'.WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_VALUE_AFFILIATE.'@@affiliate';
                                     // TOOLTIP
                                     $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_USER_TYPE_TEXT_INFO;
                                     $wdhTOOLTIP['position'] = 'right';
                                     // FILTER
                                     $wdhFILTER['is_required']     = true;

                                     $wdhINPUT['js_wdhedfp_after_save'] = '';
                                     $wdhINPUT['js_wdhedfp_onchange']   = '';

                 array_push($cpGeneralSettingsHTML, '       <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                 array_push($cpGeneralSettingsHTML, '   </div>');
                 array_push($cpGeneralSettingsHTML, '   </div>');
                 
                 return implode('', $cpGeneralSettingsHTML);
            }
            
            // Custom Posts Form Messages Settings
            
            function showFormFormMessagesSettings(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $cpSettingsHTML = array();
                $menu_item = 'menu-posts-wdhfbps_1'; 
                
                array_push($cpSettingsHTML, '<div class="wdhfbps-field-new-title">');
                array_push($cpSettingsHTML, '    <div class="wdhfbps-header-second">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES.'</div>');
                array_push($cpSettingsHTML, '</div>');
                array_push($cpSettingsHTML, '<div class="wdhfbps-forms-settings">');
                // FORM MESSAGES: MSG_SENT
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_SENT.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'msg_sent';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'textarea';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_SENT_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;

                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '    <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // FORM MESSAGES: MSG_FAILED
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_FAILED.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'msg_failed';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'textarea';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_FAILED_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;

                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '    <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // FORM MESSAGES: MSG_CLASS
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_CLASS.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'msg_class';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'textarea';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_CLASS_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;

                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '    <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // FORM MESSAGES: MSG_CSS
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_CSS.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'msg_css';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'textarea';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_FORM_MESSAGES_MSG_CSS_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;

                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '    <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
               
                array_push($cpSettingsHTML, '</div>');
                
                return implode('', $cpSettingsHTML);
            }
            
            // Custom Posts Email Messages Settings
            
            function showFormEmailMessagesSettings(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $cpSettingsHTML = array();
                $menu_item = 'menu-posts-wdhfbps_1'; 
                
                array_push($cpSettingsHTML, '<div class="wdhfbps-field-new-title">');
                array_push($cpSettingsHTML, '    <div class="wdhfbps-header-second">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES.'</div>');
                array_push($cpSettingsHTML, '</div>');
                array_push($cpSettingsHTML, '<div class="wdhfbps-forms-settings">');
                // Email Messages:Sender Email
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_SENDER_EMAIL.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'sender_email';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'text';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_SENDER_EMAIL_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = true;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '    <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:Admin Email Notification
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label" style="text-align:none;">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL_NOTIFICATION.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'admin_email_notification';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'switch';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL_NOTIFICATION_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value" style="width:100px;">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:Admin Email
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'admin_email';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'text';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = true;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:Admin Subject
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_SUBJECT.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'admin_subject';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'text';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_SUBJECT_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:Admin Email Template
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL_TEMPLATE.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'admin_email_template';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'html_editor';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_ADMIN_EMAIL_TEMPLATE_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value" style="width:360px">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:User Email Notification
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_NOTIFICATION.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'user_email_notification';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'switch';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_NOTIFICATION_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value" style="width:100px;">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:User Email Suject
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_SUBJECT.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'user_email_subject';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'text';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_SUBJECT_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                // Email Messages:User Email Template
                array_push($cpSettingsHTML, '    <div class="wdhfbps-label">'.WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_TEMPLATE.':</div>');
                // Define variables
                $wdhDB['table'] = WDHFBPS_Forms_table;
                $wdhFIELD['field_name'] = 'user_email_template';
                $wdhFIELD['json_value'] = '';
                $wdhFIELD['edit']       = true;
                $wdhFIELD['conditions'] = array( 
                    0 => array(
                         'field_label' => 'id',
                         'field_value' => '1',
                         'field_condition' => '' // Allways must be EMPTY
                    )
                );
                $wdhINPUT['type'] = 'html_editor';

                // TOOLTIP
                $wdhTOOLTIP['text']     = WDHFBPS_FBPS_CATEGORY_SETTINGS_EMAIL_MESSAGES_USER_EMAIL_TEMPLATE_INFO;
                $wdhTOOLTIP['position'] = 'right';
                // FILTER
                $wdhFILTER['is_required']     = true;
                $wdhFILTER['is_email']        = false;
                
                $wdhINPUT['js_wdhedfp_after_save'] = '';

                array_push($cpSettingsHTML, '   <div class="wdhfbps-value" style="width:360px">'.$this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD).'</div><br class="wdhfbps-clear">');
                
                array_push($cpSettingsHTML, '</div>');
                
                return implode('', $cpSettingsHTML);
            }
            
            function showCustomFieldsSettings(){
                global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
                $category = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="1"');
                $categoriesHTML = array();
                $wdhfbps_language_now = get_option('WDHFBPS_language');
                
                if ($wdhfbps_language_now == ''){
                    $wdhfbps_language_now = 'en';
                }
                
                $customFieldsDisplayStyle = '';
                
                $customFieldsDisplayStyle = "display:block;";
                
                array_push($categoriesHTML,  '          <div class="wdhfbps-field-new-title">
                                                            <div class="wdhfbps-header">
                                                                <span class="wdhfbps-header-text">'.WDHFBPS_FBPS_CATEGORY_FIELD_SETTINGS.'</span>
                                                            </div>
                                                        </div>
                                                        <div id="wdhfbps-custom-fields-all-display-for-1" style="'.$customFieldsDisplayStyle.'">
                                                            <div class="wdhfbps-add-field" onclick="wdhfbpsNewField(1);">
                                                                <div class="add-field-plus">&nbsp;</div>
                                                                <div class="add-field-text">'.WDHFBPS_FBPS_CATEGORY_ADD_FIELD.'</div>
                                                            </div>
                                                            <div class="wdhfbps-field-new-title" style="margin-top:10px;">
                                                                <select id="category-settings-language-1" onchange="wdhfbpsChangeName(1,this.value);" class="wdhfbps-language">
                                                                   '.$this->wdhLibs->getLanguagesOptions($wdhfbps_language_now).'
                                                                </select>
                                                            </div>
                                                            <div id="wdhfbps-fields-1" class="wdhfbps-fields  wdhfbps-fields-move">
                                                                '.$this->showFieldsSettings().'
                                                            </div>
                                                        </div>');
                $wdhINPUT['js_wdhedfp_onchange'] = '';
                return implode('', $categoriesHTML);
            }
            
            function wdhFieldSettingsData($fieldId){
                global $wpdb;
                $query = "SELECT * FROM " . WDHFBPS_Forms_fields_table . " WHERE id='$fieldId'";
                $results = $wpdb->get_row($query);
                
                return $results;
            }
            
            function returnConstants($prefix){
                foreach (get_defined_constants() as $key=>$value) 
                    if (substr($key,0,strlen($prefix))==$prefix)  $dump[$key] = $value; 
                if(empty($dump)) { return "Error: No Constants found with prefix '".$prefix."'"; }
                else { return $dump; }
            }
        }
}

