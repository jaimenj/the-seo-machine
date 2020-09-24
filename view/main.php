<?php

defined('ABSPATH') or die('No no no');
if (!current_user_can('administrator')) {
    wp_die(__('Sorry, you are not allowed to manage options for this site.'));
}

?>

<form method="post" enctype="multipart/form-data" action="<?php
echo $_SERVER['REQUEST_URI'];
?>" id="this_form" name="this_form">

    <div class="wrap">
        <span style="float: right">
            Support the project, please donate <a href="https://paypal.me/jaimeninoles" target="_blank"><b>here</b></a>.<br>
            Need help? Ask <a href="https://jnjsite.com/the-seo-machine-for-wordpress/" target="_blank"><b>here</b></a>.
        </span>

        <h1><span class="dashicons dashicons-performance tsm-icon"></span> The SEO Machine</h1>

        <?php
        if (isset($tsmSms)) {
            echo $tsmSms;
        }
        settings_fields('tsm_options_group');
        do_settings_sections('tsm_options_group');
        wp_nonce_field('tsm', 'tsm_nonce');
        ?>

        <p>
            <input type="submit" name="tsm-submit" id="tsm-submit" class="button button-green tsm-btn-submit" value="Save this configs">

            <label for="quantity_per_batch">Quantity per batch</label>
            <select name="quantity_per_batch" id="quantity_per_batch">
                <option value="1"<?= (1 == $quantity_per_batch ? ' selected' : ''); ?>>1</option>
                <option value="2"<?= (2 == $quantity_per_batch ? ' selected' : ''); ?>>2</option>
                <option value="3"<?= (3 == $quantity_per_batch ? ' selected' : ''); ?>>3</option>
                <option value="4"<?= (4 == $quantity_per_batch ? ' selected' : ''); ?>>4</option>
                <option value="5"<?= (5 == $quantity_per_batch ? ' selected' : ''); ?>>5</option>
                <option value="6"<?= (6 == $quantity_per_batch ? ' selected' : ''); ?>>6</option>
                <option value="7"<?= (7 == $quantity_per_batch ? ' selected' : ''); ?>>7</option>
                <option value="8"<?= (8 == $quantity_per_batch ? ' selected' : ''); ?>>8</option>
                <option value="9"<?= (9 == $quantity_per_batch ? ' selected' : ''); ?>>9</option>
                <option value="10"<?= (10 == $quantity_per_batch ? ' selected' : ''); ?>>10</option>
            </select>

            <label for="time_between_batches">Time between batches</label>
            <select name="time_between_batches" id="time_between_batches">
                <option value="1"<?= (1 == $time_between_batches ? ' selected' : ''); ?>>1s</option>
                <option value="5"<?= (5 == $time_between_batches ? ' selected' : ''); ?>>5s</option>
                <option value="10"<?= (10 == $time_between_batches ? ' selected' : ''); ?>>10s</option>
                <option value="30"<?= (30 == $time_between_batches ? ' selected' : ''); ?>>30s</option>
                <option value="60"<?= (60 == $time_between_batches ? ' selected' : ''); ?>>60s</option>
                <option value="120"<?= (120 == $time_between_batches ? ' selected' : ''); ?>>120s</option>
            </select>
        </p>

        <div class="table-responsive" id="tsm-datatable-container">
            <table 
            class="records_list table table-striped table-bordered table-hover" 
            id="tsm-datatable" 
            data-ajax_datatables_server_processing_url="<?php 
                echo get_site_url().'/wp-admin/admin-ajax.php?action=tsm_urls';
            ?>"
            width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>updated_at</th>
                        <th>Level</th>
                        <th>Title</th>
                        <th>Response</th>
                        <th>meta_charset</th>
                        <th>meta_description</th>
                        <th>meta_keywords</th>
                        <th>meta_author</th>
                        <th>meta_viewport</th>
                        <th>qty_bases</th>
                        <th>qty_css_external_files</th>
                        <th>qty_css_internal_files</th>
                        <th>qty_javascripts</th>
                        <th>qty_h1s</th>
                        <th>qty_h2s</th>
                        <th>qty_h3s</th>
                        <th>qty_h4s</th>
                        <th>qty_h5s</th>
                        <th>qty_h6s</th>
                        <th>qty_hgroups</th>
                        <th>qty_sections</th>
                        <th>qty_navs</th>
                        <th>qty_asides</th>
                        <th>qty_articles</th>
                        <th>qty_addresses</th>
                        <th>qty_headers</th>
                        <th>qty_footers</th>
                        <th>qty_ps</th>
                        <th>qty_total_links</th>
                        <th>qty_internal_links</th>
                        <th>qty_external_links</th>
                        <th>qty_targeted_links</th>
                        <th>content_study</th>
                        <th>text_to_html_ratio</th>
                        <th>curlinfo_efective_url</th>
                        <th>curlinfo_http_code</th>
                        <th>curlinfo_filetime</th>
                        <th>curlinfo_total_time</th>
                        <th>curlinfo_namelookup_time</th>
                        <th>curlinfo_connect_time</th>
                        <th>curlinfo_pretransfer_time</th>
                        <th>curlinfo_starttransfer_time</th>
                        <th>curlinfo_redirect_count</th>
                        <th>curlinfo_redirect_time</th>
                        <th>curlinfo_redirect_url</th>
                        <th>curlinfo_primary_ip</th>
                        <th>curlinfo_primary_port</th>
                        <th>curlinfo_size_download</th>
                        <th>curlinfo_speed_download</th>
                        <th>curlinfo_request_size</th>
                        <th>curlinfo_content_length_download</th>
                        <th>curlinfo_content_type</th>
                        <th>curlinfo_http_connectcode</th>
                        <th>curlinfo_num_connects</th>
                        <th>curlinfo_appconnect_time</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                        <th>Filter..</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tsm-footer-actions-container">
            <div class="tsm-footer-actions-container-left">
                <button type="button" name="tsm-btn-study-site" id="tsm-btn-study-site" class="button button-study-site">Study Site</button>
                <span id="tsm-box-study-site-status">Standby</span>
            </div>
            <div class="tsm-footer-actions-container-right">
                <button type="submit" name="tsm-submit-reset-queue" id="tsm-submit-reset-queue" class="button button-red button-reset-queue">Reset Queue</button>
                <button type="submit" name="tsm-submit-remove-all" id="tsm-submit-remove-all" class="button button-red button-remove-all">Remove All Data</button>
            </div>
        </div>
        <p>* If you are studying the site and close the window, the study will stop.</p>
    </div>

</form>
<style>hr{margin-top: 30px;}</style>
<hr>
<script>
    let weAreInTheSeoMachine = true
</script>