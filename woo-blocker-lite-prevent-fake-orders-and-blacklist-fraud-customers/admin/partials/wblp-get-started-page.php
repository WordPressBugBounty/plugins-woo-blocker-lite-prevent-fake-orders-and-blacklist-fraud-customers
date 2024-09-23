<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once( plugin_dir_path(__FILE__).'header/plugin-header.php');
?>

<?php
		switch ( $current_tab ) { // phpcs:ignore
			case 'wblp-get-started':
				?>
                    <div class="wcblu-main-table res-cl">
                        <div class="dots-getting-started-main">
                            <div class="getting-started-content">
                                <span><?php esc_html_e( 'How to Get Started', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></span>
                                <h3><?php esc_html_e( 'Welcome to Fraud Prevention Plugin', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
                                <p><?php esc_html_e( 'Thank you for choosing our top-rated WooCommerce Fraud Prevention plugin. Our user-friendly interface makes it easy to set up and prevent fraudulent activity.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
                                <p>
                                    <?php 
                                    echo sprintf(
                                        esc_html__('To help you get started, watch the quick tour video on the right. For more help, explore our help documents or visit our %s for detailed video tutorials.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'),
                                        '<a href="' . esc_url('https://www.youtube.com/@Dotstore16') . '" target="_blank">' . esc_html__('YouTube channel', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers') . '</a>',
                                    );
                                    ?>
                                </p>
                                <div class="getting-started-actions">
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'woocommerce_blacklist_users'), admin_url('admin.php'))); ?>" class="quick-start"><?php esc_html_e( 'Prevent Fraud Customers', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?><span class="dashicons dashicons-arrow-right-alt"></span></a>
                                    <a href="https://docs.thedotstore.com/article/948-beginners-guide-for-fraud-prevention" target="_blank" class="setup-guide"><span class="dashicons dashicons-book-alt"></span><?php esc_html_e( 'Read the Setup Guide', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></a>
                                </div>
                            </div>
                            <div class="getting-started-video">
                                <iframe width="960" height="600" src="<?php echo esc_url('https://www.youtube.com/embed/WLHMaHAd70c'); ?>" title="<?php esc_attr_e( 'Plugin Tour', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                <?php
				break;
            case 'wcblu-import-export-setting':
                if ( wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code() ) {
                    require_once( dirname( __FILE__ ) . '/wcblu-import-export-setting.php' );
                }
                break;
		}
		?>