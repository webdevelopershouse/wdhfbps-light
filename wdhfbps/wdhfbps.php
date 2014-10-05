<?php

/*
Plugin Name: Synoptic WordPress Responsive Visual Form Builder Light
Version: 1.0
Plugin URI: http://www.wdh.im/projects/synoptic-wordpress-responsive-visual-form-builder-light/
Description: Synoptic WordPress Responsive Visual Form Builder Light is a wordpress plugin which helps you to build very easy and fast multilanguage forms.This plugin helps you to create and edit 11 types of fields ( text, title, paragraf, password, username, textarea, select, radio, checkbox, captcha, submit) with whom you can build 3 types of forms:Contact Form, Register Form , Login Form, .You can edit the fields with Live Visual Editor which has 70 editable features for every field and 60 for the form you like to customize,you can set the messages setting which you like with Message Settings..
Author: Web Developers House
Author URI: http://www.wdh.im

Change log:

        1.0 (2014-09-01)
	
		* Initial release.
		
Installation: Upload the folder synoptic-form-builder from the zip file to "wp-content/plugins/" and activate the plugin in your admin panel or upload synoptic-form-builder.zip in the "Add new" section.
 */
    include_once 'wdhfbps.config.php';
    include_once 'wdhfbps.libs.php';
    include_once 'display/content.php';
    include_once 'wdhfbps.update.php';
    include_once 'wdhfbps.site.php';
    include_once 'wdhfbps.admin.forms.php';
    include_once 'wdhfbps.uninstall.php';
    // WDH WP EDFP LIBRARY
    include_once 'wdhedflwl/wdh.edfp.php';
        
    // Get selected language
    $backend_language = get_option('WDHFBPS_language');

    if ($backend_language == ''){
        $backend_language = 'en';
        add_option('WDHFBPS_language', 'en');
    }
   
    global $wdhFIELD;
    global $WDHFBPS_plugin;
    global $wdhPluginIsStart;
    $wdhPluginIsStart = false;
    
    $wdhFIELD['language']    = $backend_language;
    
    if (is_admin()){// If admin is loged in admin init administration panel.
        $pluggable = '../wp-includes/pluggable.php';
        
        if (!file_exists($pluggable)){
            $pluggable_file = '../../wp-includes/pluggable.php';
        }
        include_once $pluggable;

        // Include language file.
        include "languages/admin/".$backend_language.".php";

        if (class_exists("WDHFormBuilderwithPaymentSystemAdmin")){
            $WDHFBPS_plugin = new WDHFormBuilderwithPaymentSystemAdmin();
        }

        if (class_exists("WdhEditFieldDb")){
            $WDH_EditField = new WdhEditFieldDb();
        }

        if (!function_exists("WDHFormBuilderwithPaymentSystemAdmin_ap")){// Initialize the admin panel.
            function WDHFormBuilderwithPaymentSystemAdmin_ap(){
                global $WDHFBPS_plugin;
                global $WDH_EditField;

                if (!isset($WDHFBPS_plugin)){
                    return;
                }
                
                $role_action = 'manage_options';
                
                $WDHFBPS_plugin->createTables();

                    if (function_exists('add_options_page')){
                        add_menu_page(WDHFBPS_TITLE, WDHFBPS_TITLE, $role_action, 'wdhfbps', array(&$WDHFBPS_plugin, 'displayFormsPage'), plugins_url('stuff/images/small-logo.png', __FILE__));
                        add_submenu_page('wdhfbps', WDHFBPS_FBPS_CP_TITLE, WDHFBPS_FBPS_CP_TITLE, 'manage_options', 'wdhfbps', array(&$WDHFBPS_plugin, 'displayFormsPage'));
                    }
            }
        }

        if (isset($WDHFBPS_plugin)){// Init AJAX functions.
            add_action('admin_menu', 'WDHFormBuilderwithPaymentSystemAdmin_ap');
            // Categories
            add_action('wp_ajax_wdhfbps_show_categories', array(&$WDHFBPS_plugin, 'showCategories'));
            
            // Display Forms
            add_action('wp_ajax_wdhfbps_display_forms', array(&$WDHFBPS_plugin, 'displayForms'));
            
            // Forms Settings
            add_action('wp_ajax_wdhfbps_display_form_settings', array(&$WDHFBPS_plugin, 'displayFormSettings'));
            add_action('wp_ajax_wdhfbps_update_form_size_new', array(&$WDHFBPS_plugin, 'updateNewFormSize'));
            
            // Messages
            add_action('wp_ajax_wdhfbps_display_form_visual_editor', array(&$WDHFBPS_plugin, 'displayVisualEditor'));
            
            // Messages
            add_action('wp_ajax_wdhfbps_display_form_messages', array(&$WDHFBPS_plugin, 'displayMessages'));
            
            // Fields
            add_action('wp_ajax_wdhfbps_save_field', array(&$WDHFBPS_plugin, 'saveField'));
            add_action('wp_ajax_wdhfbps_delete_field', array(&$WDHFBPS_plugin, 'deleteField'));
            add_action('wp_ajax_wdhfbps_add_field', array(&$WDHFBPS_plugin, 'addField'));
            add_action('wp_ajax_wdhfbps_show_fields_by_language', array(&$WDHFBPS_plugin, 'showFieldsByLanguage'));
            add_action('wp_ajax_wdhfbps_update_form_fields_position', array(&$WDHFBPS_plugin, 'updateFieldsPosition'));
            add_action('wp_ajax_wdhfbps_update_form_fields_position_new', array(&$WDHFBPS_plugin, 'updateNewFieldsPosition'));
            add_action('wp_ajax_wdhfbps_update_form_fields_input_position_new', array(&$WDHFBPS_plugin, 'updateNewFieldsInputPosition'));
            add_action('wp_ajax_wdhfbps_update_form_fields_size_new', array(&$WDHFBPS_plugin, 'updateNewFormFieldsSize'));
            add_action('wp_ajax_wdhfbps_update_form_fields_label_size_new', array(&$WDHFBPS_plugin, 'updateNewFormFieldsLabelSize'));
            add_action('wp_ajax_wdhfbps_update_form_fields_input_size_new', array(&$WDHFBPS_plugin, 'updateNewFormFieldsInputSize'));
            
            // Visual Editor
            add_action('wp_ajax_wdhfbps_edit_form', array(&$WDHFBPS_plugin, 'formControlPanel'));
            add_action('wp_ajax_wdhfbps_edit_form_field', array(&$WDHFBPS_plugin, 'formFieldControlPanel'));
            add_action('wp_ajax_wdhfbps_edit_form_field_label', array(&$WDHFBPS_plugin, 'formFieldLabelControlPanel'));
            add_action('wp_ajax_wdhfbps_edit_form_field_input', array(&$WDHFBPS_plugin, 'formFieldInputControlPanel'));
            add_action('wp_ajax_wdhfbps_edit_form_field_all', array(&$WDHFBPS_plugin, 'formFieldControlPanelAll'));
            add_action('wp_ajax_wdhfbps_edit_form_field_label_all', array(&$WDHFBPS_plugin, 'formFieldLabelControlPanelAll'));
            add_action('wp_ajax_wdhfbps_edit_form_field_input_all', array(&$WDHFBPS_plugin, 'formFieldInputControlPanelAll'));
            add_action('wp_ajax_wdhfbps_paste_form_field', array(&$WDHFBPS_plugin, 'formFieldVisualEditor'));
            add_action('wp_ajax_wdhfbps_duplicate_form_field', array(&$WDHFBPS_plugin, 'formDuplicateField'));
            
            // Change Language      
            add_action('wp_ajax_wdhfbps_change_language', array(&$WDHFBPS_plugin, 'changeLanguage'));
        }
    } 
    else {// Frontend
            if (class_exists("WDHFormBuilderwithPaymentSystemSite")){
                global $WDHFBPS_plugin;
                $WDHFBPS_plugin = new WDHFormBuilderwithPaymentSystemSite();
            }
            
            if (isset($WDHFBPS_plugin)){
                // Save form data     
                add_action('wp_ajax_nopriv_wdhfbps_save_form_data', array(&$WDHFBPS_plugin, 'saveFormData'));
                add_action('wp_ajax_wdhfbps_save_form_data', array(&$WDHFBPS_plugin, 'saveFormData'));
            }
    }
    
// Uninstall Hook
register_uninstall_hook(__FILE__, 'wdhfbpsUninstall');
