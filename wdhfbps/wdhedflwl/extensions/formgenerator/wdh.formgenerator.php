<?php

//Project Name: WDH - Form Generator ( EDF LIGHT Extension )
//Project Version: 1.0
//Author: WDH - Web Developers House
//Author URL: http://www.wdh.im/
//File: wdh.formgenerator.php
//File Description: Form Generator PHP Class
//File Version: 1.0
//Last Update File : 04.10.2014
//
//Change log:
//		
//        1.0 (2014-10-04)
//	
//		* Initial release.



if (!class_exists("wdhFormGenerator")) {
    
    class wdhFormGenerator{
        function wdhFormGenerator(){
            
            // Define EDFP Path
            if (!defined('EDFP_PATH')) {
                define('EDFP_PATH', dirname(__FILE__) . '/');
            }
            
            // Config File
            include_once EDFP_PATH . 'edfp-config.php';
            
            // Define Globals
            global $wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $edfp;
            
            // Init EDFP
            if (!class_exists("WdhEditFieldDb")) {
                $edfp = new WdhEditFieldDb();
            }
            
        }
        
        function startForm($wdhSettings){
            global $post;
            $formHTML = array();
            $payment  = '';
            $affiliate = '';
            $form_popup_display = '';
            $form_top = '';
            $postID = 0;
            
            if (isset($_GET['wdh_payment'])) {
                $payment = $_GET['wdh_payment'];
            }
            
            if ($wdhSettings['form_type'] == 'popup') {
                $form_popup_display = 'display:none;';
                $form_top = 'top:60px;';
            }
            
            $success_display = 'display:none';
            $form_display    = 'display:block';
            
            if (isset($payment) && $payment == 'success') {
                $success_display = 'display:block';
                $form_display    = 'display:none';
                $form_popup_display = 'display:block';
            }
            
            if ($wdhSettings['form_type'] == 'popup') {
                array_push($formHTML, '<div class="wdh-edfp-show-popup wdh-edfp-show-form-button" id="wdh-edfp-show-form-id-' . $wdhSettings['form_id'] . '">' . $wdhSettings['form_popup_button'] . '</div>');
                array_push($formHTML, '<div class="wdh-edfp-popup" id="wdh-edfp-popup-form-id-' . $wdhSettings['form_id'] . '" style="'.$form_popup_display.'">');
            }
            
            // JAVASCRIPT
            array_push($formHTML, '<script type="text/javascript">');
            array_push($formHTML, "     window.website_url = '" . $wdhSettings['WDH_WEBSITE_URL'] . "';");
            array_push($formHTML, '</script>');
            // FORM
            array_push($formHTML, '<form role="form" id="wdh-edfp-form-id-' . $wdhSettings['form_id'] . '" action="' . $wdhSettings['WDH_WEBSITE_URL'] . 'extensions/paymentsystem/paypal/expresscheckout.php" method="post" class="wdh-edfp-form ' . $wdhSettings['form_class'] . '" style="' . $this->generateCSS($wdhSettings['form_css']) . $form_top . '">');
            array_push($formHTML, '  <div id="wdh-edfp-form-content-id-' . $wdhSettings["form_id"] . '" style="' . $form_display . '">');

            if (isset($post->ID)) {
                $postID = $post->ID;
            }
            
            array_push($formHTML, '     <input id="wdh-form-id" type="hidden" name="wdh-form-id" value="' . $wdhSettings['form_id'] . '"/>');
            array_push($formHTML, '     <input id="wdh-form-post-id-' . $wdhSettings['form_id'] . '" type="hidden" name="wdh-form-amount-id-' . $wdhSettings['form_id'] . '" value="' . $postID . '"/>');
            array_push($formHTML, '     <input id="wdh-form-design-id-' . $wdhSettings['form_id'] . '" type="hidden" name="wdh-form-design-id-' . $wdhSettings['form_id'] . '" value="' . $wdhSettings['form_design'] . '"/>');
            array_push($formHTML, '     <input id="wdh-form-page-id-' . $wdhSettings['form_id'] . '" type="hidden" name="wdh-form-page-id-' . $wdhSettings['form_id'] . '" value="' . $this->wdhFullURL($_SERVER) . '"/>');
            array_push($formHTML, '     <input id="wdh-form-fields-id-' . $wdhSettings['form_id'] . '" type="hidden" name="wdh-form-fields-id-' . $wdhSettings['form_id'] . '"/>');
            array_push($formHTML, '     <input id="wdh-form-settings-id-' . $wdhSettings['form_id'] . '" type="hidden" name="wdh-form-settings-id-' . $wdhSettings['form_id'] . '"/>');
            
            if ($wdhSettings['form_type'] == 'popup') {
                array_push($formHTML, ' <div id="wdh-edfp-form-close-id-' . $wdhSettings["form_id"] . '" class="wdh-close">x</div>');
            }
            
            array_push($formHTML, '     <div id="wdh-edfp-form-loader-id-' . $wdhSettings["form_id"] . '" class="wdh-loader"></div>');
            
            return implode('', $formHTML);
        }
        
        function endForm($wdhSettings){
            global $wdhERROR, $wdhINPUT, $wdhDB, $wdhUPLOAD;
            
            $wdhUPLOAD_json   = json_encode($wdhUPLOAD);
            $wdhDB_json       = json_encode($wdhDB);
            $wdhERROR_json    = json_encode($wdhERROR);
            $wdhSettings_json = json_encode($wdhSettings);
            $wdhINPUT_json    = json_encode($wdhINPUT);
            $jWDH             = '$jWDH';
            
            $success_display = 'display:none';
            $form_display    = 'display:block';
            
            $formHTML = array();
            
            array_push($formHTML, ' </div>');
            array_push($formHTML, ' <!-- wdh-edfp-form-content-id-' . $wdhSettings['form_id'] . ' -->');
            array_push($formHTML, ' <div class="wdh-success ' . $wdhSettings['form_msg_class'] . '" style="' . $success_display . ' ' . $this->generateCSS($wdhSettings['form_msg_css']) . '" id="wdh-edfp-form-success-id-' . $wdhSettings["form_id"] . '">');
            array_push($formHTML, $wdhSettings['form_msg_sent']);
            array_push($formHTML, ' </div>');
            array_push($formHTML, '</form>');
            array_push($formHTML, '<!-- wdh-edfp-form-id-' . $wdhSettings['form_id'] . ' -->');
            
            if ($wdhSettings['form_type'] == 'popup') {
                array_push($formHTML, '</div>');
            }
            $admin_url = admin_url('admin-ajax.php');
            array_push($formHTML, '<script type="text/javascript">');
            array_push($formHTML, "   window.formWidth = '" . $wdhSettings['form_width'] . "';");
            array_push($formHTML, "   $jWDH('#wdh-edfp-form-id-" . $wdhSettings['form_id'] . "').wdhGenerateForm('" . $wdhUPLOAD_json . "','" . $wdhDB_json . "','" . $wdhERROR_json . "','" . $wdhSettings_json . "','" . $wdhINPUT_json . "');");
            array_push($formHTML, "   var request_url = '" . $admin_url . "';");
            array_push($formHTML, '</script>');
            
            return implode('', $formHTML);
        }
        
        function field($wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $wdhSettings){
            $filter_class = $this->generateFilters($wdhFILTER);
            $star         = '';
            
            if ($wdhFILTER['is_required'] == true || $wdhINPUT['type'] == 'post_title' || $wdhINPUT['type'] == 'post_content' || $wdhINPUT['type'] == 'comment' || $wdhINPUT['type'] == 'username' || $wdhINPUT['type'] == 'password') {
                $star = '<span class="star">*</span>';
            }
            
            if (!isset($wdhFIELD['name']) || $wdhFIELD['name'] == '') {
                $wdhFIELD['name'] = 'wdhname';
            }
            
            if (!isset($wdhFIELD['table']) || $wdhFIELD['table'] == '') {
                $wdhFIELD['table'] = 'wdhtable';
            }
            
            if (($wdhSettings['form_mode'] == 'register' || $wdhSettings['form_mode'] == 'normal') && $wdhFILTER['is_email'] == true){
                $filter_class .= ' wdh-filter-is-unique';
            }
            
            $label_position = $wdhFIELD['label_position'];
            $field_class = $wdhFIELD['field_class'];
            $field_css = $wdhFIELD['field_css'];
            
            switch ($wdhINPUT['type']) {
                case "title":
                    return $this->titleField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "paragraph":
                    return $this->paragraphField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "text":
                    return $this->textField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "username":
                    return $this->usernameField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "textarea":
                    return $this->textareaField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "select":
                    return $this->selectField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhINPUT['values'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "radio":
                    return $this->radioField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhINPUT['values'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "checkbox":
                    return $this->checkboxField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhINPUT['values'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "password":
                    return $this->passwordField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['confirm'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "hidden":
                    return $this->hiddenField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['value'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position);
                    break;
                case "user_type":
                    return $this->userTypeField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['value'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position);
                    break;
                case "captcha":
                    return $this->captchaField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
                case "submit":
                    return $this->submitField($wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $label_position, $wdhTOOLTIP['text']);
                    break;
                default:
                    return $this->textField($star, $wdhFIELD['label'], $wdhFIELD['id'], $wdhFIELD['name'], $wdhSettings['form_id'], $wdhFIELD['table'], $field_class, $field_css, $wdhFIELD['class'], $wdhFIELD['css'], $wdhINPUT['class'], $wdhINPUT['css'], $filter_class, $label_position, $wdhTOOLTIP['text']);
                    break;
            }
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
        
        function generateFilters($wdhFILTER){
            $filterHTML = array();
            
            if ($wdhFILTER['is_required'] == true) {
                array_push($filterHTML, 'wdh-filter-is-required');
            }
            
            if ($wdhFILTER['is_email'] == true) {
                array_push($filterHTML, 'wdh-filter-is-email');
            }
            
            if ($wdhFILTER['is_url'] == true) {
                array_push($filterHTML, 'wdh-filter-is-url');
            }
            
            if ($wdhFILTER['is_phone'] == true) {
                array_push($filterHTML, 'wdh-filter-is-phone');
            }
            
            if ($wdhFILTER['is_alpha'] == true) {
                array_push($filterHTML, 'wdh-filter-is-alpha');
            }
            
            if ($wdhFILTER['is_numeric'] == true) {
                array_push($filterHTML, 'wdh-filter-is-numeric');
            }
            
            if ($wdhFILTER['is_alphanumeric'] == true) {
                array_push($filterHTML, 'wdh-filter-is-alphanumeric');
            }
            
            if ($wdhFILTER['is_unique'] == true) {
                array_push($filterHTML, 'wdh-filter-is-unique');
            }
            
            return implode(' ', $filterHTML);
        }
        
        function titleField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" style="' . $this->generateCSS($field_css) . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" >');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <h2 class="wdh-title-paragraph' . $label_class . '" style="' . $this->generateCSS($label_css) . '">' . $label . '</h2>');
                array_push($contentHTML, '         <div class="' . $input_class . '" style="' . $this->generateCSS($input_css) . '">&nbsp</div>');
            } else {
                array_push($contentHTML, '         <div class="' . $input_class . '" style="' . $this->generateCSS($input_css) . '">&nbsp</div>');
                array_push($contentHTML, '         <h2 class="wdh-title-paragraph' . $label_class . '" style="' . $this->generateCSS($label_css) . '">' . $label . '</h2>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function paragraphField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <p class="wdh-title-paragraph' . $label_class . '" style="' . $this->generateCSS($label_css) . '">' . $label . '</p>');
                array_push($contentHTML, '         <div class="' . $input_class . '" style="' . $this->generateCSS($input_css) . '">&nbsp</div>');
            } else {
                array_push($contentHTML, '         <div class="' . $input_class . '" style="' . $this->generateCSS($input_css) . '">&nbsp</div>');
                array_push($contentHTML, '         <p class="wdh-title-paragraph' . $label_class . '" style="' . $this->generateCSS($label_css) . '">' . $label . '</p>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function textField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
            } else {
                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function usernameField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . ' wdh-filter-is-username wdh-filter-is-unique wdh-filter-is-required" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
            } else {
                array_push($contentHTML, '         <input class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . ' wdh-filter-is-username wdh-filter-is-unique wdh-filter-is-required" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function textareaField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '        <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '        <textarea class="wdh-textarea wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" name="' . $name . '"></textarea>');
            } else {
                
                array_push($contentHTML, '        <textarea class="wdh-textarea wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" name="' . $name . '"></textarea>');
                array_push($contentHTML, '        <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '        <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function selectField($star, $label, $fieldID, $name, $options, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            $selecLabel  = '';
            $selecValue  = '';
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '         <select name="' . $name . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-select wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');

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
            } else {
                array_push($contentHTML, '         <select name="' . $name . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-select wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');

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
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function radioField($star, $label, $fieldID, $name, $options, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            $selecLabel  = '';
            $selecValue  = '';
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');

                $optionsall = explode("|", $options);

                $i = 0;

                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');
                array_push($contentHTML, '         <input class="wdh-radio wdh-get-value wdh-form-field-value-' . $formID . ' ' . $filters_class . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');

                foreach ($optionsall as $option) {

                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-' . $formID . '">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-radio-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-radio wdh-get-value-radio-option" type="radio" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');
                    array_push($contentHTML, '             </div>');
                    $i++;
                }

                array_push($contentHTML, '         </div>');
            } else {
                
                $optionsall = explode("|", $options);

                $i = 0;

                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');
                array_push($contentHTML, '         <input class="wdh-radio wdh-get-value wdh-form-field-value-' . $formID . ' ' . $filters_class . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');

                foreach ($optionsall as $option) {

                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-' . $formID . '">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-radio-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-radio wdh-get-value-radio-option" type="radio" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');
                    array_push($contentHTML, '             </div>');
                    $i++;
                }

                array_push($contentHTML, '         </div>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function checkboxField($star, $label, $fieldID, $name, $options, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            $selecLabel  = '';
            $selecValue  = '';
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '         <input class="wdh-checkbox wdh-get-value wdh-form-field-value-' . $formID . ' ' . $filters_class . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');

                $optionsall = explode("|", $options);
                $i          = 0;
                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');

                foreach ($optionsall as $option) {



                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-' . $formID . '">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-checkbox-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-checkbox wdh-get-value-checkbox-option" type="checkbox" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');

                    array_push($contentHTML, '             </div>');

                    $i++;

                }

                array_push($contentHTML, '         </div>');
            } else {
                array_push($contentHTML, '         <input class="wdh-checkbox wdh-get-value wdh-form-field-value-' . $formID . ' ' . $filters_class . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');

                $optionsall = explode("|", $options);
                $i          = 0;
                array_push($contentHTML, '         <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-options wdh-field-form-id-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '">');

                foreach ($optionsall as $option) {



                    if (strpos($option, '@@') !== false) {
                        $optionDATA = explode("@@", $option);
                        $selecLabel = $optionDATA[0];
                        $selecValue = $optionDATA[1];
                    } else {
                        $selecLabel = $option;
                        $selecValue = $option;
                    }

                    array_push($contentHTML, '             <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field-option wdh-field-form-id-' . $formID . '">');
                    array_push($contentHTML, '                 <input id="wdh-form-field-value-id-checkbox-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" class="wdh-checkbox wdh-get-value-checkbox-option" type="checkbox" name="' . $name . '" value="' . $selecValue . '">');
                    array_push($contentHTML, '                 <span>' . $selecLabel . '</span>');

                    array_push($contentHTML, '             </div>');

                    $i++;

                }

                array_push($contentHTML, '         </div>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function passwordField($star, $label, $fieldID, $name, $formID, $label1, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            global $wdhSettings;
            
            $contentHTML = array();
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                array_push($contentHTML, '         <input class="wdh-input-password wdh-filter-is-password wdh-get-value wdh-passoword wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="password" name="' . $name . '"/>');
            
                if ($wdhSettings['form_mode'] != 'login') {
                    array_push($contentHTML, '         <div class="wdh-separator">&nbsp;</div>');
                    array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '">' . $label1 . ':</label>');
                    array_push($contentHTML, '         <input class="wdh-input-password wdh-get-value wdh-confirm-password wdh-form-field-value-' . $formID . ' ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-second-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="password" name="' . $name . '"/>');
                }
            } else {
                array_push($contentHTML, '         <input class="wdh-input-password wdh-filter-is-password wdh-get-value wdh-passoword wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="password" name="' . $name . '"/>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
                
                if ($wdhSettings['form_mode'] != 'login') {
                    array_push($contentHTML, '         <div class="wdh-separator">&nbsp;</div>');
                    array_push($contentHTML, '         <input class="wdh-input-password wdh-get-value wdh-confirm-password wdh-form-field-value-' . $formID . ' ' . $input_class . '" style="' . $this->generateCSS($input_css) . '" id="wdh-form-field-value-id-second-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="password" name="' . $name . '"/>');
                    array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '">' . $label1 . ':</label>');
                }
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
            
        }
        
        function userTypeField($star, $label, $fieldID, $value, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" style="display:none;">');
            array_push($contentHTML, '         <label style="display:none"  id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            array_push($contentHTML, '         <input value="' . $value . '" class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' wdh-filter-is-user-type" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
            
        }
        
        function hiddenField($star, $label, $fieldID, $value, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" style="display:none;">');
            array_push($contentHTML, '         <label style="display:none"  id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">' . $label . $star . ':</label>');
            array_push($contentHTML, '         <input value="' . $value . '" class="wdh-input wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . '" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="hidden" name="' . $name . '"/>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
            
        }
        
        function captchaField($star, $label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $filters_class, $label_position, $info = null){
            $contentHTML = array();
            $term_one    = rand(0, 9);
            $second_term = rand(0, 9);
            $total       = $term_one + $second_term;
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . ' text-align:right;" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '"><b>' . $term_one . '+' . $second_term . ' = </b>&nbsp;</label>');
                array_push($contentHTML, '         <input class="' .$input_class . ' ' . $filters_class . ' wdh-input wdh-filter-is-captcha wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" maxlength="3" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
                array_push($contentHTML, '         <input class="wdh-filter-is-recaptcha" id="wdh-form-field-recaptcha-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" value="' . $total . '" type="hidden" name="recaptca-' . $name . '"/>');
            } else {
                array_push($contentHTML, '         <input class="' .$input_class . ' ' . $filters_class . ' wdh-input wdh-filter-is-captcha wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . ' ' . $filters_class . '" style="' . $this->generateCSS($input_css) . '" maxlength="3" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" type="text" name="' . $name . '"/>');
                array_push($contentHTML, '         <input class="wdh-filter-is-recaptcha" id="wdh-form-field-recaptcha-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" value="' . $total . '" type="hidden" name="recaptca-' . $name . '"/>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . ' text-align:right;" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '"><b>' . $term_one . '+' . $second_term . ' = </b>&nbsp;</label>');
            }
            
            if ($info) {
                array_push($contentHTML, '     <div class="wdh-tooltip">');
                array_push($contentHTML, '          <span class="wdh-information">' . $info . '</span>');
                array_push($contentHTML, '     </div>');
            }
            
            array_push($contentHTML, '         <div class="error-box" id="wdh-form-field-error-' . $fieldID . '"> </div>');
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function submitField($label, $fieldID, $name, $formID, $table, $field_class, $field_css, $label_class, $label_css, $input_class, $input_css, $label_position, $info = null){
            $contentHTML = array();
            
            array_push($contentHTML, '     <div id="wdh-form-field-id-' . $fieldID . '" class="wdh-field wdh-field-form-id-' . $formID . $field_class . '" style="' . $this->generateCSS($field_css) . '">');
            //echo $label_class; die();
            if ($label_position < 1) {
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">&nbsp;</label>');
                array_push($contentHTML, '         <input class="wdh-submit-btn wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . '" style="' . $this->generateCSS($input_css) . 'cursor:pointer;" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '" type="submit" " name="' . $name . '" value="' . $label . '"/>');
            } else {
                array_push($contentHTML, '         <input class="wdh-submit-btn wdh-get-value wdh-form-field-value-' . $formID . ' ' . $input_class . '" style="' . $this->generateCSS($input_css) . 'cursor:pointer;" id="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '" type="submit" " name="' . $name . '" value="' . $label . '"/>');
                array_push($contentHTML, '         <label class="' . $label_class . '" style="' . $this->generateCSS($label_css) . '" for="wdh-form-field-value-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '" id="wdh-form-field-label-id-' . $formID . '-' . $fieldID . '-' . $table . '-' . $name . '">&nbsp;</label>');
            }
            array_push($contentHTML, '     </div>');
            
            return implode('', $contentHTML);
        }
        
        function wdhOrigin($s, $use_forwarded_host = false){
            $ssl      = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
            $sp       = strtolower($s['SERVER_PROTOCOL']);
            $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
            $port     = $s['SERVER_PORT'];
            $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
            $host     = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
            $host     = isset($host) ? $host : $s['SERVER_NAME'] . $port;
            
            return $protocol . '://' . $host;
        }
        
        function wdhFullURL($s, $use_forwarded_host = false){
            return $this->wdhOrigin($s, $use_forwarded_host) . $s['REQUEST_URI'];
        }
        
        // Encypt
        function wdhEncrypt($decrypted, $password){
            $encrypted = base64_encode($password . $decrypted);
            
            return $encrypted;
        }
        
        function wdhDecrypt($encrypted, $password){
            $decrypted = base64_decode($encrypted);
            $decrypted = str_replace($password, '', $decrypted);
            
            return $decrypted;
        }
    }
}



?>