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

        <h1><span class="dashicons dashicons-location-alt"></span> My SEO Machine</h1>
        
        <?php
        if (isset($msmSms)) {
            echo $msmSms;
        }
        ?>

        <?php settings_fields('msm_options_group'); ?>
        <?php do_settings_sections('msm_options_group'); ?>

        <?php wp_nonce_field('msm', 'msm_nonce'); ?>

        <p>
            <input type="submit" name="btn-submit" id="btn-submit" class="button button-green" value="Save this configs">

            <label for="items_per_page">Items per page</label>
            <select name="items_per_page" id="items_per_page">
                <option value="10"<?= (10 == $items_per_page ? ' selected' : ''); ?>>10</option>
                <option value="20"<?= (20 == $items_per_page ? ' selected' : ''); ?>>20</option>
                <option value="50"<?= (50 == $items_per_page ? ' selected' : ''); ?>>50</option>
                <option value="100"<?= (100 == $items_per_page ? ' selected' : ''); ?>>100</option>
                <option value="250"<?= (250 == $items_per_page ? ' selected' : ''); ?>>250</option>
                <option value="500"<?= (500 == $items_per_page ? ' selected' : ''); ?>>500</option>
                <option value="1000"<?= (1000 == $items_per_page ? ' selected' : ''); ?>>1000</option>
            </select>

            <label for="report_email">Report email</label>
            <input type="text" name="report_email" id="report_email" class="regular-text" value="<?= $report_email; ?>">
            <input type="submit" name="submit-check-email" id="submit-check-email" class="button button-green" value="Check email">

            <span class="span-pagination"><?php

            if ($current_page > 1) {
                ?>
                <input type="submit" name="submit-previous-page" id="submit-previous-page" class="button button-primary" value="<<">
                <?php
            }

            ?>
            <a href="<?= admin_url('tools.php?page=my-seo-machine'); ?>">Page <?= $current_page; ?> with total <?= $total_registers; ?> items</a>
            <?php

            if ($current_page * $items_per_page < $total_registers) {
                ?>
                <input type="submit" name="submit-next-page" id="submit-next-page" class="button button-primary" value=">>">
                <?php
            }

            ?>
            </span>
            <input type="hidden" name="current-page" id="current-page" value="<?= $current_page; ?>">
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

        <p>
            <input type="submit" name="submit-remove-all" id="submit-remove-all" class="button" value="Remove all records">
        </p>
        
    </div>

</form>
<style>hr{margin-top: 30px;}</style>
<hr>
