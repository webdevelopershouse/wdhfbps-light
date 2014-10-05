<?php

/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder Light
* Version                 : 1.0
* File                    : wdhfbps.site.php
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Light Site Class.
*/

if (!class_exists("WDHFormBuilderwithPaymentSystemSite")){
    class WDHFormBuilderwithPaymentSystemSite{
        private $WDHFBPS_Display;
        private $WdhEditFieldDb;
        private $WDHFormGenerator;
        private $wdhLibs;

        function WDHFormBuilderwithPaymentSystemSite(){// Constructor.
            // Start Session
            add_action('wp_loaded', array(&$this, 'startSession'));
            
            global $form;
            // Get selected language
            $site_language = get_option('WDHFBPS_site_language');

            if ($site_language == ''){
                $site_language = 'en';
                add_option('WDHFBPS_site_language', 'en');
            }
            
            // GET DROP DOWN LANGUAGES
            if (isset($_GET["lang"])){
               $site_language = $_GET["lang"];
            }
            
            // Include language file.
            include "languages/site/".$site_language.".php";
            
            // Add CSS & JS
            add_action('wp_enqueue_scripts', array(&$this, 'addCSS'));
            add_action('wp_enqueue_scripts', array(&$this, 'addJS'));
                        
            $this->defConstants();

            add_shortcode('wdhfbps-light', array(&$this, 'showFormfromShortcode'));
            
            // INIT EDFP
            if (class_exists("WdhEditFieldDb")){
                $this->WdhEditFieldDb = new WdhEditFieldDb();
            }
            
            // INIT Form Generator
            if (class_exists("wdhFormGenerator")){
                $this->WDHFormGenerator = new wdhFormGenerator();
            }
            
            // INIT LIBS     
            if (class_exists("wdhLibs")){
                $this->wdhLibs = new wdhLibs();
            }
        }
        
        function startSession() {
            
            if (session_id() == "") {
                session_start();
            }
        }
        
        function defConstants(){// Constants define.
            global $wpdb;
            global $wdhFBPS_CONFIG;

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

            if (!defined('WDHFBPS_Forms_fields_table')) { // Forms Fields
                define('WDHFBPS_Forms_fields_table', $wpdb->prefix.'wdhfbps_forms_fields');
            }

            if (!defined('WDHFBPS_Forms_fields_values_table')) { // Forms Fields Values
                define('WDHFBPS_Forms_fields_values_table', $wpdb->prefix.'wdhfbps_forms_fields_values');
            }

        }
        
        function addCSS(){
            // Register Styles.
            wp_register_style('WDHFBPS_WDHFormBuilderwithPaymentSystem_CSS', plugins_url('stuff/css/wdh.im.FBPS.site.css', __FILE__));  
            
            // Enqueue Styles.
            wp_enqueue_style('WDHFBPS_WDHFormBuilderwithPaymentSystem_CSS');
        }

        function addJS(){
            // Register JavaScript.

            wp_register_script('WDHFBPS_JSON2', plugins_url('wdhedflwl/js/json2.js', __FILE__), array('jquery'));
            wp_register_script('WDHFBPS_SITE_JS', plugins_url('stuff/js/wdh.im.FBPS.site.js', __FILE__), array('jquery'));                

            // Enqueue JavaScript.
            if (!wp_script_is('jquery', 'queue')){
                wp_enqueue_script('jquery');
            }
            
            if (!wp_script_is('jquery-ui-core', 'jquery')){
                wp_enqueue_script('jquery-ui-core');
            }
            
            if (!wp_script_is('jquery-ui-sortable', 'jquery')){
                wp_enqueue_script('jquery-ui-sortable');
            }
            
            if (!wp_script_is('jquery-ui-datepicker', 'jquery')){
                wp_enqueue_script('jquery-ui-datepicker');
            }

            wp_enqueue_script('WDHFBPS_JSON2');
            wp_enqueue_script('WDHFBPS_SITE_JS');  
        }
        
        // ShortCodes
        
        function showFormfromShortcode($atts){// Display Label
            
            $shortcodeHTML = array();
            
            // GET PLUGIN LANGUAGE
            $wdhfbps_language_now = get_option('WDHFBPS_language');
            $language = $wdhfbps_language_now;
            // GET SHORTCODE DATA
            extract(shortcode_atts(array(
                'class' => 'wdhfbps-light',
            ), $atts));
            
            // GET SHORTCODE LANGUAGE
            if(isset($atts['lang'])) {
                $language = $atts['lang'];
            }
            
            // GET DROP DOWN LANGUAGES
            if (isset($_GET["lang"])){
                $language = $_GET["lang"];
            }
            
            // Include language file.
            include_once "languages/site/".$language.".php";
            
            // Display Form code.
            array_push($shortcodeHTML, $this->showForm($language));
            
            
            return implode("", $shortcodeHTML);
        }
        
        function showField($fieldID, $language, $showtype){
            global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD;
            global $form;
            
            $fieldHTML = array();
            
            // INIT EDFP
            if (class_exists("WdhEditFieldDb")){
                $this->WdhEditFieldDb = new WdhEditFieldDb();
            }
            
            $wdhfbps_language_now = $language;
            $field = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where id="'.$fieldID.'"');
            
            if ($showtype == 'label') {
                
                if ($field->name){
                    
                    $fieldDate              = json_decode(stripcslashes($field->name));
                    $fieldname              = $fieldDate->$language;
                    array_push($fieldHTML,   $fieldname);
                }
                
            } else {
                
                if ($field->name){
                    $fieldDate              = json_decode(stripcslashes($field->name));
                    $fieldname              = $fieldDate->$language;
                    $fieldType              = '';
                    $fieldType              = $field->edit_type;
                    $wdhFIELD['edit']       = false;
                    
                    // Define variables
                    $wdhDB['table']         = WDHFBPS_Forms_fields_values_table;
                    $wdhFIELD['table']      = WDHFBPS_Forms_fields_values_table;
                    $wdhFIELD['field_name'] = 'value';
                    $wdhFIELD['json_value'] = $language;
                    $wdhFIELD['conditions'] = array( 
                        0 => array(
                             'field_label' => 'field_id',
                             'field_value' => $field->id,
                             'field_condition' => '' // Allways must be EMPTY
                        ),
                        1 => array(
                             'field_label' => 'form_id',
                             'field_value' => $form->ID,
                             'field_condition' => 'AND'
                        )
                    );
                    $fieldValues = json_decode(stripcslashes($field->values_list));
                    $fieldValuesList = $fieldValues->$wdhfbps_language_now;
                    $wdhINPUT['type'] = $field->edit_type;
                    $wdhINPUT['values'] = $fieldValuesList;
                    
                    // TOOLTIP
                    $wdhTOOLTIP['text'] = WDHFBPS_FBPS_CUSTOMER_FIELD_EDIT;
                    $wdhTOOLTIP['position'] = 'right';
                    // FILTER
                    $wdhFILTER['is_required'] = $field->is_required; 
                    $wdhFILTER['is_email'] = $field->is_email; 
                    $wdhFILTER['is_url'] = $field->is_url; 
                    $wdhFILTER['is_phone'] = $field->is_phone; 
                    $wdhFILTER['is_alpha'] = $field->is_alpha;
                    $wdhFILTER['is_numeric'] = $field->is_numeric;
                    $wdhFILTER['is_alphanumeric'] = $field->is_alphanumeric;
                    $wdhFILTER['is_date'] = $field->is_date;
                    $wdhFILTER['is_unique'] = $field->is_unique;
                    
                    array_push($fieldHTML,      $this->WdhEditFieldDb->wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD));
                }
            }
            echo implode("\n", $fieldHTML);
        }
        
        function showForm($language){
            global $wpdb,$wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhSettings;
            
            $formHTML = array();
            $formID = '1';
            // INIT EDFP
            if (class_exists("WdhEditFieldDb")){
                if (isset($_GET['wdh_payment'])) {
                
                    if ($_GET['wdh_payment'] == 'success') {
                        $this->WdhEditFieldDb = new WdhEditFieldDb(false);
                    }
                } else {
                    $this->WdhEditFieldDb = new WdhEditFieldDb();
                }
            }
            
            // INIT Form Generator
            if (class_exists("wdhFormGenerator")){
                $this->WDHFormGenerator = new wdhFormGenerator();
            }
            
            $wdhfbps_language_now = $language;
            $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="'.$formID.'"');
            
            if (!defined('WDHFBPS_Forms_fields_values_table')) { // Forms Fields Values
                define('WDHFBPS_Forms_fields_values_table', $wpdb->prefix.'wdhfbps_forms_fields_values');
            }
            
            if (isset($_GET['wdh_payment'])) {
                
                if ($_GET['wdh_payment'] == 'success') {
                        $wdhSettings_mod = json_decode($_SESSION['wdhPS_Settings']);
                        $this->WdhEditFieldDb->insertFields(json_decode(stripslashes($_SESSION['wdhPS_Fields'])), $wdhSettings_mod, "paid");
                }
            }
            
            if(count($form) > 0){
            
                if ($form->mode == 'register'){
                    $form->mode = 'normal';
                }
                
                $wdhSettings = (array)$wdhSettings;
                // ----------------------------------
                // ---- FORM SETTINGS ---------------
                // ----------------------------------
                $wdhSettings['form_id']           = $form->id;                          // set form id (a number from 1 - 9999...)
                $wdhSettings['form_type']         = $form->display_type;                // set the way you wan to display from(normal or popup - display form in popup box)
                $wdhSettings['form_mode']         = $form->mode;                        // normal: save in db + send emails , contact: only send emails, login: only login;
                $wdhSettings['form_design']       = $form->design;                      // fixed or responsive design
                $wdhSettings['form_popup_button'] = $form->popup_button;                // set text for open popup button(only if you use popup)
                $wdhSettings['form_class']        = $form->class.' wdhfbps-form-container';                       // if you want to add other css class or classes in your form just write here with space between
                $wdhSettings['form_css']          = $form->css.'width:'.$form->form_width.'px;color:#'.$form->text_color.';font-family:'.$form->text_font_family.';font-family:'.$form->text_own_font.';font-size:'.$form->text_font_size.'px;font-style:'.$form->text_font_style.';font-weight:'.$form->text_font_weight.';text-align:'.$form->text_align.';margin-left:'.$form->box_margin_left.'%;margin-right:'.$form->box_margin_right.'%;margin-top:'.$form->box_margin_top.'px;margin-bottom:'.$form->box_margin_bottom.'px;padding-left:'.$form->box_padding_left.'%;padding-right:'.$form->box_padding_right.'%;padding-top:'.$form->box_padding_top.'px;padding-bottom:'.$form->box_padding_bottom.'px;background-color:#'.$form->box_background_color.';border-color:#'.$form->box_border_color.';border-width:'.$form->box_border_size.'px;border-style:'.$form->box_border_type.';border-radius:'.$form->box_border_radius.'px;width:'.$form->form_width.'px;'; // if you want to add other css propeties in your form just write here like in example: array('color' => '#000');
                $wdhSettings['form_width']        = $form->form_width;


                // ----------------------------------
                // ---- FORM MESSAGES ---------------
                // ----------------------------------
                $wdhSettings['form_msg_sent']   = $form->msg_sent;                          // Set message to be displayed after form is sent succesfully
                $wdhSettings['form_msg_failed'] = $form->msg_failed;                        // Set message to be display if form can not be sent ( failed message )
                $wdhSettings['form_msg_class']  = $form->msg_class;                         //  if you want to add other css class or classes in your form message box
                $wdhSettings['form_msg_css']    = $this->generateCssArray($form->msg_css);  //  if you want to add other css propeties in your form message box like in example: array('color' => '#000');



                // ----------------------------------
                // ---- EMAIL MESSAGES --------------
                // ----------------------------------
                $wdhSettings['admin_email_notification'] = $form->admin_email_notification;     // set true if you want to be notified if someone sent a form or false if you don't want to be notified
                $wdhSettings['admin_email']              = $form->admin_email;                  // Set admin email where you want to be notify
                $wdhSettings['admin_subject']            = $form->admin_subject;                // Set admin email subject
                $wdhSettings['admin_email_template']     = '';                                  // Set admin email template.you can edit template : open email_templates/standard/admin.html
                $wdhSettings['sender_email']             = $form->sender_email;                 // Set email sender
                $wdhSettings['user_email_notification']  = $form->user_email_notification;      // set true if you want to send a notification(s) to user when user sent a form or false if you don't want to send notification(s) to user 
                $wdhSettings['user_email_subject']       = $form->user_email_subject;           // Set user notification email subject
                $wdhSettings['user_email_template']      = '';                                  // Set admin email template.you can edit template : open email_templates/standard/user.html

                // ----------------------------------
                // ---- JS HOOK AFTER SEND ----------
                // ----------------------------------
                $wdhSettings['js_wdhedfp_after_save'] = $form->js_wdhedfp_after_save;  // Set your javascript code here
                // Return value is window.valueNow - Write every javascript code you want with escape ( use instead of " this \" and $jWDH instead of $ or jQuery).

                if ((($form->mode == 'comment' || $form->mode == 'post') && is_user_logged_in()) || $form->mode == 'normal' || $form->mode == 'register' || $form->mode == 'login' || $form->mode == 'contact'){
                    // ----------------------------------
                    // ---- START FORM ------------------
                    // ----------------------------------
                     array_push($formHTML, $this->WDHFormGenerator->startForm($wdhSettings));
                    // ----------------------------------

                    // -----------------
                    if ($form->mode == 'register' || $form->mode == 'normal'){
                        $wdhFIELD['label']            = WDHFBPS_FBPS_USER_TYPE;
                        $wdhFIELD['name']             = 'value'; 
                        $wdhDB['table']               = WDHFBPS_Forms_fields_values_table;
                        $wdhFIELD['table']            = WDHFBPS_Forms_fields_values_table;   // Set your database table
                        $wdhINPUT['type']             = 'user_type';
                        $wdhFIELD['value']            = $form->user_role;
                        array_push($formHTML, $this->WDHFormGenerator->field($wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhSettings));
                    }
                    // -----------------

                        // ----------------------------------
                        // ---- FORM FIELDS -----------------
                        // ----------------------------------

                        $fields = $wpdb->get_results('SELECT * FROM '.WDHFBPS_Forms_fields_table.' where cat_id="'.$form->id.'"  ORDER by display_position ASC, id ASC');

                        foreach($fields as $field){
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
                               $minHeight = 'min-'; 
                            }

                            // -------------------------------
                            // ---- FIELD --------------------
                            // -------------------------------
                            // ---- DATABASE SETTINGS --------
                            $wdhFIELD['name']             = 'value';          // Set your database field name
                            $wdhDB['table']               = WDHFBPS_Forms_fields_values_table;
                            $wdhFIELD['table']            = WDHFBPS_Forms_fields_values_table;   // Set your database table 
                            // ---- FIELD ID ----------------
                            $wdhFIELD['id']               = $field->id;            // Set field id ( write a distinct number from the others fiels: from 1 - 9999... )
                            // Field Settings
                            $wdhFIELD['field_class']    = ' wdhfbps-form-field-container';      // field css class
                            $wdhFIELD['field_css']      = 'color:#'.$field->text_color.';font-family:'.$field->text_font_family.';font-family:'.$field->text_own_font.';font-size:'.$field->text_font_size.'px;font-style:'.$field->text_font_style.';font-weight:'.$field->text_font_weight.';text-align:'.$field->text_align.';margin-left:'.$field->box_margin_left.'%;margin-right:'.$field->box_margin_right.'%;margin-top:'.$field->box_margin_top.'px;margin-bottom:'.$field->box_margin_bottom.'px;padding-left:'.$field->box_padding_left.'%;padding-right:'.$field->box_padding_right.'%;padding-top:'.$field->box_padding_top.'px;padding-bottom:'.$field->box_padding_bottom.'px;background-color:#'.$field->box_background_color.';border-color:#'.$field->box_border_color.';border-width:'.$field->box_border_size.'px;border-style:'.$field->box_border_type.';border-radius:'.$field->box_border_radius.'px;width:'.$field->width.'%;min-height:'.$field->height.'px;';        // field css
                            // ---- FIELD LABEL -------------
                            $fieldDate                    = json_decode(stripcslashes($field->name));
                            $fieldname                    = $fieldDate->$language;

                            if ($field->edit_type == 'title' || $field->edit_type == 'paragraph'){
                                $fieldDate = json_decode(stripslashes($field->label_value));
                                $fieldname = $fieldDate->$wdhfbps_language_now;
                            }

                            $wdhFIELD['label']            = $fieldname;   // Set field label

                            $fieldsecondname              = '';
                            $wdhFIELD['label_position']   = $field->display_label_position;

                            if(isset($field->second_name) && $field->second_name != ''){
                                $fieldsecondDate          = json_decode(stripcslashes($field->second_name));
                                $fieldsecondname          = $fieldsecondDate->$language;
                            }

                            $wdhFIELD['second_label']     = $fieldsecondname;             // Set second field label ( only for field type: password & form type != login )
                            $wdhFIELD['class']            = $field->label_class.' wdhfbps-form-field-label-container';             // if you want to add other css class or classes in field label just write here with space between
                            $wdhFIELD['css']              = $field->label_css.'color:#'.$field->text_label_color.';'.$labelFontFamily.';font-size:'.$field->text_label_font_size.'px;font-style:'.$field->text_label_font_style.';font-weight:'.$field->text_label_font_weight.';text-align:'.$field->text_label_align.';margin-left:'.$field->box_label_margin_left.'%;margin-right:'.$field->box_label_margin_right.'%;margin-top:'.$field->box_label_margin_top.'px;margin-bottom:'.$field->box_label_margin_bottom.'px;padding-left:'.$field->box_label_padding_left.'%;padding-right:'.$field->box_label_padding_right.'%;padding-top:'.$field->box_label_padding_top.'px;padding-bottom:'.$field->box_label_padding_bottom.'px;background-color:#'.$field->box_label_background_color.';border-color:#'.$field->box_label_border_color.';border-width:'.$field->box_label_border_size.'px;border-style:'.$field->box_label_border_type.';border-radius:'.$field->box_label_border_radius.'px;width:'.$field->label_width.'%;min-height:'.$field->label_height.'px;';        // if you want to add other css propeties in field label like in example: array('color' => '#000');
                            // ---- FIELD INPUT -------------
                            $wdhINPUT['type']             = $field->edit_type;         // text, textarea, select, radio, checkbox, date, password, switch , link, hidden, map, video, html_editor, colorpicker, price, image, file, captcha, submit
                            $wdhINPUT['class']            = $field->input_class.' wdhfbps-form-field-input-container';             // if you want to add other css class or classes in field input just write here with space between
                            $wdhINPUT['css']              = $field->input_css.'color:#'.$field->text_input_color.';'.$inputFontFamily.';font-size:'.$field->text_input_font_size.'px;font-style:'.$field->text_input_font_style.';font-weight:'.$field->text_input_font_weight.';text-align:'.$field->text_input_align.';padding-left:'.$field->box_input_padding_left.'%;padding-right:'.$field->box_input_padding_right.'%;padding-top:'.$field->box_input_padding_top.'px;padding-bottom:'.$field->box_input_padding_bottom.'px;background:#'.$field->box_input_background_color.';border-color:#'.$field->box_input_border_color.';border-width:'.$field->box_input_border_size.'px;border-style:'.$field->box_input_border_type.';border-radius:'.$field->box_input_border_radius.'px;'.';'.$minHeight.'height:'.$field->input_height.'px;margin-left:'.$field->box_input_margin_left.'%;margin-right:'.$field->box_input_margin_right.'%;margin-top:'.$field->box_input_margin_top.'px;margin-bottom:'.$field->box_input_margin_bottom.'px;width:'.$inputWidth.'%;border-radius:'.$field->box_input_border_radius.'px;%;border-width:'.$field->box_input_border_size.'px;border-color:#'.$field->box_input_border_color.';border-style:'.$field->box_input_border_type.';'.$minHeight.'height:'.$field->input_height.'px;';        // if you want to add other css propeties in field input like in example: array('color' => '#000');
                            $fieldvalues                  = '';
                            

                            if(isset($field->values_list)){
                                $fieldvaluesDate          = json_decode(stripcslashes($field->values_list));
                                $fieldvalues              = $fieldvaluesDate->$language;
                            }

                            $wdhINPUT['values']           = $fieldvalues;             // Set values ( only for select, radio and checkbox ) like in example: value 1@@label 1|value 2@@label 2
                            // ---- FIELD FILTERS -------------
                            $wdhFILTER['is_required']     = $this->filterIsSelected($field->is_required);           // Set true to add required filter
                            $wdhFILTER['is_email']        = $this->filterIsSelected($field->is_email);          // Set true to add email filter
                            $wdhFILTER['is_url']          = $this->filterIsSelected($field->is_url);          // Set true to add url filter
                            $wdhFILTER['is_phone']        = $this->filterIsSelected($field->is_phone);          // Set true to add phone filter
                            $wdhFILTER['is_alpha']        = $this->filterIsSelected($field->is_alpha);           // Set true to add alpha filter
                            $wdhFILTER['is_numeric']      = $this->filterIsSelected($field->is_numeric);          // Set true to add numeric filter
                            $wdhFILTER['is_alphanumeric'] = $this->filterIsSelected($field->is_alphanumeric);          // Set true to add alphanumeric filter
                            $wdhFILTER['is_date']         = $this->filterIsSelected($field->is_date);          // Set true to add date filter
                            $wdhFILTER['is_unique']       = $this->filterIsSelected($field->is_unique);          // Set true to add unique filter
                            $wdhFILTER['is_adult_video']  = $this->filterIsSelected($field->is_adult_video);          // set true if you allow Adult video
                            // ---- FIELD TOOLTIP -------------
                            $wdhTOOLTIP['text']           = $field->tooltip_text; // If you want to add tooltip message write message here
                            // ---- DISPLAY FIELD -------------
                            array_push($formHTML, $this->WDHFormGenerator->field($wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhSettings));

                        }
                    
                    // ----------------------------------
                    // ---- END FORM --------------------
                    // ----------------------------------
                    array_push($formHTML, $this->WDHFormGenerator->endForm($wdhSettings));
                    // ----------------------------------
                } else {
                    switch ($form->mode) {
                        case "comment":
                            array_push($formHTML, WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_COMMENT_LOGIN);
                            break;
                        case "post":
                            array_push($formHTML, WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_POST_LOGIN);
                            break;
                        default:
                            array_push($formHTML, WDHFBPS_FBPS_CATEGORY_FIELD_TYPE_COMMENT_LOGIN);
                            break;
                    } 
                }
            
            }

            
            echo implode("\n", $formHTML);
        }
        
        // Is Selected Filter
        function filterIsSelected($value){
            $isSelected = false;

            if ($value == 'true'){
                $isSelected = true;
            }

            return $isSelected;
        }
        
        function generateCssArray($formCSS){
            
        }
           
        // Get IP Address
        function getRealIpAddr() {
            //check ip from share internet
            if (!empty($_SERVER['HTTP_CLIENT_IP'])){
              $ip=$_SERVER['HTTP_CLIENT_IP'];
            }//to check ip is pass from proxy
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
              $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
              $ip=$_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }

        // Get Data : Country , City
        function geIpData($ipAddress){
            $url = 'http://www.geoplugin.net/json.gp?ip='.$ipAddress;
            $json = file_get_contents($url);
            $obj = json_decode($json);
            return $obj;
        }
    }
}
