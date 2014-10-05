<?php

/*
* Title                   : Synoptic WordPress Responsive Visual Form Builder Light
* Version                 : 1.0
* File                    : wdhfbps.uninstall.php
* File Version            : 1.0
* Created / Last Modified : 13 March 2014
* Author                  : Web Developers House
* Copyright               : © 2013 WDH.IM
* Website                 : http://www.wdh.im
* Description             : Synoptic WordPress Responsive Visual Form Builder Light Uninstallation File.
*/
    
    function wdhfbpsUninstall() {
        // Delete Database
        wdhfbpsDeleteDatabase();
        // Delete Options
        wdhfbpsDeleteOptions();
    }

    function wdhfbpsDeleteDatabase() {
        global $wpdb;

        $tables = $wpdb->get_results('SHOW TABLES');

        foreach ($tables as $table){
            $tableName = 'Tables_in_'.DB_NAME;
            $table_name = $table->$tableName;

            if (strrpos($table_name, 'wdhfbps_') !== false){
                $wpdb->query("DROP TABLE IF EXISTS $table_name");
            }
        }
    }

    function wdhfbpsDeleteOptions() {
        // Delete Options
        delete_option('WDHFBPS_db_version');
        delete_option('WDHFBPS_language');
        delete_option('WDHFBPS_site_language');
    }


?>