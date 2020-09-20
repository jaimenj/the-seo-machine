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

        <div class="tsm-footer-actions-container">
            <div class="tsm-footer-actions-container-left">
                <button type="button" name="tsm-btn-study-site" id="tsm-btn-study-site" class="button button-green button-study-site">Study Site</button>
                <span id="tsm-box-study-site-status">Standby</span>
            </div>
            <div class="tsm-footer-actions-container-right">
                <button type="submit" name="tsm-submit-reset-queue" id="tsm-submit-reset-queue" class="button button-red button-reset-queue">Reset Queue</button>
                <button type="submit" name="tsm-submit-remove-all" id="tsm-submit-remove-all" class="button button-red button-remove-all">Remove All Data</button>
            </div>
        </div>

    </div>

</form>
<style>hr{margin-top: 30px;}</style>
<hr>
<script>
    let weAreInTheSeoMachine = true
</script>