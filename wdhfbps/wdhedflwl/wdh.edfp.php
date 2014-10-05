<?php
//Project Name: WDH - Edit Database Field LIGHT (Wordpress Library)
//Project Version: 1.0
//Project Description: Edit Database Field LIGHT WL is a wordpress library which help you to display the field you want from database and you can edit it with Ajax ( without reloading page ).The Field can be edit in different types :  text , textarea, select , slider, switch on./off button, colorpicker.Also you can add to the editable field filters ( is email , is url and others ).
//Project URL: http://www.wdh.im/projects/edit-database-field-light-wordpress-library/
//Author: WDH - Web Developers House
//Author URL: http://www.wdh.im/
//File: wdh.edfp.php
//File Description: Main PHP Class
//File Version: 1.0
//Last Update File : 28.09.2014
//
//Change log:
//
//        1.0 (2014-09-28)
//	
//		* Initial release.


if (!class_exists("WdhEditFieldDb")){
    class WdhEditFieldDb{
        private $phpMailer;
        
        function WdhEditFieldDb($session_start = true){
            include_once 'edfp-config.php';
            global $wdhDB, $wdhFIELD, $wdhINPUT, $wdhTOOLTIP, $wdhFILTER, $wdhERROR, $wdhUPLOAD, $edfp, $wdhForm, $wdhSettings;

            if ($session_start != false) {
                $this->wdhInit();
            }
            
            if (!class_exists("PHPMailer")) {
                include_once ABSPATH.WPINC.'/class-phpmailer.php';
            }
            
            $this->phpMailer = new PHPMailer();

            if (!defined('EDFP_PATH')) {
                 define('EDFP_PATH', dirname(__FILE__) . '/');
            }

            //----------------------------------
            //----- Plugins --------------------
            //----------------------------------
            
            //----------------------------------
            // Form Generator
            //----------------------------------
            
            if ($wdhFIELD['WDH_FORM_GENERATOR'] == true || $wdhFIELD['WDH_FORM_GENERATOR'] > 0) {
                include_once EDFP_PATH.'extensions/formgenerator/wdh.formgenerator.php';
            }
        }
        
        // Initialiaze Library
        function wdhInit(){
            
            // Add Admin CSS
            add_action('admin_enqueue_scripts', array(&$this, 'addCSS'));
            // Add Frontend CSS
            add_action('wp_enqueue_scripts', array(&$this, 'addCSS'));
            // Add Admin JS
            add_action('admin_enqueue_scripts', array(&$this, 'addJS'));
            // Add Frontend JS
            add_action('wp_enqueue_scripts', array(&$this, 'addJS'));
            // Save Field
            add_action('wp_ajax_nopriv_wdh_edit_field_db', array(&$this, 'wdhSaveDbFieldData'));
            add_action('wp_ajax_wdh_edit_field_db', array(&$this, 'wdhSaveDbFieldData'));
            // Insert Fields
            add_action('wp_ajax_nopriv_wdh_insert_fields_db', array(&$this, 'wdhInsertFieldsData'));
            add_action('wp_ajax_wdh_insert_fields_db', array(&$this, 'wdhInsertFieldsData'));
            // Check if exist
            add_action('wp_ajax_nopriv_wdh_check_if_exist', array(&$this, 'checkIfExist'));
            add_action('wp_ajax_wdh_check_if_exist', array(&$this, 'checkIfExist'));
            
        }
        
        function addCSS(){
            global $wdhFIELD;
            // Register Styles.
            include('edfp-config.php');
            
            wp_register_style('WDH_jQuery_UI_CSS', plugins_url('css/jquery-ui.css', __FILE__));
            wp_register_style('WDH_ColorPicker_CSS', plugins_url('css/colorpicker.css', __FILE__));
            wp_register_style('WDH_Tooltip_CSS', plugins_url('css/wdh.im.tooltip.css', __FILE__));
            wp_register_style('WDH_EditDatabaseFieldLIGHT_CSS', plugins_url('templates/'.$wdhFIELD['template'].'/'.$wdhFIELD['template'].'.css', __FILE__));
            
            
            //----- PLUGINS ------
            if ($wdhFIELD['WDH_FORM_GENERATOR'] == true || $wdhFIELD['WDH_FORM_GENERATOR'] > 0) {
                wp_register_style('WDH_FormGenerator_CSS',plugins_url('extensions/formgenerator/css/wdh.formgenerator.css', __FILE__));
            }
            
            // Enqueue Styles.
            wp_enqueue_style('WDH_jQuery_UI_CSS');
            wp_enqueue_style('WDH_ColorPicker_CSS');
            wp_enqueue_style('WDH_Tooltip_CSS');
            
            wp_enqueue_style('WDH_EditDatabaseFieldLIGHT_CSS');
            
            //----- PLUGINS ------
            if ($wdhFIELD['WDH_FORM_GENERATOR'] == true || $wdhFIELD['WDH_FORM_GENERATOR'] > 0) {
                wp_enqueue_style('WDH_FormGenerator_CSS');
            }
        }
        
        function addJS(){
            global $wdhFIELD;
            // Register JavaScript.
            include('edfp-config.php');
            
            wp_register_script('WDH_ColorPicker_JS', plugins_url('js/colorpicker.js', __FILE__), array('jquery'));
            wp_register_script('WDH_JSON2_JS', plugins_url('js/json2.js', __FILE__), array('jquery'));
            wp_register_script('WDH_Slider_JS', plugins_url('js/jquery-ui-slider.js', __FILE__), array('jquery'));
            wp_register_script('WDH_EditDatabaseFieldLIGHT_JS', plugins_url('js/jquery.wdh.im.edfp.js', __FILE__), array('jquery'));
            
            //----- PLUGINS ------
            if ($wdhFIELD['WDH_FORM_GENERATOR'] == true || $wdhFIELD['WDH_FORM_GENERATOR'] > 0) {
                wp_register_script('WDH_FormGenerator_JS',plugins_url('extensions/formgenerator/js/jquery.wdh.im.formgenerator.js', __FILE__), array('jquery'));
            }
            
            // Enqueue JavaScript.
            if (!wp_script_is('jquery', 'queue')){
                wp_enqueue_script('jquery');
            }
            
            if (!wp_script_is('jquery-ui-core', 'jquery')){
                wp_enqueue_script('jquery-ui-core');
            }
            
            wp_enqueue_script('WDH_Slider_JS');
            
            if (!wp_script_is('jquery-ui-tooltip', 'queue')){
                wp_enqueue_script('jquery-ui-tooltip');
            }
            
            wp_enqueue_script('WDH_ColorPicker_JS');
            wp_enqueue_script('WDH_JSON2_JS');
            
            wp_enqueue_script('WDH_EditDatabaseFieldLIGHT_JS');
            
            //----- PLUGINS ------
            if ($wdhFIELD['WDH_FORM_GENERATOR'] == true || $wdhFIELD['WDH_FORM_GENERATOR'] > 0) {
                wp_enqueue_script('WDH_FormGenerator_JS');
            }
        }

        function wdhfield($wdhDB,$wdhFIELD, $wdhJSONCheck = 'yes'){
            global $wpdb;
            $table = $wdhDB['table'];
            $fieldname = $wdhFIELD['field_name'];
            $conditions = $wdhFIELD['conditions'];
            $defaultValue = $wdhFIELD['value'];
            $i = 0;
            $conditionAll = '';
            
            if ($defaultValue == "" || !isset($defaultValue) || $wdhFIELD['json_value'] != '') {
                
                if (isset($conditions)){

                    foreach($conditions as $condition){

                        if ($i < 1){
                            $conditionAll .= $condition['field_label'].'="'.$condition['field_value'].'"';
                        } else {
                            $conditionAll .= ' '.$condition['field_condition'].' '.$condition['field_label'].'="'.$condition['field_value'].'"';
                        }
                        $i++;
                    }
                }

                $query = 'SELECT * FROM '.$table.' WHERE '.$conditionAll.' LIMIT 1';
                $result = $wpdb->get_row($query) or die(mysql_error());
                $fieldret = '';
                // Print out result
                if (strpos($fieldname,'-----') !== false) {
                    $fieldname = explode('-----',$fieldname);
                    $field = $result->$fieldname[0];
                } else {
                    $field = $result->$fieldname;
                }
                

                // JSON Field Value
                if ($wdhJSONCheck == 'yes'){
                    if ($wdhFIELD['json_value'] != ''){
                        $fieldJSON = $wdhFIELD['json_value'];
                        $field = json_decode($field);
                        
                        if (isset($field->$fieldJSON)){
                            $field = $field->$fieldJSON;
                        }
                    }
                }
            
            } else {
                $field = $defaultValue;
            }
            
            if ($field == ""){
                $field = "...............";
            }
            
            return $field;
        }
        
        function wdhExistField($wdhDB, $wdhFIELD){
            global $wpdb;
            $table        = $wdhDB['table'];
            $fieldname    = $wdhFIELD['field_name'];
            $conditions   = $wdhFIELD['conditions'];
            $i            = 0;
            $conditionAll = '';
            
            foreach ($conditions as $condition) {
                
                if ($i < 1) {
                    $conditionAll .= $condition['field_label'] . '="' . $condition['field_value'] . '"';
                } else {
                    $conditionAll .= ' ' . $condition['field_condition'] . ' ' . $condition['field_label'] . '="' . $condition['field_value'] . '"';
                }
                $i++;
            }
            
            $results = $wpdb->get_results( 
                                "
                                SELECT * 
                                FROM $table
                                WHERE $conditionAll LIMIT 1
                                "
                        );
            $no = 0;
            // Count Results
            if (isset($results)){
                foreach  ($results as $result) {
                    $no++;
                }
            }
            return $no;
        }
        
        function wdhExistFieldValue($wdhDB, $wdhFIELD, $fieldValue){
            global $wpdb;
            $table        = $wdhDB['table'];
            $fieldname    = $wdhFIELD['field_name'];
            $conditions   = $wdhFIELD['conditions'];
            $results = $wpdb->get_results( 
                                "
                                SELECT * 
                                FROM $table
                                WHERE $fieldname = '$fieldValue'
                                "
                        );
            $no = 0;
            // Count Results
            if ($wpdb->num_rows > 0){
                $no=$wpdb->num_rows;
            }
            
            return $no;
        }
        
        function wdhAddField($wdhDB, $wdhFIELD, $wdhJSONCheck = 'yes'){
            global $wpdb, $wdhINPUT, $wdhCPF_CONFIG;
            
            $table        = $wdhDB['table'];
            $fieldname    = $wdhFIELD['field_name'];
            $conditions   = $wdhFIELD['conditions'];
            $fields_label = '';
            $fields_value = '';
            $value        = '';
            $tableData    = array();
            
            foreach ($conditions as $condition) {
                $field_label = $condition['field_label'];
                $field_value = $condition['field_value'];
                
                $tableData[$field_label] = $field_value;
            }
            
            // JSON Field Value
            if ($wdhJSONCheck == 'yes'){
                if ($wdhFIELD['json_value'] != ''){
                    $field_label = $wdhFIELD['field_name'];
                    $value = '';
                    
                    $field_value = $this->generateFieldJSON($value);
                    $tableData[$field_label] = $field_value;
                }
            }
            
            if ($wdhFIELD['auto_add'] == true) {
                $wpdb->insert($table,$tableData);
            }
        }
        
        function generateFieldJSON($value){
            $languages = array();
            $field = array();
            $languages = array('af','sq','ar','az','eu','be','bg','ca','zh','hr','cs','da','nl','en','eo','et','fl','fl','fi','fr',
                              'gl','de','el','ht','he','hi','hu','is','id','ga','it','ja','ko','lv','lt','mk','ms','mt','no','fa',
                              'pl','pt','ro','ru','sr','sk','sl','es','sw','sv','th','tr','uk','ur','vi','cy','yi');

            foreach ($languages as $language){
                $field[$language] = $value;
            }

            return json_encode($field);
        }
          
        function wdhShowField($wdhDB,$wdhFIELD,$wdhINPUT,$wdhTOOLTIP,$wdhFILTER,$wdhERROR,$wdhUPLOAD){
            $table             = $wdhDB['table'];
            $fieldname         = $wdhFIELD['field_name'];
            $conditions        = $wdhFIELD['conditions'];
            $autoAddField      = $wdhFIELD['auto_add'];
            $wfieldvalue       = '';
            $title             = $wdhTOOLTIP['text'];
            $inputType         = $wdhINPUT['type'];
            $tooltipType       = $wdhTOOLTIP['position'];
            $submitText        = $wdhINPUT['save_button'];
            $valuesList        = $wdhINPUT['values'];
            $wdhDB_json        = json_encode($wdhDB);
            $wdhFIELD_json     = json_encode($wdhFIELD);
            $wdhINPUT_json     = json_encode($wdhINPUT);
            $wdhTOOLTIP_json   = json_encode($wdhTOOLTIP);
            $wdhFILTER_json    = json_encode($wdhFILTER);
            $wdhERROR_json     = json_encode($wdhERROR);
            $wdhUPLOAD_json    = json_encode($wdhUPLOAD);
            $tooltip           = '';
            
            $i = 0;

            foreach ($conditions as $condition) {
                
                if ($i < 1) {
                    $wfieldvalue .= $condition['field_label'] . '-' . $condition['field_value'];
                } else {
                    $wfieldvalue .= '-' . $condition['field_label'] . '-' . $condition['field_value'];
                }
                $i++;
            }
            
            $fieldFound = $this->wdhExistField($wdhDB, $wdhFIELD);

            if ($autoAddField == true){
                
                // Add field
                if ($fieldFound < 1){
                    $this->wdhAddField($wdhDB, $wdhFIELD);
                    $fieldFound = 1;
                }
            }
            
            // Verify if field exist
            if ($fieldFound > 0) {
                
                $jWDH  = '$jWDH';
                $class = $wdhFIELD['class'];
                $wdhDisplay = array();
                // DISPLAY & EDIT
                if ($wdhFIELD['edit'] == true) {
                
                    // SWITCH
                    if ($wdhINPUT['type'] == 'switch') {
                        
                        $turnon  = $this->wdhfield($wdhDB, $wdhFIELD);
                        $checked = '';
                        
                        if ($turnon == 'true') {
                            $checked = 'checked';
                        }
                        
                        if (isset($title) && $title != '') {
                            $tooltip = "<span class='wdh-tooltip'><span class='wdh-information'>".$title."</span></span>";
                        }
                        
                        array_push($wdhDisplay, '<span id="wdh-field-' . $fieldname . '-' . $wfieldvalue . '" class="onoffswitch ' . $class . '" title="'.$title.'">
                                                    <input id="wdh-field-switch-' . $fieldname . '-' . $wfieldvalue . '" type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" ' . $checked . '>
                                                    <label class="onoffswitch-label" for="wdh-field-switch-' . $fieldname . '-' . $wfieldvalue . '">
                                                        <div class="onoffswitch-inner"></div>
                                                        <div class="onoffswitch-switch"></div>
                                                    </label>
                                                </span>');
                        array_push($wdhDisplay, '<div class="wdh-field">'.$tooltip.'</div>');
                        $admin_url = admin_url('admin-ajax.php');
                        array_push($wdhDisplay, "<script type='text/javascript'>
                                                    window.ajaxurl = '" . $admin_url . "';
                                                    $jWDH('#wdh-field-switch-" . $fieldname . "-" . $wfieldvalue . "').wdhEditDbFieldSwitch('" . $wdhDB_json . "','" . $wdhFIELD_json . "','" . $wdhINPUT_json . "','" . $wdhTOOLTIP_json . "','" . $wdhFILTER_json . "','" . $wdhERROR_json . "','" . $wdhUPLOAD_json . "','" . $this->wdhfield($wdhDB, $wdhFIELD) . "','" . $wfieldvalue . "');
                                                 </script>");
                        
                        // COLORPICKER
                    } else if ($wdhINPUT['type'] == 'colorpicker') {
                        $addClass = 'wdh-colorpicker-preview';
                        $addStyle = 'background:#';
                        
                        if (isset($title) && $title != '') {
                            $tooltip = "<span class='wdh-tooltip'><span class='wdh-information'>".$title."</span></span>";
                        }
                        
                        $admin_url = admin_url('admin-ajax.php');
                        array_push($wdhDisplay, "<span id='wdh-field-" . $fieldname . "-" . $wfieldvalue . "' class='wdh-field " . $class . " " . $addClass . "' style='" . $addStyle . $this->wdhfield($wdhDB, $wdhFIELD) . ";' title='" . $title . "'>&nbsp;</span>");
                        array_push($wdhDisplay, '<div class="wdh-field" style="float:left;">'.$tooltip.'</div>');
                        array_push($wdhDisplay, "<script type='text/javascript'>
                                                    window.ajaxurl = '" . $admin_url . "';
                                                    $jWDH('#wdh-field-" . $fieldname . "-" . $wfieldvalue . "').wdhEditDbFieldColorPicker('" . $wdhDB_json . "','" . $wdhFIELD_json . "','" . $wdhINPUT_json . "','" . $wdhTOOLTIP_json . "','" . $wdhFILTER_json . "','" . $wdhERROR_json . "','" . $wdhUPLOAD_json . "','" . $this->wdhfield($wdhDB, $wdhFIELD) . "','" . $wfieldvalue . "');
                                                 </script>");
                           
                    } else {
                        
                        $value = $this->wdhfield($wdhDB, $wdhFIELD);
                        
                        if (isset($title) && $title != '') {
                            $tooltip = "<span class='wdh-tooltip'><span class='wdh-information'>".$title."</span></span>";
                        }
                        
                        $admin_url = admin_url('admin-ajax.php');
                        array_push($wdhDisplay, "<span id='wdh-field-" . $fieldname . "-" . $wfieldvalue . "' class='wdh-field " . $class . "' title='" . $title . "'><span style='float:left;'>" . $value . "</span> ".$tooltip." </span>");
                        array_push($wdhDisplay, "<script type='text/javascript'>
                                                    window.ajaxurl = '" . $admin_url . "';
                                                    $jWDH('#wdh-field-" . $fieldname . "-" . $wfieldvalue . "').wdhEditDbField('" . $wdhDB_json . "','" . $wdhFIELD_json . "','" . $wdhINPUT_json . "','" . $wdhTOOLTIP_json . "','" . $wdhFILTER_json . "','" . $wdhERROR_json . "','" . $wdhUPLOAD_json . "','" . $this->wdhfield($wdhDB, $wdhFIELD) . "','" . $wfieldvalue . "');
                                                </script>");
                        
                    }
                    // DISPLAY ONLY
                } else {
                    
                    // SWITCH
                    if ($wdhINPUT['type'] == 'switch') {
                        
                        $turnon  = $this->wdhfield($wdhDB, $wdhFIELD);
                        $checked = '';
                        
                        if ($turnon == 'true') {
                            $checked = 'checked';
                        }
                        
                        array_push($wdhDisplay, '<div id="wdh-field-' . $fieldname . '-' . $wfieldvalue . '" class="onoffswitch ' . $class . '">
                                                    <input id="wdh-field-switch-' . $fieldname . '-' . $wfieldvalue . '" type="checkbox" disabled="disabled" name="onoffswitch" class="onoffswitch-checkbox" ' . $checked . '>
                                                    <label class="onoffswitch-label" style="cursor:inherit;" for="wdh-field-switch-' . $fieldname . '-' . $wfieldvalue . '">
                                                        <div class="onoffswitch-inner"></div>
                                                        <div class="onoffswitch-switch"></div>
                                                    </label>
                                                </div>');
                        
                        // COLORPICKER
                    } else if ($wdhINPUT['type'] == 'colorpicker') {
                        $addClass = 'wdh-colorpicker-preview';
                        $addStyle = 'background:#';
                        array_push($wdhDisplay, "<span id='wdh-field-" . $fieldname . "-" . $wfieldvalue . "' class='wdh-field " . $class . " " . $addClass . "' style='" . $addStyle . $this->wdhfield($wdhDB, $wdhFIELD) . ";cursor:inherit;'>&nbsp;</span>");
                        
                        // ALL    
                    } else {
                        $value = $this->wdhfield($wdhDB, $wdhFIELD);
                        array_push($wdhDisplay, "<span id='wdh-field-" . $fieldname . "-" . $wfieldvalue . "' class='wdh-field " . $class . "' style='cursor:inherit;'>" . $value . "</span>");
                    }
                    
                }
            } else {
                //array_push($wdhDisplay, "<span id='wdh-field-" . $fieldname . "-" . $wfieldvalue . "' class='wdh-error " . $class . "' style='cursor:inherit;'>" . $wdhFIELD['not_exist'] . "</span>");
            }
            
            if (isset($wdhDisplay)) {
                return implode('', $wdhDisplay);
            }
        }
        
        function has_json_data($string) {
            $array = json_decode($string, true);
            return !empty($string) && is_string($string) && is_array($array) && !empty($array) && json_last_error() == 0;
        }
        
        function wdhSaveDbFieldData(){
            global $wpdb,$wdhDB,$wdhFIELD;
            // Saving Data
            $wdhDB_mod = json_decode(stripslashes($_POST['wdhDB_json']));
            $wdhFIELD_mod = json_decode(stripslashes($_POST['wdhFIELD_json']));
            
            $value     = $_POST['value'];
            $value     = str_replace('...............#','',$value);
            $value     = str_replace('#...............','',$value);
            $value     = str_replace('...............','',$value);
            $type      = $_POST['type'];
            $is_unique = $_POST['is_unique'];
            
            // Mysql connection data define
            $wdhDB['table'] = $wdhDB_mod->table;

            // Field define
            $wdhFIELD['field_name'] = $wdhFIELD_mod->field_name;
            $wdhFIELD['conditions'] = array();
            $conditionIs = array();
            $wdhFIELD['json_value'] = $wdhFIELD_mod->json_value;
            
            foreach ($wdhFIELD_mod->conditions as $condition){
                $conditionIs['field_label'] = $condition->field_label;
                $conditionIs['field_value'] = $condition->field_value;
                $conditionIs['field_condition'] = $condition->field_condition;
                array_push($wdhFIELD['conditions'],$conditionIs);
            }
            
            $dbTable=$wdhDB['table'];
            $dbfieldName=$wdhFIELD['field_name'];
            $conditions = $wdhFIELD['conditions'];
            $i            = 0;
            $conditionAll = '';

            if (isset($conditions)){
                
                foreach ($conditions as $condition) {

                    if ($i < 1) {
                        $conditionAll .= $condition['field_label'] . '="' . $condition['field_value'] . '"';
                    } else {
                        $conditionAll .= ' ' . $condition['field_condition'] . ' ' . $condition['field_label'] . '="' . $condition['field_value'] . '"';
                    }
                    $i++;
                }
            }
            
            // XSS LIGHTTECTION
            $value = $this->xss_cleaner($value);
            
            // SQL INJECT LIGHTTECTION
            if ($type != 'html_editor'){
                //$value = $wpdb->prepare($value);
            }

            // Check if is unique
            if ($is_unique == 'true') {
                // Exist Field
                $fieldValueExist = $this->wdhExistFieldValue($wdhDB, $wdhFIELD, $value);

                if($fieldValueExist > 0) {
                    echo 'field_exist'; die();
                }
                
                if ($type == 'username'){
                    
                    if (username_exists($username)) {
                        echo 'field_exist'; die();
                    }
                }
            }
            
            // MD5 Password
            if ($type == 'password'){
                $value = md5($value);
            }
            
            // JSON Field Value
            
            if ($wdhFIELD['json_value'] != ''){
                
                $valueOld = $this->wdhfield($wdhDB, $wdhFIELD, 'noJSON');
                $fieldJSON = $wdhFIELD['json_value'];
                $values = json_decode($valueOld);
                $valueNew = array();
                foreach($values as $key => $valueMod){
                    
                    if ($key == $fieldJSON){
                        $valueNew[$key] = $value; 
                    } else {
                        $valueNew[$key] = $valueMod;
                    }
                }
                
                $value = json_encode($valueNew);
            }
            
            $real_value = $value;
            
            if ($type != 'map' && $type != 'video' && $type != 'is_in_db') {
                // Update field
                if (strpos($dbfieldName,'-----') !== false) {
                    $dbfieldsName = explode('-----',$dbfieldName); 
                    
                    foreach ($dbfieldsName as $dbfieldName) {
                        $query = 'UPDATE ' . $dbTable . ' SET ' . $dbfieldName . '="' . $value . '" WHERE ' . $conditionAll;
                        $wpdb->query($query) or mysql_error();
                    }
                } else {
                    $query = 'UPDATE ' . $dbTable . ' SET ' . $dbfieldName . '="' . $value . '" WHERE ' . $conditionAll;
                    $wpdb->query($query) or mysql_error();
                }
                echo 'success';
            }
            
            die();
        }
        
        // XSS CLEANER
        function xss_cleaner($input_str){
            $return_str = str_replace(array(
                '<',
                '>',
                "'",
                '"',
                ')',
                '('
            ), array(
                '&lt;',
                '&gt;',
                '&apos;',
                '&#x22;',
                '&#x29;',
                '&#x28;'
            ), $input_str);
            $return_str = str_ireplace('%3Cscript', '', $return_str);
            return $return_str;
        }
        
        function wdhInsertFieldsData(){
            $wdhSettings_mod = json_decode(stripslashes($_POST['settings']));
            // Insert Fields
            $this->insertFields($_POST['fields'],$wdhSettings_mod);
            die();
        }
        
        function getFormData($formID){
            global $wpdb;
            
            if (!defined('WDHFBPS_Forms_table')) { // Users
                define('WDHFBPS_Forms_table', $wpdb->prefix.'wdhfbps_forms');
            }
            
            $form = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Forms_table.' where id="'.$formID.'"');
            
            return $form;
        }
        
        function getUserData($username){
            global $wpdb;
            
            if (!defined('WDHFBPS_Users_table')) { // Users
                define('WDHFBPS_Users_table', $wpdb->prefix.'wdhfbps_users');
            }
            
            $user = $wpdb->get_row('SELECT * FROM '.WDHFBPS_Users_table.' where username="'.$username.'"');
            
            return $user;
        }
        
        function getFieldLabelandValue($fields,$fieldID,$type){
            global $wpdb,$edfp,$wdhDB,$wdhSettings,$wdhUPLOAD;
            $wdhURL = $wdhSettings->WDH_WEBSITE_URL;
            $fieldsDB = array();
            $i = 0;
            $returnValue = '';
            
            foreach($fields as $key => $fieldsTable){
                $fieldsValues = array();
                $fieldsNames = array();
                $fieldsADD = array();
                
                $fieldsTable = (array)$fieldsTable;
                
                foreach($fieldsTable as $field){
                    $field = (array)$field;
                    $wdhCurrentFormID = $field['form_id'];
                
                    // XSS LIGHTTECTION
                    $field['field_value'] = $this->xss_cleaner($field['field_value']);
                    
                    if ($field['field_id'] == $fieldID) {
                        
                        if ($type == 'value') {
                            $returnValue = $field['field_value'];
                        } else {
                            $returnValue = $field['field_label'];
                        }
                    }
                }
            }
            
            return $returnValue;
        }
        
        // Insert fields in Multiple Tables
        function insertFields($fields,$settings,$formtype = 'free'){
            $fields = (array)$fields;
            global $wpdb,$edfp,$wdhDB,$wdhSettings,$wdhUPLOAD;
            global $wdhCurrentFormID;
            $wdhSettings = $settings;
            $wdhURL = $wdhSettings->WDH_WEBSITE_URL;
            $usertype = 'subscriber';
                
            if (!defined('WDHFBPS_Forms_records_table')) { // Forms Fields
                define('WDHFBPS_Forms_records_table', $wpdb->prefix.'wdhfbps_forms_records');
            }
                
            if (!defined('WDHFBPS_Users_table')) { // Users
                define('WDHFBPS_Users_table', $wpdb->prefix.'wdhfbps_users');
            }
            
            $fieldsDB = array();
            $emails = array();
            $newFields = array();
            $fieldsRequiredMessage = '';
            $fieldsAllMessage = '';
            $loginConditions = '';
            $i = 0;
            $cookieAll = '';
            $username = '';
            $comment = '';
            $post_title = '';
            $post_content = '';
            $email = '';
            $password = '';
            $postID = 0;
            $formID = 0;
            $customerId = 0;
            $m = 0;
            
            foreach($fields as $key => $fieldsTable){
                $fieldsValues = array();
                $fieldsNames = array();
                $fieldsADD = array();
                
                $fieldsTable = (array)$fieldsTable;
                
                foreach($fieldsTable as $field){
                    $field = (array)$field;
                    $wdhCurrentFormID = $field['form_id'];
                    
                    if ($field['field_type'] == 'email') {
                        array_push($emails, $field['field_value']);
                        $email = $field['field_value'];
                    }
                    
                    if ($field['field_type'] == 'username') {
                        $username = $field['field_value'];
                    }
                    
                    // Put Field in Message Fields
                    if ($field['field_type'] != "image" && $field['field_type'] != "file") {
                        $fieldsAllMessage .= '<b>'.$field['field_label'].'</b> '.$field['field_value'].'</br>';
                    } else if ($field['field_type'] == "image"){
                        $fieldsAllMessage .= '<b>'.$field['field_label'].'</b> <img src="'.$wdhURL.'uploads/images/'.$field['field_value'].'"/></br>';
                    } else {
                        $fieldsAllMessage .= '<b>'.$field['field_label'].'</b> <a href="'.$wdhURL.'uploads/files/'.$field['field_value'].'">'.$wdhUPLOAD['file_name'].'</a></br>';
                    }
                    
                    // Put Field in Message Required Fields
                    if ($field['field_required'] == 'true') {
                        $fieldsRequiredMessage .= '<b>'.$field['field_label'].'</b> '.$field['field_value'].'</br>';
                    }
                    
                    if ($field['field_type'] == "password") {
                        //$field['field_value'] = md5($field['field_value']);
                        $password = $field['field_value'];
                    }
                    
                    if ($field['field_type'] == 'user_type') {
                        $usertype = $field['field_value'];
                    }
                    
                    // XSS LIGHTTECTION
                    $field['field_value'] = $this->xss_cleaner($field['field_value']);
                    
                    // POST ID
                    $postID = $field['post_id'];
                    
                    // FORM ID
                    $formID = $field['form_id'];
                    
                    $formData = $this->getFormData($field['form_id']);

                    // Insert Fields in DB
                    if ($wdhSettings->form_mode != 'login') { 
                        // Form Record
                        if ($m < 1) {
                            $query = 'INSERT INTO ' . WDHFBPS_Forms_records_table . ' (cat_id) VALUES("' . $field['form_id'] . '")';
                            $wpdb->query($query) or die(mysql_error());
                            $customerId = $wpdb->insert_id;
                            $m++;
                        }
                        
                        // Fields
                        $query = 'INSERT INTO ' . $field["field_table"] . ' (field_id, cat_id, ' . $field['field_name'] . ', customer_id) VALUES("' . $field['field_id'] . '" , "' . $field['form_id'] . '", "' . $field['field_value'] . '", "' . $customerId . '")';
                        $wpdb->query($query) or die(mysql_error());
                    }
                    
                    // CHECK IF USERNAME EXIST 
                    if ($wdhSettings->form_mode == 'normal' || $wdhSettings->form_mode == 'register') {
                        
                        if (isset($username)){

                            if (username_exists($username)) {

                                if (isset($customerId)) {
                                    $where = array('cat_id' => $field['form_id']);
                                    $wpdb->delete(WDHFBPS_Forms_records_table,$where) or die(mysql_error());
                                    $wpdb->delete($field["field_table"],$where) or die(mysql_error());
                                }

                                echo 'username_exist'; die();
                            }
                        }
                    }

                    // Login Condition
                    
                    if ($i<1) {
                        $loginConditions .= 'Select * from '.$field['field_table'].' where '.$field['field_name'].'="'.$field['field_value'].'"';
                        $cookieAll .= $field['field_name']."@@".$field['field_value'];
                    } else{
                        $loginConditions .= ' AND '.$field['field_name'].'="'.$field['field_value'].'"';
                        $cookieAll .= "|".$field['field_name']."@@".$field['field_value'];
                    }
                    
                    $i++;
                }
                
                $fieldsADD = array('fields' => $fieldsNames,
                                   'values' => $fieldsValues,
                                   'table' => $key
                                   ); 
                array_push($fieldsDB, $fieldsADD);
            }
            
            if ($wdhSettings->form_mode == 'normal' || $wdhSettings->form_mode == 'register') { // Create WP User
                
                if (isset($username) && isset($password) && isset($email) && isset($usertype)){
                    $usertypeOld = $usertype;
                    
                    if ($usertype == 'affiliate') {
                        $usertype = 'subscriber';
                    }
                    
                    $user_id = wp_create_user( $username, $password, $email );

                    if( is_wp_error($user_id) ) {
                        echo $user_id->get_error_message(); die();
                    } else {
                        
                        // Update User Type
                        if ($usertype != 'subscriber') {
                            $newUser = new WP_User($user_id);
                            // Remove role
                            $newUser->remove_role('subscriber');
                            // Add role
                            $newUser->add_role($usertype);
                        }
                        
                        $expirationDate = '0000-00-00';
                        $dataNow = date('Y-m-d');

                        // Add Account Valability
                        if ($formData->account_valability == 'true') {

                            if (floatval($formData->av_period) > 0) {
                                $date1 = str_replace('-', '/', $dataNow);
                                $expirationDate = date('Y-m-d',strtotime($date1 . "+".$formData->av_period." ".$formData->av_measurement_unit));
                            }
                        }
                        
                        // Add In Users DB
                        if (isset($_SESSION['wdhAS_Username']) && $_SESSION['wdhAS_Username'] != ''){
                            $referalUserData = $this->getUserData($_SESSION['wdhAS_Username']);
                            $refID = $referalUserData->id;
                            $refAmount = floatval($referalUserData->amount)+floatval($formData->as_price);
                            
                            // Create User
                            $query = 'INSERT INTO ' . WDHFBPS_Users_table . ' (ref_id, user_id, form_id, username, email, usertype, expiration_date) VALUES("' . $refID . '", "'. $user_id .'" , "'. $field['form_id'] .'", "' . $username . '", "' . $email . '", "' . $usertypeOld . '", "' . $expirationDate . '")';
                            $wpdb->query($query) or die(mysql_error());
                            
                            // Create payment for Referal
                            $query = 'INSERT INTO ' . WDHFBPS_Payments_Records_table . ' (ref_id, user_id, form_id, amount, currency, currency_code, created_date) VALUES("' . $refID . '", "'. $user_id .'" , "'. $field['form_id'] .'", "' . $formData->as_price . '", "' . $formData->as_currency . '", "' . $formData->as_currency_code . '", "' . $dataNow . '")';
                            $wpdb->query($query) or die(mysql_error());
                            
                            // Update Referal Amount
                            $dataRef = array('amount' => $refAmount);
                            $where = array('id' => $referalUserData->id);
                            $wpdb->update(WDHFBPS_Users_table, $dataRef, $where) or die(mysql_error());
                            
                        } else {
                            // Create user
                            $query = 'INSERT INTO ' . WDHFBPS_Users_table . ' (user_id, form_id, username, email, usertype, expiration_date) VALUES("'. $user_id .'" , "'. $field['form_id'] .'", "' . $username . '", "' . $email . '", "' . $usertypeOld . '", "' . $expirationDate . '")';
                            $wpdb->query($query) or die(mysql_error());
                        }
                    }
                }
            }
            
            if ($wdhSettings->form_mode != 'login') {
                // Sending Emails to admin
                if ($wdhSettings->admin_email_notification) {
                    $this->sendMail("$wdhSettings->admin_email",'admin',$fieldsRequiredMessage,$fieldsAllMessage, $fields);
                }

                // Sending Emails to user
                if ($wdhSettings->user_email_notification) {
                    foreach($emails as $email){
                        $this->sendMail($email,'user',$fieldsRequiredMessage,$fieldsAllMessage, $fields);
                    }
                }
            }
            
            if ($wdhSettings->form_mode == 'login' ) {
                global $wdhDB;
                
                if (isset($username) && isset($password)){
                    $creds = array();
                    $creds['user_login'] = $username;
                    $creds['user_password'] = $password;
                    $creds['remember'] = true;
                    $newUser = wp_signon( $creds, false );

                    if (is_wp_error($newUser)) {
                        echo 'no_login'; die();
                    } else{
                        $cookieAll = $this->wdhEncrypt($cookieAll, $wdhDB['key']);
                        setcookie("wdh-login", $cookieAll, time()+3600*24,'/');
                        if ($formtype == 'free') {
                            echo 'success'; die();
                        }
                    }
                }
            }
            
            echo 'success';
            
            die();
            
        }
        
        function getCookieData($wdh_login){
            global $wdhDB;
            $wdh_login = $this->wdhDecrypt($wdh_login, $wdhDB['key']);
            $wdh_elements = explode('|',$wdh_login);
            $wdh_cookie = array();
            
            foreach($wdh_elements as $element){
                $element = explode('@@',$element);
                $wdh_cookie[$element[0]] = $element[1];
            }
            
            return $wdh_cookie;
        }
        
        function extract_text($string){
         $text_outside=array();
         $text_inside=array();
         $t="";
         for($i=0;$i<strlen($string);$i++)
         {
             if($string[$i]=='[')
             {
                 $text_outside[]=$t;
                 $t="";
                 $t1="";
                 $i++;
                 while($string[$i]!=']')
                 {
                     $t1.=$string[$i];
                     $i++;
                 }
                 $text_inside[] = $t1;

             }
             else {
                 if($string[$i]!=']')
                 $t.=$string[$i];
                 else {
                     continue;
                 }

             }
         }
         if($t!="")
         $text_outside[]=$t;
         
         return $text_inside;
       }
        
        function extractShortcodes($message, $fields){
            global $WDHFBPS_plugin;
            
            if (strpos($message,'[') !== false && strpos($message,']') !== false) {
                $shortcodes = $this->extract_text($message);
                
                if (!empty($shortcodes)) {
                    // [wdhfbps-value-id]
                    foreach($shortcodes as $shortcode){
                        $atts = explode('-',$shortcode);
                        $type = $atts[1];
                        $fieldID = $atts[2];
                        $message = str_replace('[wdhfbps-'.$type.'-'.$fieldID.']',$this->getFieldLabelandValue($fields,$fieldID,$type), $message);
                    }
                }
            }
            
            return $message;
        }
        
        function sendMail($to,$type = 'user',$fieldsRequiredMessage,$fieldsAllMessage, $fields){
            global $wdhSettings;
            global $wdhCurrentFormID;
            global $wdhFBPS_CONFIG;
            
            if (!isset($wdhFBPS_CONFIG['FORM_SENDER_NAME'])){
                $wdhFBPS_CONFIG['FORM_SENDER_NAME'] = '';
            }
            
            $formData = $this->getFormData($wdhCurrentFormID);
                    
            $header  = '';
            $header .= "Content-type: text/html; charset=utf-8"."\r\n";
            $header .= "MIME-Version: 1.1"."\r\n";
            
            if ($formData->use_smtp != 'true'){ // Default Email
                $header .= "From: ".$wdhFBPS_CONFIG['FORM_SENDER_NAME']." <".$wdhSettings->sender_email.">\r\n";
                $header .= "Reply-To:".$wdhSettings->sender_email;
                $this->phpMailer->CharSet = 'utf-8';
                $this->phpMailer->isMail();
                $this->phpMailer->IsHTML(true);
                $this->phpMailer->From = $formData->sender_email;
                $this->phpMailer->addReplyTo($formData->sender_email);
            } else { // Use SMTP
                
                if ($formData->smtp_ssl_connection == 'true'){
                    if ($formData->smtp_port == '587') {
                        $securePort = 'tls';
                    } else {
                        $securePort = 'ssl';
                    }
                } else {
                    $securePort = '';
                }
                $header .= "From: ".$wdhFBPS_CONFIG['FORM_SENDER_NAME']." <".$formData->smtp_email.">\r\n";
                $header .= "Reply-To:".$formData->smtp_email;
                $this->phpMailer->CharSet = 'utf-8';
                $this->phpMailer->isSMTP();
                $this->phpMailer->IsHTML(true);
                $this->phpMailer->Host = $formData->smtp_host;
                $this->phpMailer->SMTPAuth = TRUE;
                $this->phpMailer->Port = $formData->smtp_port;
                $this->phpMailer->Username = $formData->smtp_username;
                $this->phpMailer->Password = $formData->smtp_password;
                $this->phpMailer->SMTPSecure = $securePort;
                $this->phpMailer->From = $formData->smtp_email;
                $this->phpMailer->addReplyTo($formData->smtp_email);
            }
            
            if ($type == 'user'){
                $body = $formData->user_email_template;
                // ADD Shortcodes
                $body = $this->addShortcodes('[[FIELD_LIST_ALL]]',$body,$fieldsAllMessage);
                $body = $this->addShortcodes('[[FIELD_LIST_ONLY_REQUIRED]]',$body,$fieldsRequiredMessage);
                $body = $this->extractShortcodes($body, $fields);
                // Add Message
                $this->phpMailer->Subject = $wdhSettings->user_email_subject;
                $this->phpMailer->Body = $body;
                
                if ($wdhSettings->user_email_notification) {
                    wp_mail($to, $wdhSettings->user_email_subject, $body, $header);
                }
            } else {
                $body = $formData->admin_email_template;
                
                // ADD Shortcodes
                $body = $this->addShortcodes('[[FIELD_LIST_ALL]]',$body,$fieldsAllMessage);
                $body = $this->addShortcodes('[[FIELD_LIST_ONLY_REQUIRED]]',$body,$fieldsRequiredMessage);
                $body = $this->extractShortcodes($body, $fields);
                // Add Message
                $this->phpMailer->Subject = $wdhSettings->admin_email_notification;
                $this->phpMailer->Body = $body;
                if ($wdhSettings->admin_email_notification) {
                    wp_mail($to, $wdhSettings->admin_subject, $body, $header);
                }
            }
            
        }
        
        function addShortcodes($shortcode,$content,$add_content){
            $content = str_replace($shortcode,$add_content,$content);
            
            return $content;
        }
        
        function wdhExistFieldValueByTable($table, $fieldname, $fieldValue){
            global $wpdb;

            $results = $wpdb->get_results( 
                                "
                                SELECT * 
                                FROM $table
                                WHERE $fieldname = '$fieldValue'
                                "
                        );
            $no = 0;
            // Count Results
            if ($wpdb->num_rows > 0){
                $no=$wpdb->num_rows;
            }
            
            return $no;
        }
        
        function checkIfExist(){
            global $wdhSettings;
            
            $fieldValue = $_POST['value'];
            $fieldname = $_POST['name'];
            $table = $_POST['table'];
            $field_type = $_POST['field_type'];
            $formMode = $_POST['form_mode'];
            $i = 0;
            
            if ($formMode == 'login') {
                echo 'ok'; die();
            }
            
            if ($field_type == 'username') {

                if (username_exists($fieldValue)) {
                    $i++;
                    echo 'exist'; die();
                }
            }
            
            if ($field_type == 'email') {

                if (email_exists($fieldValue)) {
                    $i++;
                    echo 'exist'; die();
                }
            }

            if ($this->wdhExistFieldValueByTable($table, $fieldname, $fieldValue) > 0){
                $i++;
                echo 'exist'; die();
            }

            if ($i<1) {
                echo 'ok';
            }
            
            die();
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