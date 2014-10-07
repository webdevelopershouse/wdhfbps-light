<?php
/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder Light
* Version                 : 1.0
* File                    : wdhfbps.config.php
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Light Configuration File.
*/
global $wdhFBPS_CONFIG;

$wdhFBPS_CONFIG['plugin_version']                              = 1.0;
// Form Messages
$wdhFBPS_CONFIG['FORM_NAME']                                   = 'name'; // Set default text for the field
$wdhFBPS_CONFIG['FORM_DISPLAY_TYPE']                           = 'normal'; // Set default text for display_type
$wdhFBPS_CONFIG['FORM_MODE']                                   = 'contact'; // Set default text for succesfull sent message
$wdhFBPS_CONFIG['FORM_DESIGN']                                 = 'fixed'; // Set your form design : responsive / fixed
$wdhFBPS_CONFIG['FORM_POPUP_BUTTON']                           = 'popup'; // Set default text for popup_button
$wdhFBPS_CONFIG['FORM_CLASS']                                  = ''; // Set default text for class
$wdhFBPS_CONFIG['FORM_CSS']                                    = ''; // Set default text for css
$wdhFBPS_CONFIG['FORM_MESSAGES_SUCCESFULL']                    = 'Congratulations your message has been sent'; // Set default text for succesfull sent message
$wdhFBPS_CONFIG['FORM_MESSAGES_FAILED']                        = 'Your message has not been sent.Try again.'; // Set default text for failed sent message
$wdhFBPS_CONFIG['FORM_MESSAGES_CLASS']                         = ''; // Set default text for message class
$wdhFBPS_CONFIG['FORM_MESSAGES_CSS']                           = ''; // Set default text for message css
$wdhFBPS_CONFIG['FORM_SENDER_NAME']                            = 'Kirk All'; // Set default name
$wdhFBPS_CONFIG['FORM_SENDER_EMAIL']                           = 'sender@yourwebsite.com'; // Set default email
$wdhFBPS_CONFIG['FORM_ADMIN_EMAIL_NOTIFICATION']               = 'true'; // Set default email
$wdhFBPS_CONFIG['FORM_ADMIN_EMAIL']                            = 'admin@yourwebsite.com'; // Set default email
$wdhFBPS_CONFIG['FORM_ADMIN_SUBJECT']                          = 'Your admin subject'; // Set default admin subject
$wdhFBPS_CONFIG['FORM_ADMIN_EMAIL_TEMPLATE']                   = '<h1>Fields:</h1><p>[[FIELD_LIST_ALL]]</p>'; // Set default email template
$wdhFBPS_CONFIG['FORM_USER_EMAIL_NOTIFICATION']                = 'true'; // Set default email
$wdhFBPS_CONFIG['FORM_USER_EMAIL_TEMPLATE']                    = '<h1>Required Fields:</h1><p>[[FIELD_LIST_ONLY_REQUIRED]]</p>'; // Set default email template
$wdhFBPS_CONFIG['FORM_USER_EMAIL_SUBJECT']                     = 'Your user subject'; // Set default user subject
$wdhFBPS_CONFIG['FORM_USE_SMTP']                               = 'false'; //Set default use SMTP
$wdhFBPS_CONFIG['FORM_SMTP_HOST']                              = ''; //Set default SMTP host
$wdhFBPS_CONFIG['FORM_SMTP_PORT']                              = ''; //Set default SMTP port
$wdhFBPS_CONFIG['FORM_SMTP_EMAIL']                             = 'smtp@yourwebsite.com'; //Set default SMTP email
$wdhFBPS_CONFIG['FORM_SMTP_USERNAME']                          = ''; //Set default SMTP username
$wdhFBPS_CONFIG['FORM_SMTP_PASSWORD']                          = ''; //Set default SMTP password
$wdhFBPS_CONFIG['FORM_SMTP_SSL_CONNECTION']                    = ''; //Set default SMTP ssl connection
$wdhFBPS_CONFIG['FORM_JSWDHEDFP_AFTER_SAVE']                   = ''; // Set default js_wdhedfp_after_save
$wdhFBPS_CONFIG['FORM_JS_HOOK_AFTER_SAVE']                     = ''; //Set default js hook after send form
$wdhFBPS_CONFIG['FORM_USER_ROLE']                              = 'subscriber'; //Set default user role

// Form Design
$wdhFBPS_CONFIG['FORM_TEXT_COLOR']                             = '000000'; // Set default text color for form design
$wdhFBPS_CONFIG['FORM_TEXT_FONT_FAMILY']                       = 'Times New Roman'; // Set default text font family for form design
$wdhFBPS_CONFIG['FORM_TEXT_OWN_FONT']                          = ''; // Set default text own font for form design
$wdhFBPS_CONFIG['FORM_TEXT_FONT_SIZE']                         = '16'; // Set default text font size for form design
$wdhFBPS_CONFIG['FORM_TEXT_ALIGN']                             = 'left'; // Set default text align for form design
$wdhFBPS_CONFIG['FORM_TEXT_FONT_STYLE']                        = 'regular'; // Set default text font style for form design
$wdhFBPS_CONFIG['FORM_TEXT_FONT_WEIGHT']                       = '100'; // Set default text font weight for form design
$wdhFBPS_CONFIG['FORM_BOX_MARGIN_LEFT']                        = '0'; // Set default box margin left for form design
$wdhFBPS_CONFIG['FORM_BOX_MARGIN_RIGHT']                       = '0'; // Set default box margin right for form design
$wdhFBPS_CONFIG['FORM_BOX_MARGIN_TOP']                         = '0'; // Set default box margin top for form design
$wdhFBPS_CONFIG['FORM_BOX_MARGIN_BOTTOM']                      = '0'; // Set default box margin bottom for form design
$wdhFBPS_CONFIG['FORM_BOX_PADDING_LEFT']                       = '1'; // Set default box padding left for form design
$wdhFBPS_CONFIG['FORM_BOX_PADDING_RIGHT']                      = '1'; // Set default box padding right for form design
$wdhFBPS_CONFIG['FORM_BOX_PADDING_TOP']                        = '20'; // Set default box padding top for form design
$wdhFBPS_CONFIG['FORM_BOX_PADDING_BOTTOM']                     = '20'; // Set default box padding bottom for form design
$wdhFBPS_CONFIG['FORM_BOX_BACKGROUND_COLOR']                   = 'ffffff'; // Set default box backgound color for form design
$wdhFBPS_CONFIG['FORM_BOX_BORDER_COLOR']                       = 'fccf1b'; // Set default box border color for form design
$wdhFBPS_CONFIG['FORM_BOX_BORDER_SIZE']                        = '1'; // Set default box border size for form design
$wdhFBPS_CONFIG['FORM_BOX_BORDER_TYPE']                        = 'solid'; // Set default box borderr type  for form design
$wdhFBPS_CONFIG['FORM_BOX_BORDER_RADIUS']                      = '0'; // Set default box border radius for form design
$wdhFBPS_CONFIG['FORM_WIDTH']                                  = '300'; // Set default box width for form design

// Form  Field Design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_COLOR']                       = '000000'; // Set default text color for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_FAMILY']                 = 'Times New Roman'; // Set default text font family for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_OWN_FONT']                    = ''; // Set default text own font for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_SIZE']                   = '16'; // Set default text font size for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_ALIGN']                       = 'left'; // Set default text align for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_STYLE']                  = 'regular'; // Set default text font style for form design
$wdhFBPS_CONFIG['FORM_FIELD_TEXT_FONT_WEIGHT']                 = '100'; // Set default text font weight for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_LEFT']                  = '0'; // Set default box margin left for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_RIGHT']                 = '0'; // Set default box margin right for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_TOP']                   = '0'; // Set default box margin top for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_MARGIN_BOTTOM']                = '10'; // Set default box margin bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_LEFT']                 = '0'; // Set default box padding left for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_RIGHT']                = '0'; // Set default box padding right for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_TOP']                  = '0'; // Set default box padding top for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_PADDING_BOTTOM']               = '0'; // Set default box padding bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_BACKGROUND_COLOR']             = 'inherit'; // Set default box backgound color for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_COLOR']                 = 'fccf1b'; // Set default box border color for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_SIZE']                  = '0'; // Set default box border size for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_TYPE']                  = 'solid'; // Set default box borderr type  for form design
$wdhFBPS_CONFIG['FORM_FIELD_BOX_BORDER_RADIUS']                = '0'; // Set default box border radius for form design
$wdhFBPS_CONFIG['FORM_FIELD_WIDTH']                            = '100'; // Set default box width for form design [%]
$wdhFBPS_CONFIG['FORM_FIELD_HEIGHT']                           = '22'; // Set default box height for form design [px]

// Form  Field Label Design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_COLOR']                 = '000000'; // Set default text color for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_FAMILY']           = 'Times New Roman'; // Set default text font family for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_OWN_FONT']              = ''; // Set default text own font for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_SIZE']             = '14'; // Set default text font size for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_ALIGN']                 = 'left'; // Set default text align for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_STYLE']            = 'regular'; // Set default text font style for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_TEXT_FONT_WEIGHT']           = '400'; // Set default text font weight for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_LEFT']            = '0'; // Set default box margin left for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_RIGHT']           = '0'; // Set default box margin right for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_TOP']             = '0'; // Set default box margin top for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_MARGIN_BOTTOM']          = '0'; // Set default box margin bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_LEFT']           = '0'; // Set default box padding left for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_RIGHT']          = '0'; // Set default box padding right for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_TOP']            = '0'; // Set default box padding top for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_PADDING_BOTTOM']         = '0'; // Set default box padding bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BACKGROUND_COLOR']       = 'inherit'; // Set default box backgound color for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_COLOR']           = 'fccf1b'; // Set default box border color for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_SIZE']            = '0'; // Set default box border size for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_TYPE']            = 'solid'; // Set default box borderr type  for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_BOX_BORDER_RADIUS']          = '0'; // Set default box border radius for form design
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_WIDTH']                      = '49'; // Set default box width for form design [%]
$wdhFBPS_CONFIG['FORM_FIELD_LABEL_HEIGHT']                     = '22'; // Set default box height for form design [px]

// Form  Field Input Design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_COLOR']                 = '000000'; // Set default text color for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_FAMILY']           = 'Times New Roman'; // Set default text font family for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_OWN_FONT']              = ''; // Set default text own font for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_SIZE']             = '12'; // Set default text font size for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_ALIGN']                 = 'left'; // Set default text align for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_STYLE']            = 'regular'; // Set default text font style for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_TEXT_FONT_WEIGHT']           = 'bold'; // Set default text font weight for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_LEFT']            = '0'; // Set default box margin left for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_RIGHT']           = '0'; // Set default box margin right for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_TOP']             = '0'; // Set default box margin top for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_MARGIN_BOTTOM']          = '0'; // Set default box margin bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_LEFT']           = '0'; // Set default box padding left for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_RIGHT']          = '0'; // Set default box padding right for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_TOP']            = '0'; // Set default box padding top for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_PADDING_BOTTOM']         = '0'; // Set default box padding bottom for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BACKGROUND_COLOR']       = 'f8dc6b'; // Set default box backgound color for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_COLOR']           = 'fccf1b'; // Set default box border color for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_SIZE']            = '1'; // Set default box border size for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_TYPE']            = 'solid'; // Set default box borderr type  for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_BOX_BORDER_RADIUS']          = '0';     // Set default box border radius for form design
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_WIDTH']                      = '49';    // Set default box width for form design [%]
$wdhFBPS_CONFIG['FORM_FIELD_INPUT_HEIGHT']                     = '22';    // Set default box height for form design [px]
