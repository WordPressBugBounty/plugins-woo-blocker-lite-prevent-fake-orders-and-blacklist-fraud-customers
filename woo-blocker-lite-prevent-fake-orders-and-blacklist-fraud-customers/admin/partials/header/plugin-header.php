<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$plugin_name = 'Fraud Prevention';
$plugin_version = WB_PLUGIN_VERSION;
$plugin_slug = '';
$version_label = 'Free';
$plugin_slug = 'basic_woo_fraud';
$wb_admin_object = new Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers_Admin('', '');
?>

<div id="dotsstoremain">
	<div class="all-pad dots-settings-inner-main">
    <?php 
$wb_admin_object->wb_get_promotional_bar( $plugin_slug );
?>
		<header class="dots-header">
		    <div class="dots-plugin-details">
                <div class="dots-header-left">
                    <div class="dots-logo-main">
                        <img src="<?php 
echo esc_url( WB_PLUGIN_URL . 'admin/images/WooCommerce-Blocker-Prevent-Fake-Orders.png' );
?>">
                    </div>
                    <div class="plugin-name">
                        <div class="title"><?php 
esc_html_e( $plugin_name, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></div>
                    </div>
                    <span class="version-label <?php 
echo esc_attr( $plugin_slug );
?>">
                        <span><?php 
esc_html_e( $version_label, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></span>
                    </span>
                    <span class="version-number"><?php 
echo esc_html( $plugin_version );
?></span>
                </div>
                <div class="dots-header-right">
                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/' );
?>">
                            <?php 
esc_html_e( 'Support', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>
                        </a>
                    </div>

                    <div class="button-dots">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/' );
?>">
                            <?php 
esc_html_e( 'Suggest', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>
                        </a>
                    </div>

                    <div class="button-dots <?php 
echo ( wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                        <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/category/149-premium-plugin-settings' );
?>">
                            <?php 
echo esc_html_e( 'Help', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>
                        </a>
                    </div>

                    <?php 
?>
                        <div class="button-dots">
                            <a target="_blank" href="javascript:void(0);"  class="dots-upgrade-btn">
                                <?php 
esc_html_e( 'Upgrade Now', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>
                            </a>
                        </div>
                    <?php 
?>
                </div>
            </div>
			<?php 
global $pagenow;
$bloker_data_list = '';
$wcblu_post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$wcblu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( isset( $wcblu_post_type ) && 'blocked_user' === $wcblu_post_type && $pagenow === 'edit.php' || $pagenow === 'post.php' || $pagenow === 'post-new.php' ) {
    $bloker_data_list = "active";
}
$edd_active = '';
if ( class_exists( 'Easy_Digital_Downloads' ) ) {
    $edd_active = 'yes';
} else {
    $edd_active = 'no';
}
$fee_list = ( isset( $wcblu_page ) && 'woocommerce_blacklist_users' === $wcblu_page ? 'active' : '' );
$gs_list = ( isset( $wcblu_page ) && 'wcblu-general-settings' === $wcblu_page ? 'active' : '' );
$rules = ( isset( $wcblu_page ) && 'wcblu-auto-rules' === $wcblu_page ? 'active' : '' );
$wcblu_import_export_setting = ( isset( $current_tab ) && 'wcblu-import-export-setting' === $current_tab ? 'active' : '' );
$wcblu_settings_menu = ( isset( $wcblu_page ) && ('wcblu-import-export-setting' === $wcblu_page || 'wcblu-import-export-setting' === $current_tab) ? 'active' : '' );
$wcblu_free_dashboard = ( isset( $wcblu_page ) && 'wcblu-upgrade-dashboard' === $wcblu_page ? 'active' : '' );
$wcblu_dashboard = ( isset( $wcblu_page ) && 'wcblu-dashboard' === $wcblu_page ? 'active' : '' );
$getting_started = ( isset( $wcblu_page ) && isset( $current_tab ) && ('wblp-get-started' === $wcblu_page && 'wblp-get-started' === $current_tab) ? 'active' : '' );
if ( 'yes' === $edd_active ) {
    $edd_wcblu_dashboard = ( isset( $wcblu_page ) && 'edd-wcblu-dashboard' === $wcblu_page ? 'active' : '' );
}
$wcblu_display_submenu = ( !empty( $wcblu_settings_menu ) && 'active' === $wcblu_settings_menu ? 'display:inline-block' : 'display:none' );
$wcblu_account_page = ( isset( $wcblu_page ) && 'woocommerce_blacklist_users-account' === $wcblu_page ? 'active' : '' );
?>
            <div class="dots-bottom-menu-main">
                <div class="dots-menu-main">
                    <nav>
                        <ul>
                            <?php 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
    ?>
                                    <li>
                                        <a class="dotstore_plugin <?php 
    echo esc_attr( $wcblu_dashboard );
    ?>" href="<?php 
    echo esc_url( add_query_arg( array(
        'page' => 'wcblu-dashboard',
    ), admin_url( 'admin.php' ) ) );
    ?>"><?php 
    esc_html_e( 'Dashboard', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></a>
                                    </li>
                                <?php 
}
if ( 'yes' === $edd_active ) {
    ?>
                                    <li>
                                        <a class="dotstore_plugin <?php 
    echo esc_attr( $edd_wcblu_dashboard );
    ?>" href="<?php 
    echo esc_url( add_query_arg( array(
        'page' => 'edd-wcblu-dashboard',
    ), admin_url( 'admin.php' ) ) );
    ?>"><?php 
    esc_html_e( 'Dashboard EDD', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></a>
                                    </li>
                                    <?php 
}
?>
                                <li>
                                    <a class="dotstore_plugin <?php 
esc_attr_e( $gs_list, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>" href="<?php 
echo esc_url( home_url( '/wp-admin/admin.php?page=wcblu-general-settings' ) );
?>"><?php 
esc_html_e( 'General Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></a>
                                </li>
                                <li>
                                    <a class="dotstore_plugin <?php 
esc_attr_e( $rules, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>" href="<?php 
echo esc_url( home_url( '/wp-admin/admin.php?page=wcblu-auto-rules' ) );
?>"><?php 
esc_html_e( 'Rules', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></a>
                                </li>
                                <li>
                                    <a class="dotstore_plugin <?php 
esc_attr_e( $fee_list, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>" href="<?php 
echo esc_url( home_url( '/wp-admin/admin.php?page=woocommerce_blacklist_users' ) );
?>"><?php 
esc_html_e( 'Blacklist Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></a>
                                </li>
                                <li>
                                    <a class="dotstore_plugin <?php 
esc_attr_e( $bloker_data_list, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?>" href="<?php 
echo esc_url( site_url( 'wp-admin/edit.php?post_type=blocked_user' ) );
?>"><?php 
esc_html_e( 'Blocked User List', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></a>
                                </li>
                                
                                
                                <?php 
$wcblu_settings_page_url = '';
$wcblu_settings_page_url = add_query_arg( array(
    'page' => 'wblp-get-started&tab=wblp-get-started',
), admin_url( 'admin.php' ) );
if ( wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code() ) {
    ?>
                                    <li>
                                        <a class="dotstore_plugin <?php 
    echo esc_attr( $wcblu_settings_menu );
    ?>" href="<?php 
    echo esc_url( $wcblu_settings_page_url );
    ?>"><?php 
    esc_html_e( 'Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></a>
                                    </li>
                                    <li>
                                        <a class="dotstore_plugin <?php 
    echo esc_attr( $wcblu_account_page );
    ?>" href="<?php 
    echo esc_url( wbpfoabfc_fs()->get_account_url() );
    ?>"><?php 
    esc_html_e( 'License', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></a>
                                    </li>
                                    <?php 
} else {
    ?>
                                    <li>
                                        <a class="dotstore_plugin dots_get_premium <?php 
    echo esc_attr( $wcblu_free_dashboard );
    ?>" href="<?php 
    echo esc_url( add_query_arg( array(
        'page' => 'wcblu-upgrade-dashboard',
    ), admin_url( 'admin.php' ) ) );
    ?>"><?php 
    esc_html_e( 'Get Premium', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></a>
                                    </li>
                                    <?php 
}
?>
                            </ul>
                        </nav>
                    </div>
                    <div class="dots-getting-started">
                        <a href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'wblp-get-started',
    'tab'  => 'wblp-get-started',
), admin_url( 'admin.php' ) ) );
?>" class="<?php 
echo esc_attr( $getting_started );
?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z" fill="#a0a0a0"></path></svg></a>
                    </div>
                </div>

		</header>
        <!-- Upgrade to pro popup -->
        <?php 
if ( !(wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code()) ) {
    require_once WB_PLUGIN_PATH . 'admin/partials/dots-upgrade-popup.php';
}
?>
        <div class="dots-settings-inner-main">
            <div class="dots-settings-left-side">
                <div class="dotstore-submenu-items" style="<?php 
echo esc_attr( $wcblu_display_submenu );
?>">
                    <ul>
                    <?php 
?>
                        <li><a href="<?php 
echo esc_url( 'https://www.thedotstore.com/plugins/' );
?>" target="_blank"><?php 
esc_html_e( 'Shop Plugins', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
?></a></li>
                    </ul>
                </div>
