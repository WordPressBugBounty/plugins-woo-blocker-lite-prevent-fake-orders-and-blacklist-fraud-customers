<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
// Function for free plugin content
function wbclu_free_general_settings_content() {
    $getplugingeneralopt = get_option( 'wcblu_general_option' );
    if ( isset( $getplugingeneralopt ) && !empty( $getplugingeneralopt ) ) {
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcbfc_recaptcha_status = ( !empty( $getplugingeneraloptarray['wcbfc_recaptcha_status'] ) ? $getplugingeneraloptarray['wcbfc_recaptcha_status'] : 'off' );
        $wcbfc_recaptcha_version = ( !empty( $getplugingeneraloptarray['wcbfc_recaptcha_version'] ) ? $getplugingeneraloptarray['wcbfc_recaptcha_version'] : 'v2' );
        $wcblu_v2_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v2_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v2_keys_value'] : '' );
        $wcblu_v3_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v3_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v3_keys_value'] : '' );
        $wcblu_v2_secret_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v2_secret_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v2_secret_keys_value'] : '' );
        $wcblu_v3_secret_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v3_secret_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v3_secret_keys_value'] : '' );
    } else {
        $wcbfc_recaptcha_status = 'off';
        $wcbfc_recaptcha_version = '';
        $wcblu_v2_keys_value = '';
        $wcblu_v3_keys_value = '';
        $wcblu_v2_secret_keys_value = '';
        $wcblu_v3_secret_keys_value = '';
    }
    $success_note = filter_input( INPUT_GET, 'success', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $wcbfc_status = '';
    ?>
    <div class="wcblu-main-table res-cl">
            <?php 
    if ( !empty( $success_note ) ) {
        ?>
                <div id="message" class="updated notice is-dismissible"><p><?php 
        esc_html_e( 'Data has been updated.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p></div>
            <?php 
    }
    ?>
            <div class="heading_div_rvp">
                <div class="heading_section_rvp">
                    <h2 class="no-border ds-rewanp"><?php 
    echo esc_html__( 'General Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></h2>
                    <p><?php 
    echo esc_html__( 'Set up the automatic blocking options for the Fraud Prevention plugin on this page.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
                </div>
            </div>
            <form id="wcblu_plugin_general_settings" method="post" action="<?php 
    esc_url( get_admin_url() );
    ?>admin-post.php" enctype="multipart/form-data">
                <input type='hidden' name='action' value='submit_general_setting_form_wcblu'/>
                <?php 
    wp_nonce_field( 'wcblu_plugin_general_settings', 'wcblu_plugin_general_settings_nonce' );
    ?>
                <div class="wcblu-upgrade-pro-to-unlock">
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Enable Automatic Fraud Check', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/514-how-to-enable-auto-fraud-score-calculation-in-your-woocommerce-store" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                        <button type="submit" name="wcblu_gs_submit" class="button button-primary wcblu_submit" value="Save Changes"><?php 
    echo esc_html__( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label><?php 
    echo esc_html__( 'Fraud Score Check', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                                <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'If this is enabled, the fraud check will automatically check for all rules and general settings, count the score and prevent the fraud orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                            </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="wcbfc_fraud_check_status" value="on"  checked>
                                        <div class="slider round"></div>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Pre-Purchase Assessment', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/1066-prevent-order-placement-for-high-risk-fraud-scores-at-checkout" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label><?php 
    echo esc_html__( 'Before Payment Checking', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'If this is enabled, this option will prevent the customer to place an order at the checkout page if the fraud score is equal to or higher than a high risk score.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                <label class="switch">
                                    <input type="checkbox" name="wcbfc_fraud_check_before_pay" value="on"  checked>
                                    <div class="slider round"></div>
                                </label>
                                    <label for="wcbfc_fraud_check_before_pay"><?php 
    echo esc_html__( 'Fraud check before payment', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </td>
                            </tr>
                            <tr class="" data-label="<?php 
    echo esc_attr( 'wcbfc_fraud_check_before_pay' );
    ?>">
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Message for Blocked users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Customize the message displayed when a fraud check is triggered and block users at checkout', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <textarea name="wcblu_pre_payment_message" id="wcblu_pre_payment_message" style="width:100%; height: 100px;" class="" placeholder="" spellcheck="false"><?php 
    echo esc_html__( 'Website Administrator does not allow you to place this order. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Change Order Status based on Risk Score', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/519-how-to-cancel-orders-or-put-them-on-hold-based-on-fraud-score" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label><?php 
    echo esc_html__( 'Enable Update Order Status', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'If this is enabled, the order status will be updated as score value\'s status.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                    <input checked type="checkbox" id="wcbfc_update_order_status_on_score" name="wcbfc_update_order_status_on_score" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                    <label for="wcbfc_update_order_status_on_score"><?php 
    echo esc_html__( 'Update order status according the order score.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </td>
                            </tr>
                            <tr class="wcbfc_prgrs_exist" data-label="<?php 
    echo esc_attr( 'wcbfc_update_order_status_on_score' );
    ?>">
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Cancel Score', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Orders with a score equal to or greater than this number will be automatically cancelled. Select 0 to disable.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <div class="wcbfc-control-settings">
                                        <select name="wcbfc_settings_cancel_score" id="wcbfc_settings_cancel_score" style="width: 5em;" class="">
                                            <option value="0">0</option>
                                        </select>
                                    </div>
                                    <div class="wcbfc-control-points">
                                        <progress max="100" class="wcbfc-progressBar" value="0"></progress>
                                        <span class="wcbfc-tooltip progress-tooltip">0</span>
                                        <div class="progress-container">
                                            <div class="progress-bar">
                                                <div class="segment good">
                                                    <span class="text"><?php 
    echo esc_html__( 'No Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment average">
                                                    <span class="text"><?php 
    echo esc_html__( 'Moderate', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment poor">
                                                    <span class="text"><?php 
    echo esc_html__( 'High Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class=" wcbfc_prgrs_exist" data-label="<?php 
    echo esc_attr( 'wcbfc_update_order_status_on_score' );
    ?>">
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'On-Hold Score', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Orders with a score equal to or greater than this number will be automatically set on hold. Select 0 to disable.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <div class="wcbfc-control-settings">
                                        <select name="wcbfc_settings_hold_score" id="wcbfc_settings_hold_score" style="width: 5em;" class="">
                                        <option selected value="0">0</option>
                                        </select>
                                    </div>
                                    <div class="wcbfc-control-points">
                                        <progress max="100" class="wcbfc-progressBar" value="0"></progress>
                                        <span class="wcbfc-tooltip progress-tooltip">0</span>
                                        <div class="progress-container">
                                            <div class="progress-bar">
                                                <div class="segment good">
                                                    <span class="text"><?php 
    echo esc_html__( 'No Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment average">
                                                    <span class="text"><?php 
    echo esc_html__( 'Moderate', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment poor">
                                                    <span class="text"><?php 
    echo esc_html__( 'High Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Set Risk Thresholds', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/515-how-to-set-risk-thresholds-to-low-medium-or-high" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label><?php 
    echo esc_html__( 'Set Risk Thresholds', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'With above setting you may set the risk threadhold ( Low, Medium, High ) between 0-100.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <div class="wcbfc-control-settings">
                                        <div class="wcblu_field wcblu_align_text">
                                            <input name="wcbfc_settings_low_risk_threshold" id="wcbfc_settings_low_risk_threshold" type="number" style="width: 5em;" value="20" class="" placeholder="" min="0" step="1" max="100">
                                            <label for="wcbfc_settings_low_risk_threshold"><?php 
    echo esc_html__( 'Medium Risk threshold', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                        </div>
                                        <div class="wcblu_field wcblu_align_text">
                                            <input name="wcbfc_settings_high_risk_threshold" id="wcbfc_settings_high_risk_threshold" type="number" style="width: 5em;" value="60" class="" placeholder="" min="" step="1" max="100">
                                            <label for="wcbfc_settings_high_risk_threshold"><?php 
    echo esc_html__( 'High Risk threshold', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="wcbfc-control-points">
                                        <progress max="100" class="wcbfc-progressBar medium" value="20"></progress>
                                        <progress max="100" class="wcbfc-progressBar high" value="60"></progress>
                                        <span class="wcbfc-tooltip progress-tooltip medium">0</span>
                                        <span class="wcbfc-tooltip progress-tooltip high">0</span>
                                        <div class="progress-container">
                                            <div class="progress-bar">
                                                <div class="segment good">
                                                    <span class="text"><?php 
    echo esc_html__( 'No Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment average">
                                                    <span class="text"><?php 
    echo esc_html__( 'Moderate', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment poor">
                                                    <span class="text"><?php 
    echo esc_html__( 'High Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td> 
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Email Alerts', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/520-how-to-send-email-notification-to-admin-for-high-risk-orders" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label><?php 
    echo esc_html__( 'Send Admin Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Send a notification mail to the site admin showing fraud score checks.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input checked type="checkbox" id="wcbfc_email_notification" name="wcbfc_email_notification" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                    <label for="wcbfc_email_notification"><?php 
    echo esc_html__( 'Admin Email Alert Activation', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </td>
                            </tr>
                            <tr class="wcbfc_prgrs_exist" data-label="<?php 
    echo esc_attr( 'wcbfc_email_notification' );
    ?>">
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Email Notification Score', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'An admin email notification will be sent if an orders scores equal to or greater than this number. Select 0 to disable.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <div class="wcbfc-control-settings">
                                        <select name="wcbfc_settings_email_score" id="wcbfc_settings_email_score" style="width: 5em;" class="">
                                            <option selected value="0">0</option>
                                        </select>
                                    </div>
                                    <div class="wcbfc-control-points">
                                        <progress max="100" class="wcbfc-progressBar" value="0"></progress>
                                        <span class="wcbfc-tooltip progress-tooltip">0</span>
                                        <div class="progress-container">
                                            <div class="progress-bar">
                                                <div class="segment good">
                                                    <span class="text"><?php 
    echo esc_html__( 'No Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment average">
                                                    <span class="text"><?php 
    echo esc_html__( 'Moderate', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                                <div class="segment poor">
                                                    <span class="text"><?php 
    echo esc_html__( 'High Importance', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="" data-label="<?php 
    echo esc_attr( 'wcbfc_email_notification' );
    ?>">
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Additional Recipients', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'To send email notifications to additional addresses, enter them, separated by commas, above.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <input name="wcblu_settings_custom_email" id="wcblu_settings_custom_email" type="text" style="width: 70%;" value="0" class="" placeholder="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Whitelist Options: Email, Payment Method, Role, and IP Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/516-how-to-do-special-email-whitelisting-using-fraud-prevention-plugin" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Email Whitelist', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Email addresses listed above will not be subject to fraud checks. Enter one email address per line.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <textarea name="wcblu_settings_whitelist" id="wcblu_settings_whitelist" style="width:100%; height: 100px;" class="" placeholder="">dummy@dummy.com</textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Whitelist Payment Method', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Selected payment methods will not be considered for Cancel Order Score and On-Hold Order Score evaluation for each order.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input checked type="checkbox" id="wcbfc_enable_whitelist_payment_method" name="wcbfc_enable_whitelist_payment_method" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                    <label for="wcbfc_enable_whitelist_payment_method"><?php 
    echo esc_html__( 'Enable Payment Method Whitelisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                    <div class="wcblu_rule_field" style="display:">
                                        <select name="wcblu_whitelist_payment_method[]" id="wcblu_whitelist_payment_method" multiple data-placeholder="Select payment methods">
                                            <option value="cod" selected>Cash on Delivery</option>
                                            <option value="bacs" selected>Direct Bank Transfer</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'User Roles Whitelisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Selected User Roles will not be considered for Cancel Order Score and On-Hold Order Score evaluation for each order.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input checked type="checkbox" id="wcbfc_enable_whitelist_user_roles" name="wcbfc_enable_whitelist_user_roles" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                    <label for="wcbfc_enable_whitelist_user_roles"><?php 
    echo esc_html__( 'Enable User Roles Whitelisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                    <div class="wcblu_rule_field">
                                        <select name="wcblu_whitelist_user_roles[]" id="wcblu_whitelist_user_roles" multiple data-placeholder="Select user roles">
                                            <option value="administrator" selected>Administrator</option>
                                            <option value="shop_manager" selected>Shop Manager</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'IP Whitelisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'IP addresses listed above will not be subject to fraud checks. Enter one IP address per line.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" id="wcbfc_enable_whitelist_ips" name="wcbfc_enable_whitelist_ips" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                    <label for="wcbfc_enable_whitelist_ips"><?php 
    echo esc_html__( 'Enable IP Whitelisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                    <div class="wcblu_rule_field">
                                        <textarea name="wcblu_settings_whitelist_ips" id="wcblu_settings_whitelist_ips" style="width:100%; height: 100px;" class="" placeholder="">0.0.0.0</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="heading_div">
                        <div class="heading_section">
                            <h2><?php 
    echo esc_html__( 'Secure Checkout and Payment Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="#" class="main-title-tooltip" target="_blank"></a> <div class="wcblu-pro-label"></div></h2>
                        </div>
                    </div>
                    <table class="form-table table-outer general-settings res-cl">
                        <tbody>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Block COD for Fraudulent Users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Disable Cash on Delivery for high-risk orders while allowing other payment options.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input checked type="checkbox" id="wcbfc_cod_block_status" name="wcbfc_cod_block_status" value="" checked>
                                        <div class="slider round"></div>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="titledesc">
                                    <label> <?php 
    echo esc_html__( 'Checkout Lock on Failed Orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        <div class="wcbfc-tooltip-rules">
                                            <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_html__( 'Maximum failed checkouts allowed before checkout is locked. Helps block bots.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                                        </div>
                                    </label>
                                </th>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" id="wcbfc_cbf_status" name="wcbfc_cbf_status" value="" checked>
                                        <div class="slider round"></div>
                                    </label>

                                    <div class="wcblu_checkout_payment_settings <?php 
    echo esc_attr( $wcbfc_status );
    ?>">
                                        <div class="wcblu_rule_field">
                                            <input type="number" placeholder="" name="wcbfc_cbf_attempts" id="wcbfc_cbf_attempts" value="3" min="1" max="1000" step="1"> <?php 
    echo esc_html__( 'Enter the allowed number of failed attempts', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        </div>
                                        <div class="wcblu_rule_field">
                                            <input type="number" placeholder="" name="wcbfc_cbf_time" id="wcbfc_cbf_time" value="60" min="1" max="6000" step="1"> <?php 
    echo esc_html__( 'Time frame (in minutes) to track failed orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php 
    if ( class_exists( 'WooCommerce' ) ) {
        ?>
                <div class="heading_div">
                    <div class="heading_section">
                        <h2><?php 
        echo esc_html__( 'Google reCAPTCHA Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?><a href="https://docs.thedotstore.com/article/1057-how-to-enhance-security-with-recaptcha-to-protect-your-checkout-from-bots" class="main-title-tooltip" target="_blank"></a></h2>
                    </div>
                </div>
                <table class="form-table table-outer general-settings res-cl">
                    <tbody>
                        <tr>
                            <th scope="row" class="titledesc">
                                <label> <?php 
        echo esc_html__( 'Enable reCAPTCHA', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                    <div class="wcbfc-tooltip-rules">
                                        <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_html__( 'Enable reCAPTCHA on checkout page', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></span>
                                    </div>
                                </label>
                            </th>
                            <td>
                                <label class="switch">
                                    <?php 
        $wcbfc_status = '';
        if ( empty( $wcbfc_recaptcha_status ) && '' === $wcbfc_recaptcha_status ) {
            ?>
                                        <input checked type="checkbox" id="wcbfc_recaptcha_status"
                                            name="wcbfc_recaptcha_status" value="">
                                        <?php 
        } else {
            ?>
                                        <input <?php 
            if ( !empty( $wcbfc_recaptcha_status ) && '1' === $wcbfc_recaptcha_status ) {
                ?> checked <?php 
            }
            ?>
                                            type="checkbox" id="wcbfc_recaptcha_status"
                                            name="wcbfc_recaptcha_status" value="<?php 
            if ( !empty( $wcbfc_recaptcha_status ) && '1' === $wcbfc_recaptcha_status ) {
                echo "1";
                $wcbfc_status = 'show_it';
            } else {
                echo "0";
                $wcbfc_status = 'hide_it';
            }
            ?>">
                                        <?php 
        }
        ?>
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_captcha_settings <?php 
        echo esc_attr( $wcbfc_status );
        ?>">
                                    <div class="wcblu_rule_field">
                                        <select name="wcbfc_recaptcha_version" id="wcbfc_recaptcha_version" class="">
                                            <option value=""><?php 
        echo esc_html__( 'Select Captcha Version', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></option>
                                            <option value="wcblu_v2_keys" <?php 
        selected( $wcbfc_recaptcha_version, 'wcblu_v2_keys' );
        ?>>
                                                <?php 
        echo esc_html__( 'v2 reCAPTCHA', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                            </option>
                                            <option value="wcblu_v3_keys" <?php 
        selected( $wcbfc_recaptcha_version, 'wcblu_v3_keys' );
        ?>>
                                                <?php 
        echo esc_html__( 'v3 reCAPTCHA', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="wcblu_v2_keys wcblu_versions_key" style="display:<?php 
        echo ( 'wcblu_v2_keys' === $wcbfc_recaptcha_version ? 'block' : 'none' );
        ?>;">
                                        <div class="wcblu_rule_field"><input type="text" name="wcblu_v2_keys_value" placeholder="Enter v2 Site Key" value="<?php 
        echo esc_attr( $wcblu_v2_keys_value );
        ?>"></div>
                                        <div class="wcblu_rule_field"><input type="text" name="wcblu_v2_secret_keys_value" placeholder="Enter v2 Secret Key" value="<?php 
        echo esc_attr( $wcblu_v2_secret_keys_value );
        ?>"></div>
                                    </div>
                                    <div class="wcblu_v3_keys wcblu_versions_key" style="display:<?php 
        echo ( 'wcblu_v3_keys' === $wcbfc_recaptcha_version ? 'block' : 'none' );
        ?>;">
                                        <div class="wcblu_rule_field"><input type="text" name="wcblu_v3_keys_value" placeholder="Enter v3 Site Key" value="<?php 
        echo esc_attr( $wcblu_v3_keys_value );
        ?>"></div>
                                        <div class="wcblu_rule_field"><input type="text" name="wcblu_v3_secret_keys_value" placeholder="Enter v3 Secret Key" value="<?php 
        echo esc_attr( $wcblu_v3_secret_keys_value );
        ?>"></div>
                                        <p class="wcbfc-pl-compatiblity-notice" style="margin-top: 12px;"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
        echo esc_html_e( ' This feature will only works with classic checkout.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
                                    </div>
                                <div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php 
    }
    ?>
                <p>
                    <button type="submit" name="wcblu_gs_submit" class="button button-primary" value="Save Changes"><?php 
    echo esc_html__( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
                </p>
            </form>
        </div>
    <?php 
}

wbclu_free_general_settings_content();
?>

</div>
</div>
</div>
</div>