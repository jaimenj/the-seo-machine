<?php

defined('ABSPATH') or die('No no no');

class MySeoMachineDatabase
{
    private static $instance;

    private $current_version = 1;

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->update_if_needed();
    }

    public function create_initial_tables()
    {
        global $wpdb;

        // Main table..
        $sql = 'CREATE TABLE '.$wpdb->prefix.'my_seo_machine_url_entity ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'url VARCHAR(256) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'my_seo_machine_url_eav_attribute ('
            .'code VARCHAR(16) NOT NULL,'
            .'type VARCHAR(16) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'my_seo_machine_url_string ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value VARCHAR(256) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'my_seo_machine_url_text ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value TEXT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'my_seo_machine_url_number ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value FLOAT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        update_option('msm_db_version', 1);
    }

    public function remove_tables()
    {
        global $wpdb;

        $sql = 'DROP TABLE '.$wpdb->prefix.'my_seo_machine_url_entity;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'my_seo_machine_url_eav_attribute;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'my_seo_machine_url_string;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'my_seo_machine_url_text;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'my_seo_machine_url_number;';
        $wpdb->get_results($sql);
    }

    public function update_if_needed()
    {
        global $wpdb;
        $db_version = get_option('msm_db_version');

        // Updates for v2..
        /*if ($db_version < $this->current_version
        and 2 > $db_version) {
            $sql = '';
            $wpdb->get_results($sql);

            ++$db_version;
        }*/

        update_option('msm_db_version', $this->current_version);
    }
}
