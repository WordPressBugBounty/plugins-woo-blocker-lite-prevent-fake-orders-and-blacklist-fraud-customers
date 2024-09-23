<?php

/**
 * Handles premium plugin user dashboard for review data
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
// Function for free plugin content
function wbclu_free_fraud_data_dashboard_content() {
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

wbclu_free_fraud_data_dashboard_content();