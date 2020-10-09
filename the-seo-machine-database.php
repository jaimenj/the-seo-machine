<?php

defined('ABSPATH') or die('No no no');

class TheSeoMachineDatabase
{
    private static $instance;

    private $current_version = 2;

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

    public function get_eav_attributes()
    {
        $data = [
            'id' => 'number',
            'url' => 'string',
            'updated_at' => 'string',
            'level' => 'number',
            'title' => 'string',
            'http_code' => 'number',
            'time_consumed' => 'number',
            'starttransfer_time' => 'number',
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
        ];

        foreach (TheSeoMachineCore::get_instance()->get_available_headers() as $header_name) {
            $data[str_replace('-', '_', strtolower($header_name))] = 'string';
        }

        return $data;
    }

    public function create_initial_tables()
    {
        global $wpdb;

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_entity ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'url VARCHAR(512) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_string ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(64) NOT NULL,'
            .'value VARCHAR(256) NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_text ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(64) NOT NULL,'
            .'value TEXT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_url_number ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'id_url INTEGER NOT NULL,'
            .'code VARCHAR(64) NOT NULL,'
            .'value FLOAT NOT NULL'
            .');';
        $wpdb->get_results($sql);

        $sql = 'CREATE TABLE '.$wpdb->prefix.'the_seo_machine_queue ('
            .'id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,'
            .'url VARCHAR(512) NOT NULL,'
            .'level INTEGER NOT NULL,'
            .'found_in_url VARCHAR(512) NOT NULL,'
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
        if ($db_version < $this->current_version
        and 2 > $db_version) {
            $sql = 'alter table '.$wpdb->prefix.'the_seo_machine_url_number 
            add constraint fk_number_to_entity foreign key (id_url)
            references '.$wpdb->prefix.'the_seo_machine_url_entity(id)
            on delete cascade;';
            $wpdb->get_results($sql);

            $sql = 'alter table '.$wpdb->prefix.'the_seo_machine_url_string 
            add constraint fk_string_to_entity foreign key (id_url)
            references '.$wpdb->prefix.'the_seo_machine_url_entity(id)
            on delete cascade;';
            $wpdb->get_results($sql);

            $sql = 'alter table '.$wpdb->prefix.'the_seo_machine_url_text 
            add constraint fk_text_to_entity foreign key (id_url)
            references '.$wpdb->prefix.'the_seo_machine_url_entity(id)
            on delete cascade;';
            $wpdb->get_results($sql);

            ++$db_version;
        }

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

        $result = $wpdb->get_row(
            'SELECT * FROM '.$wpdb->prefix.'the_seo_machine_url_entity '
            ."WHERE url LIKE '".$data['url']."';"
        );

        if (empty($result)) {
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
            $id_url = $result->id;

            $wpdb->get_results(
                'DELETE FROM '.$wpdb->prefix.'the_seo_machine_url_string '
                .'WHERE id_url = '.$id_url.';'
            );
            $wpdb->get_results(
                'DELETE FROM '.$wpdb->prefix.'the_seo_machine_url_text '
                .'WHERE id_url = '.$id_url.';'
            );
            $wpdb->get_results(
                'DELETE FROM '.$wpdb->prefix.'the_seo_machine_url_number '
                .'WHERE id_url = '.$id_url.';'
            );
        }

        // Finally insert the new data values..
        foreach ($data as $key => $value) {
            if ('url' != $key) {
                $key = str_replace('-', '_', strtolower($key));
                $wpdb->get_results(
                    'INSERT INTO '.$wpdb->prefix.'the_seo_machine_url_'
                    .$this->get_eav_attributes()[$key].' (id_url, code, value) '
                    ."VALUES ('".$id_url."', '".$key."', '".$value."');"
                );
            }
        }
    }
}
