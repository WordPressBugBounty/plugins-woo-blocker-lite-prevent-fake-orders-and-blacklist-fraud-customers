<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// Get plugin discount details
$plugin_slug = 'fraud_prevention';
$get_discounts_api = WB_STORE_URL . 'wp-json/dots-upgrade-plugin-discounts/v2/dots-upgrade-plugin-discounts?' . wp_rand();
$get_discounts = wp_remote_get( $get_discounts_api );  //phpcs:ignore
$discount_number = 0;
$discount_coupon = '';

if ( ! is_wp_error( $get_discounts ) && ( 200 === wp_remote_retrieve_response_code( $get_discounts ) ) ) {
    $get_discounts_body = wp_remote_retrieve_body($get_discounts);
    $plugin_discounts = json_decode( $get_discounts_body, true );

    $plugin_discount = isset( $plugin_discounts['md_discounts_rules'] ) && ! empty( $plugin_discounts['md_discounts_rules'] ) ? $plugin_discounts['md_discounts_rules'] : array();

    $final_discount = array();
    if ( isset( $plugin_discount ) && ! empty( $plugin_discount ) ) {
        foreach ( $plugin_discount as $discount ) {
            $check_plugins = isset( $discount['plugins'] ) && ! empty( $discount['plugins'] ) ? $discount['plugins'] : array();
            if ( in_array( $plugin_slug, $check_plugins, true ) || in_array( 'all', $check_plugins, true ) ) {
                $final_discount = $discount;
            }
        }
    }
    
    $discount_number = isset( $final_discount['discount'] ) ? $final_discount['discount'] : '';
    $discount_coupon = isset( $final_discount['coupon'] ) ? $final_discount['coupon'] : '';
}
?>
<!-- Upgrade to pro plugin popup -->
<input type="hidden" class="upgrade-to-pro-discount-code" value="<?php echo esc_attr( $discount_coupon ); ?>" >
<div class="upgrade-to-pro-modal-main">
    <div class="upgrade-to-pro-modal-outer">
        <div class="pro-modal-inner">
            <div class="pro-modal-wrapper">
                <div class="pro-modal-header">
                    <img src="<?php echo esc_url( WB_PLUGIN_URL . 'admin/images/premium-upgrade-img/upgrade-rocket-img.png' ); ?>" alt="<?php esc_attr_e( 'Upgrade to Pro', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?>">
                    <span class="dashicons dashicons-no-alt modal-close-btn"></span>
                </div>
                <div class="pro-modal-body">
                    <?php 
                    if ( ! empty( $discount_number ) ) {
                        ?>
                        <h3 class="pro-feature-title"><?php echo sprintf( esc_html__( 'Unlock Premium Features with a %s%% Discount!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), esc_html( $discount_number ) ); ?></h3>
                        <?php
                    } else {
                        ?>
                        <h3 class="pro-feature-title"><?php echo esc_html__( 'Unlock Premium Features Today!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
                        <?php
                    }
                    ?>
                    <p><?php esc_html_e( 'Unlock a world of possibilities for your WooCommerce store with our Premium Fraud Prevention plugin!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
                    <ul class="pro-feature-list">
                        <li><?php esc_html_e( 'Automated Fraud Check and Blacklisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></li>
                        <li><?php esc_html_e( 'Customizable Fraud Rules & Scoring', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></li>
                        <li><?php esc_html_e( 'Block Users with Multiple Activities', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></li>
                        <li><?php esc_html_e( 'Detailed Dashboard Fraud Order Reports', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></li>
                        <li><?php esc_html_e( 'Preventing Fake Account Creation by Multiple Activities', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></li>
                    </ul>
                </div>
                <div class="pro-modal-footer">
                    <a class="pro-feature-trial-btn upgrade-now" target="_blank" href="javascript:void(0);"><?php esc_html_e( 'Upgrade Now', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
