<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineDatabase
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

    private function _get_eav_attributes()
    {
        return [
            'url' => 'string',
            'updated_at' => 'string',
            'level' => 'number',
            'title' => 'string',
            'meta_charset' => 'string',
            'meta_description' => 'string',
            'meta_keywords' => 'string',
            'meta_author' => 'string',
            'meta_viewport' => 'string',
            'qty_bases' => 'number',
            'qty_css_external_files' => 'number',
            'qty_css_internal_files' => 'number',
            'qty_javascripts' => 'number',
            'qty_h1s' => 'number',
            'qty_h2s' => 'number',
            'qty_h3s' => 'number',
            'qty_h4s' => 'number',
            'qty_h5s' => 'number',
            'qty_h6s' => 'number',
            'qty_hgroups' => 'number',
            'qty_sections' => 'number',
            'qty_navs' => 'number',
            'qty_asides' => 'number',
            'qty_articles' => 'number',
            'qty_addresses' => 'number',
            'qty_headers' => 'number',
            'qty_footers' => 'number',
            'qty_ps' => 'number',
            'qty_total_links' => 'number',
            'qty_internal_links' => 'number',
            'qty_external_links' => 'number',
            'qty_targeted_links' => 'number',
            'content_study' => 'text',
            'text_to_html_ratio' => 'number',
            'curlinfo_efective_url' => 'string',
            'curlinfo_http_code' => 'number',
            'curlinfo_filetime' => 'number',
            'curlinfo_total_time' => 'number',
            'curlinfo_namelookup_time' => 'number',
            'curlinfo_connect_time' => 'number',
            'curlinfo_pretransfer_time' => 'number',
            'curlinfo_starttransfer_time' => 'number',
            'curlinfo_redirect_count' => 'number',
            'curlinfo_redirect_time' => 'number',
            'curlinfo_redirect_url' => 'number',
            'curlinfo_primary_ip' => 'string',
            'curlinfo_primary_port' => 'number',
            'curlinfo_size_download' => 'number',
            'curlinfo_speed_download' => 'number',
            'curlinfo_request_size' => 'number',
            'curlinfo_content_length_download' => 'number',
            'curlinfo_content_type' => 'string',
            'curlinfo_response_code' => 'number',
            'curlinfo_http_connectcode' => 'number',
            'curlinfo_num_connects' => 'number',
            'curlinfo_appconnect_time' => 'number',
        ];
    }

    public function create_initial_tables()
    {
        global $wpdb;

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_entity ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'url VARCHAR(256) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_string ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value VARCHAR(256) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_text ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value TEXT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_number ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(16) NOT NULL,'
            .'value FLOAT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_queue ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'url VARCHAR(256) NOT NULL,'
            .'level INTEGER NOT NULL,'
            .'found_in_url VARCHAR(256) NOT NULL,'
            .'visited BOOLEAN DEFAULT false'
            .');';
        $wpdb->get_results($sql);

        update_option('tsm_db_version', 1);
    }

    public function remove_tables()
    {
        global $wpdb;

        $sql = 'DROP TABLE '.$wpdb->prefix.'the_seo_machine_url_entity;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'the_seo_machine_url_string;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'the_seo_machine_url_text;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'the_seo_machine_url_number;';
        $wpdb->get_results($sql);
        $sql = 'DROP TABLE '.$wpdb->prefix.'the_seo_machine_queue;';
        $wpdb->get_results($sql);
    }

    public function update_if_needed()
    {
        global $wpdb;
        $db_version = get_option('tsm_db_version');

        // Updates for v2..
        /*if ($db_version < $this->current_version
        and 2 > $db_version) {
            $sql = '';
            $wpdb->get_results($sql);

            ++$db_version;
        }*/

        update_option('tsm_db_version', $this->current_version);
    }

    public function save_url_in_queue($new_url, $level, $found_in_url)
    {
        global $wpdb;

        $new_url_count = $wpdb->get_var(
            'SELECT count(*) FROM '.$wpdb->prefix.'the_seo_machine_queue '
            ."WHERE url LIKE '".$new_url."';"
        );

        // If URL doesn't exists, add it..
        if (0 == $new_url_count) {
            $wpdb->get_results(
                'INSERT INTO '.$wpdb->prefix.'the_seo_machine_queue (url, level, found_in_url, visited) '
                ."VALUES ('".$new_url."', ".$level.", '".$found_in_url."', false)");
        }
    }

    public function save_url($data)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            'SELECT * FROM '.$wpdb->prefix.'the_seo_machine_url_entity '
            ."WHERE url LIKE '".$data['url']."';"
        );

        if (empty($results)) {
            // New URL..
            $wpdb->get_results(
                'INSERT INTO '.$wpdb->prefix.'the_seo_machine_url_entity (url) '
                ."VALUES ('".$data['url']."');"
            );
            $id_url = $wpdb->get_var(
                'SELECT id FROM '.$wpdb->prefix.'the_seo_machine_url_entity '
                ."WHERE url LIKE '".$data['url']."';"
            );
        } else {
            // If updating an existing URL, remove the old values..
            $id_url = $results->id;

            $wpdb->get_results(
                'REMOVE FROM '.$wpdb->prefix.'the_seo_machine_url_string '
                .'WHERE id_url = '.$id_url.';'
            );
            $wpdb->get_results(
                'REMOVE FROM '.$wpdb->prefix.'the_seo_machine_url_text '
                .'WHERE id_url = '.$id_url.';'
            );
            $wpdb->get_results(
                'REMOVE FROM '.$wpdb->prefix.'the_seo_machine_url_number '
                .'WHERE id_url = '.$id_url.';'
            );
        }

        // Finally insert the new data values..
        foreach ($data as $key => $value) {
            if ('url' != $key) {
                $wpdb->get_results(
                    'INSERT INTO '.$wpdb->prefix.'the_seo_machine_url_'
                    .TheSeoMachineDatabase::get_instance()->_get_eav_attributes()[$key].' (id_url, code, value) '
                    ."VALUES ('".$id_url."', '".$key."', '".$value."');"
                );
            }
        }
    }
}
