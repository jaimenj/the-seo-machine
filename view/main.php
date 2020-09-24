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
?>" id="tsm_form" name="tsm_form">

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
<script>
    let weAreInTheSeoMachine = true
</script>