<?php

defined('ABSPATH') or die('No no no');
if (!current_user_can('administrator')) {
    wp_die(__('Sorry, you are not allowed to manage options for this site.'));
}

$current_columns_to_show_array = explode(',', $current_columns_to_show);

?>

<style>hr{margin-top: 30px;}</style>

<form method="post" enctype="multipart/form-data" action="<?php
echo $_SERVER['REQUEST_URI'];
?>"
id="tsm_form"
name="tsm_form"
data-tsm_ajax_url="<?= admin_url('admin-ajax.php') ?>">

    <div class="wrap">
        <span style="float: right">
            Support the project, please donate <a href="https://paypal.me/jaimeninoles" target="_blank"><b>here</b></a>.<br>
            Need help? Ask <a href="https://jnjsite.com/the-seo-machine-for-wordpress/" target="_blank"><b>here</b></a>.
        </span>

        <h1><span class="dashicons dashicons-performance tsm-icon"></span> The SEO Machine</h1>

        <button type="button" name="tsm-btn-study-site" id="tsm-btn-study-site" class="tsm-btn-study-site">Study Site</button>
        <span id="tsm-box-study-site-status">Standby</span>
        * If you are studying the site and close the window, the study will stop.<br>
        <div class="tsm-progress-bar-border">
            <span class="tsm-progress-queue-text" id="tsm-progress-queue-text">Total 0%</span>
            <div class="tsm-progress-queue-content" id="tsm-progress-queue-content"></div>
        </div>

        <?php
        if (isset($tsmSms)) {
            echo $tsmSms;
        }
        settings_fields('tsm_options_group');
        do_settings_sections('tsm_options_group');
        wp_nonce_field('tsm', 'tsm_nonce');
        ?>

        <div class="table-responsive" id="tsm-datatable-container">
            <table 
            class="records_list table table-striped table-bordered table-hover" 
            id="tsm-datatable" 
            width="100%">
                <thead>
                    <tr>
                        <?php
                        $cont_cols = 0;
                        foreach (TheSeoMachineDatabase::get_instance()->get_eav_attributes() as $key => $value) {
                            if(in_array($key, $current_columns_to_show_array)) {
                                echo '<th>'.strtoupper($key).'</th>';
                                $cont_cols++;
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <?php
                        for ($i=0; $i < $cont_cols; $i++) { 
                            echo '<th>Filter..</th>';
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tsm-footer-actions-container">
            <div class="tsm-footer-actions-container-left">
                <input type="submit" name="tsm-submit" id="tsm-submit" class="button button-green tsm-btn-submit" value="Save this configs">

                <label for="quantity_per_batch">Quantity per Batch</label>
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

                <label for="time_between_batches">Time between Batches</label>
                <select name="time_between_batches" id="time_between_batches">
                    <option value="1"<?= (1 == $time_between_batches ? ' selected' : ''); ?>>1s</option>
                    <option value="5"<?= (5 == $time_between_batches ? ' selected' : ''); ?>>5s</option>
                    <option value="10"<?= (10 == $time_between_batches ? ' selected' : ''); ?>>10s</option>
                    <option value="30"<?= (30 == $time_between_batches ? ' selected' : ''); ?>>30s</option>
                    <option value="60"<?= (60 == $time_between_batches ? ' selected' : ''); ?>>60s</option>
                    <option value="120"<?= (120 == $time_between_batches ? ' selected' : ''); ?>>120s</option>
                </select>

                <label for="crawl_type">Crawl type</label>
                <select name="crawl_type" id="crawl_type">
                    <option value="in-width"<?= ('in-width' == $crawl_type ? ' selected' : ''); ?>>In width</option>
                    <option value="in-depth"<?= ('in-depth' == $crawl_type ? ' selected' : ''); ?>>In depth</option>
                    <option value="random"<?= ('random' == $crawl_type ? ' selected' : ''); ?>>Random</option>
                </select>

                <label for="autoreload_datatables">Auto reload Datatables</label>
                <select name="autoreload_datatables" id="autoreload_datatables">
                    <option value="-1"<?= (-1 == $autoreload_datatables ? ' selected' : ''); ?>>No</option>
                    <option value="5"<?= (5 == $autoreload_datatables ? ' selected' : ''); ?>>5s</option>
                    <option value="10"<?= (10 == $autoreload_datatables ? ' selected' : ''); ?>>10s</option>
                    <option value="30"<?= (30 == $autoreload_datatables ? ' selected' : ''); ?>>30s</option>
                    <option value="60"<?= (60 == $autoreload_datatables ? ' selected' : ''); ?>>60s</option>
                    <option value="120"<?= (120 == $autoreload_datatables ? ' selected' : ''); ?>>120s</option>
                </select>
            </div>
            <div class="tsm-footer-actions-container-right">
                <button type="submit" name="tsm-submit-reset-queue" id="tsm-submit-reset-queue" class="button button-red button-reset-queue">Reset Queue</button>
                <button type="submit" name="tsm-submit-remove-all" id="tsm-submit-remove-all" class="button button-red button-remove-all">Remove All Data</button>
            </div>
        </div>
        
    </div>

    <hr>

    <div class="tsm-current-columns-to-show-container">
        <h2>Current columns to show</h2>
        <p>Be careful with this, if you add too many columns you can crash the server when looking for information in the database.</p>
        <div class="tsm-columns-container">
            <?php

            foreach (TheSeoMachineDatabase::get_instance()->get_eav_attributes() as $key => $value) {
                ?>
                <label for="checkbox_current_columns_to_show_<?= $key; ?>">
                <input 
                type="checkbox" 
                name="checkbox_current_columns_to_show_<?= $key; ?>" 
                id="checkbox_current_columns_to_show_<?= $key; ?>"
                <?= (in_array($key, $current_columns_to_show_array) ? 'checked' : ''); ?>>
                <?= strtoupper($key); ?>
                </label>
                <?php
            }

            ?>
        </div>
        <div style="clear:both;"></div>
        <input type="hidden" name="tsm-current-columns-to-show" id="tsm-current-columns-to-show" value="<?= $current_columns_to_show ?>">
        <input type="submit" name="tsm-submit-save-current-columns" id="tsm-submit-save-current-columns" class="button button-green tsm-btn-submit" value="Save current columns to show">
    </div>
</form>

<hr>
<p>Current DB version: <?= $tsm_db_version ?></p>
<p>This plugin uses the awesome Datatables, you can find it here: <a href="https://www.datatables.net/" target="_blank">https://www.datatables.net/</a></p>
<script>
    let weAreInTheSeoMachine = true
</script>