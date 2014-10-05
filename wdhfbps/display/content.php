<?php
/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder Light
* Version                 : 1.0
* File                    : content.php
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Light Templates Class.
*/

    if (!class_exists("WDHFBPSDisplay")){
        class WDHFBPSDisplay{
            private $wdhLibs;
            
            function WDHFBPSDisplay($jsInit = true){// Constructor.
                global $jsInitStatus;
                $jsInitStatus = $jsInit;
                
                // INIT LIBS     
                if (class_exists("wdhLibs")){
                    $this->wdhLibs = new wdhLibs();
                }
            }
            
            function returnLanguagesVars(){// Languages in JS.
                global $jsInitStatus;
                $current_page     = '';
                $WDHFBPS_curr_page = 'in_form';
                
                if (isset($_GET['page'])) { 
                    $current_page = $_GET['page'];
                    switch($current_page){
                        case "wdhfbps":
                            $WDHFBPS_curr_page = "Forms";
                            break;
                        default:
                            $WDHFBPS_curr_page = "Forms";
                            break;
                    }
                }
                if (class_exists("WDHFormBuilderwithPaymentSystemAdmin")){
                    $wdhfbps_backend = new WDHFormBuilderwithPaymentSystemAdmin();
                }
                $defined_constants = $wdhfbps_backend->returnConstants('WDHFBPS_');
                
                $wdhfbps_language_now = get_option('WDHFBPS_language');

                if ($wdhfbps_language_now == ''){
                    $wdhfbps_language_now = 'en';
                }
                
                if ($jsInitStatus == true) {
?>

<script type="text/javaScript">
    $jWDH(document).ready(function(){
        window.WDHFBPS_curr_page = "<?php echo $WDHFBPS_curr_page?>";
        window.WDHFBPS_plugin_url = "<?php echo WP_PLUGIN_URL.'/wdhfbps/'?>";
        window.WDHFBPS_plugin_absolute = "<?php echo ABSPATH.'wp-content/plugins/wdhfbps/'?>";
        window.WDHFBPS_languages_options = '<?php echo $this->wdhLibs->getLanguagesOptions($wdhfbps_language_now); ?>'; 
<?php 
        foreach ($defined_constants as $key=>$defined_constant_value) {
?>
        window.<?php echo $key; ?> = "<?php echo $defined_constant_value; ?>";
<?php 
        }
?>
    });
</script>

<?php 
                }
            }
            
            function Languages($JSfunction = 'wdhfbpsChangeLanguage'){
                $wdhfbps_language_now = get_option('WDHFBPS_language');
                $JSformID = '';
                if ($wdhfbps_language_now == ''){
                    $wdhfbps_language_now = 'en';
                }
                
                if($JSfunction == "wdhfbpsChangeLanguageFields"){
                    global $post; 
                    $JSformID = ','.$post->ID;
                }
                ?>
                
                
                <select id="WDHFBPS-language" onchange="<?php echo $JSfunction; ?>(this.value<?php echo $JSformID; ?>)">
                    <?php echo $this->wdhLibs->getLanguagesOptions($wdhfbps_language_now); ?>
                </select>
<?php
            }
            
            // Forms
            function Forms(){
                if (class_exists("WDHFormBuilderwithPaymentSystemAdmin")){
                    $wdhfbps_backend = new WDHFormBuilderwithPaymentSystemAdmin();
                }
                $this->returnLanguagesVars();
                global $post;
?>  

    <div id="wdhfbps-new-field-box" class="WDHFBPS-backend-popup">
        <div class="content">
            <div class="close" onclick="wdhfbpsCancel();">
                 x
            </div>
            <div class="title">
                <?php echo WDHFBPS_FBPS_CATEGORY_NEW_FIELD; ?>
            </div>
            <form>
                <label for="field-name"><?php echo WDHFBPS_FBPS_CATEGORY_FIELD_NAME; ?>:</label>
                <input id="field-name" type="text" placeholder="<?php echo WDHFBPS_FBPS_CATEGORY_NEW_FIELD; ?>">
                <input type="hidden" id="field-category-id">
                <input type="button" name="save" value="<?php echo WDHFBPS_FBPS_CUSTOMER_SAVE; ?>" onclick="wdhfbpsAddField();" class="save" id="wdhfbps-save-field">
                <input type="button" name="cancel" value="<?php echo WDHFBPS_FBPS_CUSTOMER_CANCEL; ?>" onclick="wdhfbpsCancel();" class="cancel" id="wdhfbps-cancel-field">
            </form>
        </div>
    </div><!-- .WDHFBPS-backend-popup !-->
    
    <div id="wdhfbps-new-coupon-box" class="WDHFBPS-backend-popup">
        <div class="content">
            <div class="close" onclick="wdhfbpsCancel();">
                 x
            </div>
            <div class="title">
                <?php echo WDHFBPS_FBPS_CATEGORY_NEW_COUPON; ?>
            </div>
            <form>
                <label for="coupon-name"><?php echo WDHFBPS_FBPS_CATEGORY_COUPON_NAME; ?>:</label>
                <input id="coupon-name" type="text" placeholder="<?php echo WDHFBPS_FBPS_CATEGORY_NEW_COUPON; ?>">
                <input type="button" name="save" value="<?php echo WDHFBPS_FBPS_CUSTOMER_SAVE; ?>" onclick="wdhfbpsAddCoupon();" class="save" id="wdhfbps-save-coupon">
                <input type="button" name="cancel" value="<?php echo WDHFBPS_FBPS_CUSTOMER_CANCEL; ?>" onclick="wdhfbpsCancel();" class="cancel" id="wdhfbps-cancel-coupon">
            </form>
        </div>
    </div><!-- .WDHFBPS-backend-popup !-->  
    
    <div id="wdhfbps-new-fee-box" class="WDHFBPS-backend-popup">
        <div class="content">
            <div class="close" onclick="wdhfbpsCancel();">
                 x
            </div>
            <div class="title">
                <?php echo WDHFBPS_FBPS_CATEGORY_NEW_FEE; ?>
            </div>
            <form>
                <label for="fee-name"><?php echo WDHFBPS_FBPS_CATEGORY_FEE_NAME; ?>:</label>
                <input id="fee-name" type="text" placeholder="<?php echo WDHFBPS_FBPS_CATEGORY_NEW_FEE; ?>">
                <input type="button" name="save" value="<?php echo WDHFBPS_FBPS_CUSTOMER_SAVE; ?>" onclick="wdhfbpsAddFee();" class="save" id="wdhfbps-save-fee">
                <input type="button" name="cancel" value="<?php echo WDHFBPS_FBPS_CUSTOMER_CANCEL; ?>" onclick="wdhfbpsCancel();" class="cancel" id="wdhfbps-cancel-fee">
            </form>
        </div>
    </div><!-- .WDHFBPS-backend-popup !--> 
        
    <div class="WDHFBPS-backend">
        <div class="WDHFBPS-backend-language">
            <?php echo WDHFBPS_LANGUAGE; ?>:&nbsp; 
            <?php $this->Languages(); ?>
        </div>
        
        <!-- header !-->
        <div class="WDHFBPS-header">
            <h1><?php echo WDHFBPS_TITLE_VERSION; ?> <span style="color:#ccc;">LIGHT</span></h1>
        </div>
        <!-- end header !-->
        
        <div class="WDHFBPS-backend-box">
            
            <!-- Forms list -->
            <div id="wdhfbps-loader-categories"></div>
            <div id="all-forms" class="buttons-secondbar">
            </div>
           
        </div>
        
        <?php include_once 'footer.php'; ?>
    </div>
<?php 
            }
        }
    }
    
?>