<?php

/**
 * Handles plugin user dashboard for EDD
 * @package Dots_Edd
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
// Function for free plugin content
function edd_wbclu_free_fraud_data_dashboard_content() {
    ?>
        <!-- Dashboard HTML start -->
        <div class="wcblu-section-full wcblu-upgrade-pro-to-unlock">
            <div class="wcblu-grid-layout">
                <div class="wcblu-card wcblu-main-chart detected-order" style="grid-column: span 3 / auto;">
                    <div class="content">
                        <div class="wcblu-mini-chart">
                            <div class="header">
                                <div class="title"><?php 
    esc_html_e( 'Orders Detected 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            </div>
                            <div class="content">
                                <div class="amount"><?php 
    echo esc_html( '0' );
    ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcblu-card wcblu-main-chart low-risk" style="grid-column: span 3 / auto;">
                    <div class="content">
                        <div class="wcblu-mini-chart">
                            <div class="header">
                                <div class="title"><?php 
    esc_html_e( 'Low Risk 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            </div>
                            <div class="content">
                                <div class="amount"><?php 
    echo esc_html( '0' );
    ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcblu-card wcblu-main-chart medium-risk" style="grid-column: span 3 / auto;">
                    <div class="content">
                        <div class="wcblu-mini-chart">
                            <div class="header">
                                <div class="title"><?php 
    esc_html_e( 'Medium Risk 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            </div>
                            <div class="content">
                                <div class="amount"><?php 
    echo esc_html( '0' );
    ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcblu-card wcblu-main-chart high-risk" style="grid-column: span 3 / auto;">
                    <div class="content">
                        <div class="wcblu-mini-chart">
                            <div class="header">
                                <div class="title"><?php 
    esc_html_e( 'Needs Attention 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            </div>
                            <div class="content">
                                <div class="amount"><?php 
    echo esc_html( '0' );
    ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wcblu-top-ten wcblu-main-chart" style="grid-column: span 6 / auto;">
                    <div class="content">
                        <div class="wcblu-table-title">
                            <span class="wcblu-title"><?php 
    esc_html_e( 'Recent Order Data 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                        </div>
                        <div class="wcblu-recent-order-data-chart-main">
                            <img src="<?php 
    echo esc_url( WB_PLUGIN_URL . 'admin/images/premium-upgrade-img/premium-fraud-data-graph.png' );
    ?>" alt="Fraud Data Graph">
                        </div>
                    </div>
                </div>
                <div class="wcblu-top-ten wcblu-main-chart" style="grid-column: span 6 / auto;">
                    <div class="content">
                        <div class="wcblu-chart-title">
                            <span class="wcblu-chart-title-text"><?php 
    esc_html_e( 'Last 24 Hours Update 🔒', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
                        </div>
                        <div class="wcblu-chart-legend">
                        <div class="item item-0">
                            <div class="icon"><span class="dashicons dashicons-money-alt"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'Total Transaction Amount', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-1">
                            <div class="icon"><span class="dashicons dashicons-clock"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'Total Number of Orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-2">
                            <div class="icon"><span class="dashicons dashicons-dashboard"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'Medium Risk Orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-3">
                            <div class="icon"><span class="dashicons dashicons-performance"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'High-Risk Orders on Hold', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-4">
                            <div class="icon"><span class="dashicons dashicons-dismiss"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'Fraudulent Orders Cancelled', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-5">
                            <div class="icon"><span class="dashicons dashicons-chart-line"></span></div>
                            <div class="label"><?php 
    esc_html_e( 'High-Risk Net Transaction', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                        <div class="item item-5">
                            <div class="icon"><span class="dashicons dashicons-email"></span></span></div>
                            <div class="label"><?php 
    esc_html_e( 'Emails Blocked', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
                            <div class="data"><?php 
    echo esc_html( '0' );
    ?></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dashboard HTML start -->
    </div>
    </div>
    </div>
    <?php 
}

function get_score_meta(  $score_points, $order_id = ''  ) {
    if ( '' === $score_points ) {
        return array(
            'color' => '#adadad',
            'label' => __( 'No fraud checking has been done on this order yet.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        );
    }
    $meta = array(
        'label' => __( 'Low Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'color' => '#7AD03A',
    );
    $getGeneralSettings = get_option( 'wcblu_general_option' );
    $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
    $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
    $low_threshold = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
    $high_threshold = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
    $whitelist_action = get_post_meta( $order_id, 'whitelist_action', true );
    if ( $score_points <= $high_threshold && $score_points >= $low_threshold ) {
        $meta['label'] = __( 'Medium Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        $meta['color'] = ( $whitelist_action === 'user_email_whitelisted' ? 'grey' : '#FFAE00' );
    } elseif ( $score_points > $high_threshold ) {
        $meta['label'] = __( 'High Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        $meta['color'] = ( $whitelist_action === 'user_email_whitelisted' ? 'grey' : '#D54E21' );
    }
    return $meta;
}

// Define the edd_get_score_meta function
function edd_get_score_meta(  $score  ) {
    // This function should return the appropriate meta information based on the score
    $getplugingeneralopt = get_option( 'wcblu_general_option' );
    $wcbfc_settings_low_risk_threshold = 30;
    $wcbfc_settings_high_risk_threshold = 70;
    $getplugingeneraloptarray = ( !empty( $getplugingeneralopt ) ? json_decode( $getplugingeneralopt, true ) : '' );
    if ( isset( $getplugingeneralopt ) && !empty( $getplugingeneralopt ) ) {
        $wcbfc_settings_low_risk_threshold = ( !empty( $getplugingeneraloptarray['wcbfc_settings_low_risk_threshold'] ) ? intval( $getplugingeneraloptarray['wcbfc_settings_low_risk_threshold'] ) : '30' );
        $wcbfc_settings_high_risk_threshold = ( !empty( $getplugingeneraloptarray['wcbfc_settings_high_risk_threshold'] ) ? intval( $getplugingeneraloptarray['wcbfc_settings_high_risk_threshold'] ) : '70' );
    }
    $meta = array(
        'label' => 'Unknown',
    );
    if ( $score <= $wcbfc_settings_low_risk_threshold ) {
        $meta['label'] = 'Low Risk';
    } elseif ( $score <= $wcbfc_settings_high_risk_threshold ) {
        $meta['label'] = 'Medium Risk';
    } else {
        $meta['label'] = 'High Risk';
    }
    return $meta;
}

edd_wbclu_free_fraud_data_dashboard_content();