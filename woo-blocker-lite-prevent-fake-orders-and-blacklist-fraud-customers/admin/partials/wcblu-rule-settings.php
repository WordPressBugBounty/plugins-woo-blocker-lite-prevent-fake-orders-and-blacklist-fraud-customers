<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
// Function for free plugin content
function wbclu_free_rules_settings_content() {
    ?>
    <div class="wcblu-main-table res-cl wcblu-upgrade-pro-to-unlock">
        <form id="wcblu_plugin_general_rules" method="post" action="<?php 
    esc_url( get_admin_url() );
    ?>admin-post.php" enctype="multipart/form-data" novalidate="novalidate">
            <input type='hidden' name='action' value='submit_general_rules_form_wcblu'/>
            <?php 
    wp_nonce_field( 'wcblu_save_rule_settings', 'wcblu_save_rule_settings_nonce' );
    ?>
            <div class="heading_div_rvp">
                <div class="heading_section_rvp">
                    <h2 class="no-border ds-rewanp"><?php 
    echo esc_html__( 'General Rules', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><div class="wcblu-pro-label"></div></h2>
                    <p><?php 
    echo esc_html__( 'Each rule that is matched will add the configured "Rule Weight" value to the overall fraud score. In this section you can configure general fraud detection rules.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
                </div>
            </div>
            <div class="heading_div">
                <div class="heading_section">
                    <h2><?php 
    echo esc_html__( 'First Time Purchase Rules', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/1067-first-time-purchase-rules" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                </div>
                <button type="submit" name="wcblu_gr_submit" class="button button-primary wcblu_submit" value="Save Changes"><?php 
    echo esc_html__( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
            </div>
            <table class="form-table table-outer general-rules res-cl">
                <tbody>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Check Customer\'s First Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Check if it is the customer\'s first Order.' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_first_order_status">                                    
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_first_order_weight" id="wcbfc_first_order_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Check If First Orders in Processing State', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Perform first order check again once order is in Processing state' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_first_order_custom">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_first_order_custom_weight" id="wcbfc_first_order_custom_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
    echo esc_html__( 'IP, Billing and Shipping Address-based Rules', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/1068-how-to-configure-ip-billing-and-shipping-address-based-rules" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                </div>
            </div>
            <table class="form-table table-outer general-rules res-cl">
                <tbody>
                    <?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
        echo esc_html__( 'Are Billing and Shipping Addresses Same?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'Check billing and shipping addresses are not the same.' );
        ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                        <div class="wcbfc-control-settings">
                            <label class="switch" for="wcbfc_bca_order">
                                <div class="slider round"></div>
                            </label>
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_bca_order_weight" id="wcbfc_bca_order_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                            </div>
                        </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                            <?php 
        }
        ?>
                        </td>
                    </tr>
                    <tr> 
                        <th scope="row" class="titledesc">
                            <label><?php 
        echo esc_html__( 'Enable phone number and billing country check', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'If you enable this rule, then it is highly recommended that you use a correct international phone number format on the checkout page. Else, this rule will treat an invalid phone number as a risk. For example: (+1) 111 1111' );
        ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_billing_phone_number_order">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_billing_phone_number_order_weight" id="wcbfc_billing_phone_number_order_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                            <?php 
        }
        ?>
                        </td>
                    </tr>
                    <tr> 
                        <th scope="row" class="titledesc">
                            <label><?php 
        echo esc_html__( 'Does Geo Location Match?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'If you enable this rule, It will check if the customer\'s shipping or billing state matches the Geo Location state.' );
        ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_billing_shipping_geo_match">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_billing_shipping_geo_match_weight" id="wcbfc_billing_shipping_geo_match_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                </div>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_geo_match_key" id="wcbfc_geo_match_key" type="text" style="width: 100%;" value="" class="wcbfc_geo_match_key" placeholder="API Key for bigdatacloud.com">
                                    <p class="wcbfc-short-info">
                                        <?php 
        esc_html_e( 'No BigDataCloud account?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?> 
                                        <a href="<?php 
        echo esc_url( 'https://www.bigdatacloud.com/' );
        ?>" target="_blank">
                                            <?php 
        esc_html_e( 'Sign up here', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                        </a> – 
                                        <?php 
        esc_html_e( 'Get 50K free requests/month, upgrade anytime.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                            <?php 
        }
        ?>
                        </td>
                    </tr>
                    <?php 
    }
    ?>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Check Customer behind Proxy or VPN', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Check if the customer is behind either a proxy or a VPN' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_proxy_order">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_proxy_order_weight" id="wcbfc_proxy_order_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights  " placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
            <?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
                <div class="heading_div">
                    <div class="heading_section">
                        <h2><?php 
        echo esc_html__( 'Multiple Orders Attempts using Different Addresses from Same IP ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?><a href="https://docs.thedotstore.com/article/1069-detecting-multiple-order-attempts-using-different-addresses-from-the-same-ip" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                    </div>
                </div>
                <table class="form-table table-outer general-rules res-cl">
                    <tbody>
                        <tr>
                            <th scope="row" class="titledesc">
                                <label><?php 
        echo esc_html__( 'Same IP but Different Customer Addresses', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                    <div class="wcbfc-tooltip-rules">
                                        <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'Check if multiple orders with different billing or shipping addresses have originated from the same IP address' );
        ?></span>
                                    </div>
                                </label>
                            </th>
                            <td>
                                <div class="wcbfc-control-settings"> 
                                    <label class="switch" for="wcbfc_ip_multiple_check">
                                        <div class="slider round"></div>
                                    </label>
                                    <div class="wcblu_rule_field">
                                        <input name="wcbfc_ip_multiple_weight" id="wcbfc_ip_multiple_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                        <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                    </div>
                                    <div class="wcblu_rule_field">
                                        <input name="wcbfc_ip_multiple_time_span" id="wcbfc_ip_multiple_time_span" type="number" style="width: 5em;" value="30" class="" placeholder="" min="0" step="1">
                                        <label><?php 
        echo esc_html__( 'Days to check past IP and physical addresses', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                    </div>
                                </div>
                                <div class="wcbfc-control-points">
                                    <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                                <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                    <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                                <?php 
        }
        ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php 
    }
    ?>
            <?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
                <div class="heading_div">
                    <div class="heading_section">
                        <h2><?php 
        echo esc_html__( 'Origin Countries', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?><a href="https://docs.thedotstore.com/article/1070-how-to-configure-origin-countries-rules" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                    </div>
                </div>
                <table class="form-table table-outer general-rules res-cl">
                    <tbody>
                        <tr>
                            <th scope="row" class="titledesc">
                                <label><?php 
        echo esc_html__( 'Is International Order?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                    <div class="wcbfc-tooltip-rules">
                                        <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'Check if the order originates from outside of your store\'s home country.' );
        ?></span>
                                    </div>
                                </label>
                            </th>
                            <td>
                                <div class="wcbfc-control-settings">
                                    <label class="switch" for="wcbfc_international_order">
                                        <div class="slider round"></div>
                                    </label>
                                    <div class="wcblu_rule_field">
                                        <input name="wcbfc_international_order_weight" id="wcbfc_international_order_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                        <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                    </div>
                                </div>
                                <div class="wcbfc-control-points">
                                    <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                                <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                    <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                                <?php 
        }
        ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="titledesc">
                                <label><?php 
        echo esc_html__( 'Is order from high-risk country?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
                                    <div class="wcbfc-tooltip-rules">
                                        <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
        echo esc_attr( 'Check if order originates from any country in the high-risk countries list below:' );
        ?></span>
                                    </div>
                                </label>
                            </th>
                            <td>
                                <div class="wcbfc-control-settings">
                                    <label class="switch" for="wcbfc_unsafe_countries">
                                        <div class="slider round"></div>
                                    </label>
                                    <div class="wcblu_rule_field">
                                        <input name="wcbfc_unsafe_countries_weight" id="wcbfc_unsafe_countries_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                        <label class="wcbfc-rule-weight-label"><?php 
        echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                    </div>
                                </div>
                                <div class="wcbfc-control-points">
                                    <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                                
                                <div class="wcblu_rule_field">
                                    <select name="wcblu_define_unsafe_countries_list[]" id="wcblu_define_unsafe_countries_list" multiple class="chosen-ducl-select-countries chosen-rtl">
                                        <option value="AF" selected="selected">Afghanistan</option>    
                                    </select>
                                </div>
                                <div class="wcblu_rule_field">
                                    <input type="checkbox" id="wcbfc_unsafe_countries_ip" name="wcbfc_unsafe_countries_ip" value="0">
                                    <label for="wcbfc_unsafe_countries_ip"><?php 
        echo esc_html__( 'Check high-risk countries using user IP.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
                                </div>
                                <?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
                                    <p class="wcbfc-pl-compatiblity-notice"><span class="dashicons dashicons-warning" style="color:#d0a823;"></span><?php 
            echo esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                                <?php 
        }
        ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php 
    }
    ?>
            <div class="heading_div">
                <div class="heading_section">
                    <h2><?php 
    echo esc_html__( 'High-Risk Email Domains', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/1071-how-to-configure-the-high-risk-email-domain-rule" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                </div>
            </div>
            <table class="form-table table-outer general-rules res-cl">
                <tbody>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Is suspicious email domain', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Check if customer\'s email address originates from any high-risk domain listed below' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_suspecius_email">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_suspecious_email_weight" id="wcbfc_suspecious_email_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <div class="wcblu_rule_field">
                                <select id="wcbfc_suspecius_email_list"
                                data-placeholder="<?php 
    esc_attr_e( 'Add emails separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
                                name="wcbfc_suspecius_email_list[]" multiple="true"
                                class="chosen-ss-select-email category-select chosen-rtl">
                                    <option value=""></option>
                                    <option value="hotmail" selected="" data-select2-id="7">hotmail</option>
                                    <option value="live" selected="" data-select2-id="8">live</option>
                                    <option value="gmail" selected="" data-select2-id="9">gmail</option>
                                    <option value="yahoo" selected="" data-select2-id="10">yahoo</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="heading_div">
                <div class="heading_section">
                    <h2><?php 
    echo esc_html__( 'Order Amounts and Attempts ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?><a href="https://docs.thedotstore.com/article/1072-how-to-configure-order-amounts-and-attempts-rules" class="main-title-tooltip" target="_blank"><span class="dashicons dashicons-info"></span></a></h2>
                </div>
            </div>
            <table class="form-table table-outer general-rules res-cl">
                <tbody>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Order amount above average?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Check if order significantly exceeds the average order amount for your site' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_order_avg_amount_check">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_order_avg_amount_weight" id="wcbfc_order_avg_amount_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_order_avg_amount_time_span" id="wcbfc_order_avg_amount_time_span" type="number" style="width: 5em;" value="3" class="" placeholder="" min="0" step="1">
                                <label><?php 
    echo esc_html__( 'The amount over the average transaction value that will trigger the rule (expressed as a multiplier).', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Order exceeds maximum', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Confirm the total amount of the order does not exceed the maxmimum configured below' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_order_amount_check">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_max_order_attempt_weight" id="wcbfc_max_order_attempt_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_max_order_attempt_span" id="wcbfc_max_order_attempt_span" type="number" style="width: 5em;" value="500" class="" placeholder="" min="0" >
                                <?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        $currency = get_woocommerce_currency_symbol();
    } else {
        $currency = edd_currency_symbol();
    }
    ?>
                                <label><?php 
    echo esc_html__( 'Order amount limit (' . $currency . ') - Total maximum order amount accepted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Too many order attempts?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Check if customer attempts to make a purchase too many times within the time period configured below' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_too_many_oa_check">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_too_many_oa_attempt_weight" id="wcbfc_too_many_oa_attempt_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value=""></progress>
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
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_too_many_oats_attempt_span" id="wcbfc_too_many_oats_attempt_span" type="number" style="width: 5em;" value="24" class="" placeholder="" min="0" >
                                <label><?php 
    echo esc_html__( 'Time span (hours) to check', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_too_many_oatos_attempt_span" id="wcbfc_too_many_oatos_attempt_span" type="number" style="width: 5em;" value="3" class="" placeholder="" min="0" >
                                <label><?php 
    echo esc_html__( 'Maximum number of orders that a user can make in the specified time span', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Multiple Failed order attempts?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Block users from placing orders after multiple failed credit card attempts during a single session. The system tracks failed attempts through order notes and automatically restricts further checkout attempts if the limit is exceeded.' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_too_many_o_failed_a_check">
                                    <input checked type="checkbox" id="wcbfc_too_many_o_failed_a_check" name="wcbfc_too_many_o_failed_a_check" value="">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_too_many_o_failed_a_check_weight" id="wcbfc_too_many_o_failed_a_check_weight" type="number" style="width: 5em;" value="0" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
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
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_too_many_failed_oats_attempt_try" id="wcbfc_too_many_failed_oats_attempt_try" type="number" style="width: 5em;" value="2" class="" placeholder="" min="0" >
                                <label><?php 
    echo esc_html__( 'Allow Number of Attempts', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                            <div class="wcblu_rule_field">
                                <textarea rows="4" name="wcbfc_too_many_failed_oats_strings" id="wcbfc_too_many_failed_oats_strings" cols="50" style="width:100%; height: 100px;" placeholder="Enter failed attempt message or keywords from order notes to block..."></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="titledesc">
                            <label><?php 
    echo esc_html__( 'Limit Number of Orders between Time', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
                                <div class="wcbfc-tooltip-rules">
                                    <span class="wcbfc-tooltiptext wcbfc-tooltip-bottom"><?php 
    echo esc_attr( 'Turning on this rule restricts the number of orders that can be placed within a specific time frame each day.' );
    ?></span>
                                </div>
                            </label>
                        </th>
                        <td>
                            <div class="wcbfc-control-settings">
                                <label class="switch" for="wcbfc_no_of_allow_order_between_time">
                                    <input type="checkbox" id="wcbfc_no_of_allow_order_between_time" name="wcbfc_no_of_allow_order_between_time" value="">
                                    <div class="slider round"></div>
                                </label>
                                <div class="wcblu_rule_field">
                                    <input name="wcbfc_no_of_allow_order_between_time_weight" id="wcbfc_no_of_allow_order_between_time_weight" type="number" style="width: 5em;" value="40" class="wcbfc_rules_weights" placeholder="" min="0" step="1" max="100">
                                    <label class="wcbfc-rule-weight-label"><?php 
    echo esc_html__( 'Rule Weight', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                                </div>
                            </div>
                            <div class="wcbfc-control-points">
                                <progress max="100" class="wcbfc-progressBar" value="15"></progress>
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
                            <div class="wcblu_rule_field">
                                <input name="wcbfc_maximum_allowed_number_of_orders_between_time" id="wcbfc_maximum_allowed_number_of_orders_between_time" type="number" style="width: 5em;" value="2" class="" placeholder="" min="0" >
                                <label><?php 
    echo esc_html__( 'Maximum Allowed Number of Orders Between Time', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                            <div class="wcblu_rule_field">
                                <input id="wcbfc_start_time_timepicker" name="wcbfc_start_time_timepicker" type="text" placeholder="Select Date.." data-id="timePicker" readonly="readonly" value="">
                                <label><?php 
    echo esc_html__( 'Start Time', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                            <div class="wcblu_rule_field">
                                <input id="wcbfc_end_time_timepicker" name="wcbfc_end_time_timepicker" type="text" placeholder="Select Date.." data-id="timePicker" readonly="readonly" value="">
                                <label><?php 
    echo esc_html__( 'End Time', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
                            </div>
                            <div class="wcblu_rule_field">
                                <?php 
    $time_zone = new \DateTimeZone(wp_timezone_string());
    $now = new \DateTime('NOW', $time_zone);
    ?>
                                <span><?php 
    echo esc_html__( 'Current Time: ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . esc_attr( $now->format( 'H:i:s' ) );
    ?></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>
                <button type="submit" name="wcblu_gr_submit" class="button button-primary" value="Save Changes"><?php 
    echo esc_html__( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
            </p>
        </form>
    </div>
    <?php 
}

wbclu_free_rules_settings_content();
?>
</div>
</div>
</div>
</div>