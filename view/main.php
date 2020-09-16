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
            Need help? Ask <a href="https://jnjsite.com/my-seo-machine-for-wordpress/" target="_blank"><b>here</b></a>.
        </span>

        <h1><span class="dashicons dashicons-performance msm-icon"></span> My SEO Machine</h1>

        <?php
        if (isset($msmSms)) {
            echo $msmSms;
        }
        settings_fields('msm_options_group');
        do_settings_sections('msm_options_group');
        wp_nonce_field('msm', 'msm_nonce');
        ?>

        <p>
            <input type="submit" name="btn-submit" id="btn-submit" class="button button-green msm-btn-submit" value="Save this configs">

            <label for="quantity_per_batch">Quantity per batch</label>
            <select name="quantity_per_batch" id="quantity_per_batch">
                <option value="1"<?= (1 == $quantity_per_batch ? ' selected' : ''); ?>>1</option>
                <option value="2"<?= (2 == $quantity_per_batch ? ' selected' : ''); ?>>2</option>
                <option value="5"<?= (5 == $quantity_per_batch ? ' selected' : ''); ?>>5</option>
                <option value="10"<?= (10 == $quantity_per_batch ? ' selected' : ''); ?>>10</option>
                <option value="25"<?= (25 == $quantity_per_batch ? ' selected' : ''); ?>>25</option>
                <option value="50"<?= (50 == $quantity_per_batch ? ' selected' : ''); ?>>50</option>
                <option value="100"<?= (100 == $quantity_per_batch ? ' selected' : ''); ?>>100</option>
            </select>

            <label for="time_between_batches">Time between batches</label>
            <select name="time_between_batches" id="time_between_batches">
                <option value="1"<?= (1 == $time_between_batches ? ' selected' : ''); ?>>1s</option>
                <option value="2"<?= (2 == $time_between_batches ? ' selected' : ''); ?>>2s</option>
                <option value="5"<?= (5 == $time_between_batches ? ' selected' : ''); ?>>5s</option>
                <option value="10"<?= (10 == $time_between_batches ? ' selected' : ''); ?>>10s</option>
                <option value="30"<?= (30 == $time_between_batches ? ' selected' : ''); ?>>30s</option>
                <option value="60"<?= (60 == $time_between_batches ? ' selected' : ''); ?>>60s</option>
                <option value="120"<?= (120 == $time_between_batches ? ' selected' : ''); ?>>120s</option>
            </select>
        </p>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <td>Col</td>
                    <td>Col</td>
                    <td>Col</td>
                    <td>Col</td>
                    <td>Col</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($results as $key => $result) {
                ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            <?php
            }
            ?>
            </tbody>
        </table>

        <div class="msm-footer-actions-container">
            <div class="msm-footer-actions-container-left">
                <button type="button" name="msm-btn-study-site" id="msm-btn-study-site" class="button button-green button-study-site">Study Site</button>
                <span id="msm-box-study-site-status">Standby</span>
            </div>
            <div class="msm-footer-actions-container-right">
                <button type="button" name="msm-btn-reset-queue" id="msm-btn-reset-queue" class="button button-red button-reset-queue">Reset Queue</button>
                <input type="submit" name="submit-remove-all" id="submit-remove-all" class="button button-red button-remove-all" value="Remove All Data">
            </div>
        </div>

    </div>

</form>
<style>hr{margin-top: 30px;}</style>
<hr>
