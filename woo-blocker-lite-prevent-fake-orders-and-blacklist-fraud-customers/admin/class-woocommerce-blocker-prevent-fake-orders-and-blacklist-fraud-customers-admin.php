<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers
 * @subpackage Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers
 * @subpackage Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers/admin
 * @author     multidots <info@multidots.in>
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;
class Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     *
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * @param $string
     *
     * @return bool
     * funtion to return string is in json format or not
     */
    public static function wcblu_isdata_Json( $string ) {
        json_decode( $string );
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook ) {
        global $typenow;
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $order_blacklist = array('dotstore-plugins_page_woocommerce_blacklist_users', 'woocommerce_page_wc-orders', 'download_page_edd-payment-history');
        global $pagenow;
        $get_action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_post = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        // Check if we're on an order edit page
        if ( $pagenow === 'post.php' && isset( $get_post ) && isset( $get_action ) && $get_action === 'edit' ) {
            $post_id = intval( $get_post );
            $post_type = get_post_type( $post_id );
            if ( $post_type === 'shop_order' ) {
                $order_blacklist[] = 'post.php';
            }
        }
        if ( 'edit.php' === $pagenow && isset( $get_post_type ) && 'shop_order' === $get_post_type ) {
            $order_blacklist[] = 'edit.php';
        }
        if ( in_array( $hook, $order_blacklist, true ) ) {
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-admin.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_script(
                'jquery-wblp-order-widget-js',
                plugin_dir_url( __FILE__ ) . 'js/wblp-order-widget.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_localize_script( 'jquery-wblp-order-widget-js', 'wblp_order_ajax', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'wcblu-ajax-nonce' ),
                'i18n'    => array(
                    'select_field'    => __( 'Select at least one detail to continue.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'confirm_block'   => __( 'Block Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'confirm_unblock' => __( 'Unblock Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'not_blocked'     => __( 'Order details not available to update.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                ),
            ) );
        }
        // Dashboard widget styles (Blacklist Users Report).
        if ( 'index.php' === $hook ) {
            wp_enqueue_style(
                $this->plugin_name . '-dashboard-widget',
                plugin_dir_url( __FILE__ ) . 'css/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-admin.css',
                array(),
                $this->version,
                'all'
            );
        }
        $valid_hooks = array(
            'admin_page_wblp-get-started',
            'admin_page_woocommerce_blacklist_users',
            'dotstore-plugins_page_woocommerce_blacklist_users',
            'blocked_user',
            'dotstore-plugins_page_wcblu-import-export-setting',
            'dotstore-plugins_page_wblp-get-started',
            'dotstore-plugins_page_wblp-information',
            'dotstore-plugins_page_wcblu-general-settings',
            'dotstore-plugins_page_wcblu-auto-rules',
            'admin_page_woocommerce_blacklist_users-account',
            'dotstore-plugins_page_wcblu-upgrade-dashboard',
            'dotstore-plugins_page_wcblu-dashboard',
            'toplevel_page_woocommerce_blacklist_users',
            'dotstore-plugins_page_edd-wcblu-dashboard',
            'dotstore-plugins_page_wcblu-ai-detection-setting'
        );
        if ( in_array( $hook, $valid_hooks, true ) || 'blocked_user' === $typenow ) {
            wp_enqueue_style(
                'main-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                'plugin-new-style',
                plugin_dir_url( __FILE__ ) . 'css/plugin-new-style.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_script(
                $this->plugin_name . 'freemius_pro',
                'https://checkout.freemius.com/checkout.min.js',
                array('jquery'),
                $this->version,
                true
            );
            wp_enqueue_script(
                $this->plugin_name . '-help-scout-beacon-js',
                plugin_dir_url( __FILE__ ) . 'js/help-scout-beacon.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-admin.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_enqueue_style(
                $this->plugin_name . 'select2-style',
                plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_script(
                $this->plugin_name . 'select2-js',
                plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js',
                array('jquery'),
                $this->version,
                true
            );
            if ( !(wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code()) ) {
                wp_enqueue_style(
                    $this->plugin_name . 'upgrade-dashboard-style',
                    plugin_dir_url( __FILE__ ) . 'css/upgrade-dashboard.css',
                    array(),
                    $this->version,
                    'all'
                );
                wp_enqueue_style(
                    $this->plugin_name . 'font-awesome',
                    plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
                    array(),
                    $this->version,
                    'all'
                );
            }
            wp_enqueue_style(
                $this->plugin_name . 'plugin-setup-wizard',
                plugin_dir_url( __FILE__ ) . 'css/plugin-setup-wizard.css',
                array(),
                $this->version,
                'all'
            );
        }
        if ( 'dotstore-plugins_page_wcblu-dashboard' === $hook || 'dotstore-plugins_page_edd-wcblu-dashboard' === $hook ) {
            wp_enqueue_script(
                $this->plugin_name . '-chart',
                plugin_dir_url( __FILE__ ) . 'js/chart.js',
                array('jquery'),
                $this->version,
                false
            );
        }
        wp_enqueue_script(
            'jquery-validate',
            plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js',
            array(),
            $this->version,
            false
        );
        wp_enqueue_script(
            'jquery-knob-min-js',
            plugin_dir_url( __FILE__ ) . 'js/jquery.knob.min.js',
            array(),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'adminajax', array(
            'ajaxurl'                => admin_url( 'admin-ajax.php' ),
            'ajax_icon'              => plugin_dir_url( __FILE__ ) . '/images/ajax-loader.gif',
            'nonce'                  => wp_create_nonce( 'wcblu-ajax-nonce' ),
            'dpb_api_url'            => WB_STORE_URL,
            'resetConfirm'           => esc_html__( 'Are you sure you want to reset all settings? This action cannot be undone.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'importError'            => array(
                'invalidFile' => esc_html__( 'Please add JSON file', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'exmptyFile'  => esc_html__( 'Please choose JSON file', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ),
            'convert_to_pro_nonce'   => wp_create_nonce( 'wcblu_convert_to_pro_purchase' ),
            'recaptcha_verify_nonce' => wp_create_nonce( 'wcblu_verify_recaptcha_nonce' ),
            'recaptchaVerifyError'   => esc_html__( 'Verification failed. Please check your network connection and try again.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsOn'                  => esc_html__( 'ON', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsOff'                 => esc_html__( 'OFF', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsRules'               => esc_html__( '%1$s rules will be enabled', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsWeights'             => esc_html__( 'Weights: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsAutoBlacklist'       => esc_html__( 'Automatic Blacklisting: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsAutoIp'              => esc_html__( 'Automatically block high-risk IPs: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsBeforePay'           => esc_html__( 'Before Payment Checking: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsUpdateStatus'        => esc_html__( 'Enable Update Order Status: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsPlaceOrder'          => esc_html__( 'Place order: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsMediumRisk'          => esc_html__( 'Medium Risk: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsHighRisk'            => esc_html__( 'High Risk: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsApplyError'          => esc_html__( 'Could not apply setup. Please try again.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'ocsPreviewIntro'        => esc_html__( 'Here\'s what will be applied with the selected %s setup.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ) );
        wp_enqueue_script( 'jquery-ui-dialog' );
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchSelectedIpAddress = ( !empty( $getpluginoptionarray['wcblu_block_ip'] ) ? $getpluginoptionarray['wcblu_block_ip'] : '' );
        $optionsIpAddress = ( !empty( $fetchSelectedIpAddress ) ? wp_json_encode( $fetchSelectedIpAddress ) : wp_json_encode( array() ) );
        $fetchSelecetedState = ( !empty( $getpluginoptionarray['wcblu_block_state'] ) ? $getpluginoptionarray['wcblu_block_state'] : '' );
        $optionsState = ( !empty( $fetchSelecetedState ) ? wp_json_encode( $fetchSelecetedState ) : wp_json_encode( array() ) );
        $fetchSelecetedCountry = ( !empty( $getpluginoptionarray['wcblu_block_country'] ) ? $getpluginoptionarray['wcblu_block_country'] : '' );
        $optionsCountry = ( !empty( $fetchSelecetedCountry ) ? wp_json_encode( $fetchSelecetedCountry ) : wp_json_encode( array() ) );
        $fetchSelecetedZip = ( !empty( $getpluginoptionarray['wcblu_block_zip'] ) ? $getpluginoptionarray['wcblu_block_zip'] : '' );
        $optionsZip = ( !empty( $fetchSelecetedZip ) ? wp_json_encode( $fetchSelecetedZip ) : wp_json_encode( array() ) );
        $fetchSelecetedZone = ( !empty( $getpluginoptionarray['wcblu_block_zone'] ) ? $getpluginoptionarray['wcblu_block_zone'] : '' );
        $optionsZone = ( !empty( $fetchSelecetedZone ) ? wp_json_encode( $fetchSelecetedZone ) : wp_json_encode( array() ) );
        $fetchSelecetedUserRole = ( !empty( $getpluginoptionarray['wcblu_block_userrole'] ) ? $getpluginoptionarray['wcblu_block_userrole'] : '' );
        $optionsUserRole = ( !empty( $fetchSelecetedUserRole ) ? wp_json_encode( $fetchSelecetedUserRole ) : wp_json_encode( array() ) );
        $fetchSelecetedDomain = ( !empty( $getpluginoptionarray['wcblu_block_domain'] ) ? $getpluginoptionarray['wcblu_block_domain'] : '' );
        $optionsDomain = ( !empty( $fetchSelecetedDomain ) ? wp_json_encode( $fetchSelecetedDomain ) : wp_json_encode( array() ) );
        $fetchWildcardDomainExt = ( !empty( $getpluginoptionarray['wcblu_block_domain_ext'] ) ? $getpluginoptionarray['wcblu_block_domain_ext'] : '' );
        $optionsDomainExt = ( !empty( $fetchWildcardDomainExt ) ? wp_json_encode( $fetchWildcardDomainExt ) : wp_json_encode( array() ) );
        $fetchSelectedPhone = ( !empty( $getpluginoptionarray['wcblu_block_phone'] ) ? $getpluginoptionarray['wcblu_block_phone'] : '' );
        $optionsPhone = ( !empty( $fetchSelectedPhone ) ? wp_json_encode( $fetchSelectedPhone ) : wp_json_encode( array() ) );
        $fetchSelecetedFirstName = ( !empty( $getpluginoptionarray['wcblu_block_first_name'] ) ? $getpluginoptionarray['wcblu_block_first_name'] : '' );
        $optionsFirstName = ( !empty( $fetchSelecetedFirstName ) ? wp_json_encode( $fetchSelecetedFirstName ) : wp_json_encode( array() ) );
        $fetchSelecetedLastName = ( !empty( $getpluginoptionarray['wcblu_block_last_name'] ) ? $getpluginoptionarray['wcblu_block_last_name'] : '' );
        $optionsLastName = ( !empty( $fetchSelecetedLastName ) ? wp_json_encode( $fetchSelecetedLastName ) : wp_json_encode( array() ) );
        $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_user_agent'] ) ? $getpluginoptionarray['wcblu_block_user_agent'] : '' );
        $optionsUserAgent = ( !empty( $fetchCurrentBrowser ) ? wp_json_encode( $fetchCurrentBrowser ) : wp_json_encode( array() ) );
        $fetchSelectedEmail = ( !empty( $getpluginoptionarray['wcblu_block_email'] ) ? $getpluginoptionarray['wcblu_block_email'] : '' );
        $optionsEmail = ( !empty( $fetchSelectedEmail ) ? wp_json_encode( $fetchSelectedEmail ) : wp_json_encode( array() ) );
        $localize_php_variable_array = array(
            'getEmailOption'     => array(
                'emailarray' => $optionsEmail,
            ),
            'getUserAgentOption' => array(
                'useragentarray' => $optionsUserAgent,
            ),
            'getLastNameOption'  => array(
                'lastnamearray' => $optionsLastName,
            ),
            'getFirstNameOption' => array(
                'firstnamearray' => $optionsFirstName,
            ),
            'getPhoneOption'     => array(
                'phonearray' => $optionsPhone,
            ),
            'getDomainextOption' => array(
                'domainextarray' => $optionsDomainExt,
            ),
            'getDomainOption'    => array(
                'domainarray' => $optionsDomain,
            ),
            'getUserroleOption'  => array(
                'userrolearray' => $optionsUserRole,
            ),
            'getZoneOption'      => array(
                'zonearray' => $optionsZone,
            ),
            'getZipOption'       => array(
                'ziparray' => $optionsZip,
            ),
            'getStateOption'     => array(
                'statearray' => $optionsState,
            ),
            'getCountryOption'   => array(
                'countryarray' => $optionsCountry,
            ),
            'getIpOption'        => array(
                'ipaddressarray' => $optionsIpAddress,
            ),
        );
        ?>

		<input type="hidden" value='<?php 
        echo esc_attr( wp_json_encode( $localize_php_variable_array ) );
        ?>' name="localize_json_output">
	
	<?php 
    }

    /**
     * function to return custom menu for dot store.
     */
    public function remove_premium_blocker_custom_menu() {
        remove_submenu_page( 'dots_store', 'dots_store' );
        remove_submenu_page( 'dots_store', 'wblp-information' );
        remove_submenu_page( 'dots_store', 'wblp-get-started' );
        remove_submenu_page( 'dots_store', 'blocked_user' );
        remove_submenu_page( 'dots_store', 'wcblu-import-export-setting' );
        remove_submenu_page( 'dots_store', 'wcblu-general-settings' );
        remove_submenu_page( 'dots_store', 'wcblu-auto-rules' );
        remove_submenu_page( 'dots_store', 'wcblu-upgrade-dashboard' );
        remove_submenu_page( 'dots_store', 'wcblu-dashboard' );
        remove_submenu_page( 'dots_store', 'edd-wcblu-dashboard' );
        remove_submenu_page( 'dots_store', 'wcblu-ai-detection-setting' );
    }

    /**
     * function to return started page of plugin
     */
    public function wblp_get_started_page() {
        // Exit if accessed directly
        if ( !defined( 'ABSPATH' ) ) {
            exit;
        }
        $file_dir = '/partials/wblp-get-started-page.php';
        if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
            include_once dirname( __FILE__ ) . $file_dir;
        }
    }

    /**
     * Function to return inclued import export setting page.
     */
    public function wbclu_import_export_page() {
        if ( !defined( 'ABSPATH' ) ) {
            exit;
        }
        $file_dir = '/partials/wcblu-import-export-setting.php';
        if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
            include_once dirname( __FILE__ ) . $file_dir;
        }
    }

    /**
     * Premium version info page
     *
     */
    public function wbclu_free_user_upgrade_page() {
        require_once plugin_dir_path( __FILE__ ) . '/partials/dots-upgrade-dashboard.php';
    }

    /**
     * Auto Blocking Dashboard
     */
    public function wbclu_dashboard_page() {
        require_once plugin_dir_path( __FILE__ ) . '/partials/dots-dashboard.php';
    }

    /**
     * Function to return inclued general settings setting page.
     */
    public function wbclu_general_settings_page() {
        if ( !defined( 'ABSPATH' ) ) {
            exit;
        }
        $file_dir = '/partials/wcblu-general-settings.php';
        if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
            include_once dirname( __FILE__ ) . $file_dir;
        }
    }

    /**
     * Function to return inclued rules setting page.
     */
    public function wbclu_rules_page() {
        if ( !defined( 'ABSPATH' ) ) {
            exit;
        }
        $file_dir = '/partials/wcblu-rule-settings.php';
        if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
            include_once dirname( __FILE__ ) . $file_dir;
        }
    }

    /**
     * function to returnplugin header page of plugin
     */
    public function woocommerce_blocker_pro_plugin_custom_plugin_header() {
        $post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $post_type ) && 'blocked_user' === $post_type ) {
            $file_dir = '/partials/header/plugin-header.php';
            if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
                include_once dirname( __FILE__ ) . $file_dir;
            }
        }
    }

    /**
     * @param $columns
     *
     * @return array
     * function to return add column in blocker wp list
     */
    function set_custom_edit_banned_user_columns( $columns ) {
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'title'   => __( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'attempt' => __( 'Attempt', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        );
        return $columns;
    }

    /**
     * @param $actions
     *
     * @return mixed
     * function to return edit column
     */
    function custom_bulk_edit_action_for_banned_user( $actions ) {
        unset($actions['edit']);
        return $actions;
    }

    /**
     * function return to add content in footer
     */
    function custom_banned_user_admin_footer() {
        global $post_type;
        if ( 'blocked_user' === $post_type ) {
            ?>
			<script type="text/javascript">
							jQuery(document).ready(function () {
								jQuery('<option>').val('delete_permanently').text('<?php 
            esc_attr_e( 'Delete Permanently', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?>').appendTo('select[name=\'action\']');
							});
			</script>
			<?php 
        }
    }

    /**
     * function to return update option in plugin
     */
    public function wcblu_custom_add_update_options() {
        /**
         * get form action
         */
        $getformsumbitaction = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $getformsumbitaction = ( !empty( $getformsumbitaction ) ? $getformsumbitaction : '' );
        $getformactiontype = filter_input( INPUT_POST, 'action-which', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $getformactiontype = ( !empty( $getformactiontype ) ? $getformactiontype : '' );
        $wcpoa_blklist_nonce = filter_input( INPUT_POST, 'wcblu_blacklist_settings_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        /**
         * check form action
         *
         */
        if ( !empty( $getformsumbitaction ) && 'submit_form_wcblu' === $getformsumbitaction && !empty( $getformactiontype ) && 'add' === $getformactiontype && wp_verify_nonce( sanitize_text_field( $wcpoa_blklist_nonce ), 'wcblu_blacklist_settings' ) ) {
            /**
             * get plugin option settings
             */
            //get user block track type
            $wc_user_register_type = filter_input( INPUT_POST, 'wc_user_register_type', FILTER_SANITIZE_NUMBER_INT );
            $wc_user_place_order_type = filter_input( INPUT_POST, 'wc_user_place_order_type', FILTER_SANITIZE_NUMBER_INT );
            $wc_user_address_type = filter_input( INPUT_POST, 'wc_user_address_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_user_register_type = ( empty( $wc_user_register_type ) ? '0' : $wc_user_register_type );
            $wc_user_place_order_type = ( empty( $wc_user_place_order_type ) ? '0' : $wc_user_place_order_type );
            $wc_user_address_type = ( empty( $wc_user_address_type ) ? '' : $wc_user_address_type );
            $wc_register_type = $wc_user_register_type;
            $wc_place_order_type = $wc_user_place_order_type;
            $wc_address_type = $wc_user_address_type;
            //get plugin block option
            $post_array_sanitise_ = filter_input_array( INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $getemail = ( !empty( $post_array_sanitise_['email'] ) ? array_filter( $post_array_sanitise_['email'] ) : '' );
            $wc_all_chk_selection = ( !empty( $post_array_sanitise_['all_chk_selection'] ) ? array_filter( $post_array_sanitise_['all_chk_selection'] ) : '' );
            $getipbasic = ( !empty( $post_array_sanitise_['ip-basic'] ) ? array_filter( $post_array_sanitise_['ip-basic'] ) : '' );
            $getstate = ( !empty( $post_array_sanitise_['state'] ) ? array_filter( $post_array_sanitise_['state'] ) : '' );
            $getcountry = ( !empty( $post_array_sanitise_['country'] ) ? array_filter( $post_array_sanitise_['country'] ) : '' );
            $getzip = ( !empty( $post_array_sanitise_['zip'] ) ? array_filter( $post_array_sanitise_['zip'] ) : '' );
            $getdomain = ( !empty( $post_array_sanitise_['domain'] ) ? array_filter( $post_array_sanitise_['domain'] ) : '' );
            $getdomainext = ( !empty( $post_array_sanitise_['domain_ext'] ) ? array_filter( $post_array_sanitise_['domain_ext'] ) : '' );
            $getphone = ( !empty( $post_array_sanitise_['phone'] ) ? array_filter( $post_array_sanitise_['phone'] ) : '' );
            $getfirstname = ( !empty( $post_array_sanitise_['first_name'] ) ? array_filter( $post_array_sanitise_['first_name'] ) : '' );
            $getlastname = ( !empty( $post_array_sanitise_['last_name'] ) ? array_filter( $post_array_sanitise_['last_name'] ) : '' );
            $get_address = ( !empty( $post_array_sanitise_['wcblu_address'] ) ? array_filter( $post_array_sanitise_['wcblu_address'] ) : '' );
            $getuseragent = ( !empty( $post_array_sanitise_['user_agent'] ) ? array_filter( $post_array_sanitise_['user_agent'] ) : '' );
            $getzone = ( !empty( $post_array_sanitise_['zone'] ) ? array_filter( $post_array_sanitise_['zone'] ) : '' );
            $getuserrole = ( !empty( $post_array_sanitise_['userrole'] ) ? array_filter( $post_array_sanitise_['userrole'] ) : '' );
            //get plugin error messages
            $wc_email_msg_sett = filter_input( INPUT_POST, 'wc_email_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_ip_msg_sett = filter_input( INPUT_POST, 'wc_ip_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_state_msg_sett = filter_input( INPUT_POST, 'wc_state_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_country_msg_sett = filter_input( INPUT_POST, 'wc_country_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_zpcode_msg_sett = filter_input( INPUT_POST, 'wc_zpcode_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_domain_msg_sett = filter_input( INPUT_POST, 'wc_domain_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_domain_ext_msg_sett = filter_input( INPUT_POST, 'wc_domain_ext_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_phone_msg_sett = filter_input( INPUT_POST, 'wc_phone_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_first_name_msg_sett = filter_input( INPUT_POST, 'wc_first_name_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_first_name_relation = filter_input( INPUT_POST, 'wc_first_name_relation', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_user_agent_msg_sett = filter_input( INPUT_POST, 'wc_user_agent_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_last_name_msg_sett = filter_input( INPUT_POST, 'wc_last_name_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_last_name_relation = filter_input( INPUT_POST, 'wc_last_name_relation', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_address_msg_sett = filter_input( INPUT_POST, 'wc_address_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_zone_msg_sett = filter_input( INPUT_POST, 'wc_zone_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_userrole_msg_sett = filter_input( INPUT_POST, 'wc_userrole_msg_sett', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $automatic_blacklist = filter_input( INPUT_POST, 'wcblu_automatic_blacklist', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_email_msg_sett = ( !empty( $wc_email_msg_sett ) ? $wc_email_msg_sett : esc_html__( 'This email address has been blocked, please try other email address or Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_ip_msg_sett = ( !empty( $wc_ip_msg_sett ) ? $wc_ip_msg_sett : esc_html__( 'This IP address has been blocked due to some reason, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_state_msg_sett = ( !empty( $wc_state_msg_sett ) ? $wc_state_msg_sett : esc_html__( 'Sorry :( We are not shipping this products in this state.  Kindly contact admin. ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_country_msg_sett = ( !empty( $wc_country_msg_sett ) ? $wc_country_msg_sett : esc_html__( 'Sorry :( We are not shipping this products in this country.  Kindly contact admin. ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_zpcode_msg_sett = ( !empty( $wc_zpcode_msg_sett ) ? $wc_zpcode_msg_sett : esc_html__( 'Sorry :( We are not shipping this products in this location. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_domain_msg_sett = ( !empty( $wc_domain_msg_sett ) ? $wc_domain_msg_sett : esc_html__( 'Sorry :( This domain has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_domain_ext_msg_sett = ( !empty( $wc_domain_ext_msg_sett ) ? $wc_domain_ext_msg_sett : esc_html__( 'Sorry :( This domain extension has been blocked, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_phone_msg_sett = ( !empty( $wc_phone_msg_sett ) ? $wc_phone_msg_sett : esc_html__( 'Sorry :( This phone number has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_first_name_msg_sett = ( !empty( $wc_first_name_msg_sett ) ? $wc_first_name_msg_sett : esc_html__( 'Sorry :( This first name has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_user_agent_msg_sett = ( !empty( $wc_user_agent_msg_sett ) ? $wc_user_agent_msg_sett : esc_html__( 'This browser has been blocked due to some reason, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_last_name_msg_sett = ( !empty( $wc_last_name_msg_sett ) ? $wc_last_name_msg_sett : esc_html__( 'Sorry :( This last name has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_address_msg_sett = ( !empty( $wc_address_msg_sett ) ? $wc_address_msg_sett : esc_html__( 'Sorry :( This Address has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_zone_msg_sett = ( !empty( $wc_zone_msg_sett ) ? $wc_zone_msg_sett : esc_html__( 'Sorry :( This zone has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $wc_userrole_msg_sett = ( !empty( $wc_userrole_msg_sett ) ? $wc_userrole_msg_sett : esc_html__( 'Sorry :( This user role has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            $automatic_blacklist = ( !empty( $automatic_blacklist ) ? $automatic_blacklist : '0' );
            $wc_first_name_relation = ( !empty( $wc_first_name_relation ) ? $wc_first_name_relation : 'or' );
            $wc_last_name_relation = ( !empty( $wc_last_name_relation ) ? $wc_last_name_relation : 'or' );
            //get plugin external user block list
            $wc_enb_ext_bl = filter_input( INPUT_POST, 'wc_enb_ext_bl', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wc_enb_ext_bl = ( !empty( $wc_enb_ext_bl ) ? $wc_enb_ext_bl : '' );
            $wcbluoption_array = array();
            $wcbluoption_array['wcblu_register_type'] = $wc_register_type;
            $wcbluoption_array['wcblu_place_order_type'] = $wc_place_order_type;
            $wcbluoption_array['wcblu_address_type'] = $wc_address_type;
            $wcbluoption_array['wcblu_block_ip'] = $getipbasic;
            $wcbluoption_array['wcblu_block_state'] = $getstate;
            $wcbluoption_array['wcblu_block_country'] = $getcountry;
            $wcbluoption_array['wcblu_block_zip'] = $getzip;
            $wcbluoption_array['wcblu_block_domain'] = $getdomain;
            $wcbluoption_array['wcblu_block_domain_ext'] = $getdomainext;
            $wcbluoption_array['wcblu_block_phone'] = $getphone;
            $wcbluoption_array['wcblu_block_first_name'] = $getfirstname;
            $wcbluoption_array['wcblu_block_last_name'] = $getlastname;
            $wcbluoption_array['wcblu_block_address'] = $get_address;
            $wcbluoption_array['wcblu_block_user_agent'] = $getuseragent;
            $wcbluoption_array['wcblu_block_zone'] = $getzone;
            $wcbluoption_array['wcblu_block_userrole'] = $getuserrole;
            $wcbluoption_array['wcblu_email_msg'] = $wc_email_msg_sett;
            $wcbluoption_array['wcblu_ip_msg'] = $wc_ip_msg_sett;
            $wcbluoption_array['wcblu_state_msg'] = $wc_state_msg_sett;
            $wcbluoption_array['wcblu_country_msg'] = $wc_country_msg_sett;
            $wcbluoption_array['wcblu_zpcode_msg'] = $wc_zpcode_msg_sett;
            $wcbluoption_array['wcblu_domain_msg'] = $wc_domain_msg_sett;
            $wcbluoption_array['wcblu_domain_ext_msg'] = $wc_domain_ext_msg_sett;
            $wcbluoption_array['wcblu_phone_msg'] = $wc_phone_msg_sett;
            $wcbluoption_array['wcblu_first_name_msg'] = $wc_first_name_msg_sett;
            $wcbluoption_array['wcblu_last_name_msg'] = $wc_last_name_msg_sett;
            $wcbluoption_array['wcblu_address_msg'] = $wc_address_msg_sett;
            $wcbluoption_array['wcblu_user_agent_msg'] = $wc_user_agent_msg_sett;
            $wcbluoption_array['wcblu_zone_msg'] = $wc_zone_msg_sett;
            $wcbluoption_array['wcblu_userrole_msg'] = $wc_userrole_msg_sett;
            $wcbluoption_array['wcblu_enable_ext_bl'] = $wc_enb_ext_bl;
            $wcbluoption_array['wcblu_block_email'] = $getemail;
            $wcbluoption_array['wcblu_automatic_blacklist'] = $automatic_blacklist;
            $wcbluoption_array['wcblu_all_chk_selection'] = $wc_all_chk_selection;
            $wcbluoption_array['wcblu_first_name_relation'] = $wc_first_name_relation;
            $wcbluoption_array['wcblu_last_name_relation'] = $wc_last_name_relation;
            $wcbluoption_array = wp_json_encode( $wcbluoption_array );
            update_option( 'wcblu_option', $wcbluoption_array );
        }
        wp_safe_redirect( add_query_arg( array(
            'page'    => 'woocommerce_blacklist_users',
            'success' => 'true',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * function for reset plugins all settings.
     *
     */
    public function wcblu_reset_settings() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'You are not allowed to reset settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), 403 );
        }
        update_option( 'wcblu_option', '' );
        die;
    }

    /**
     * Verify the saved Google reCAPTCHA keys against the Google siteverify API.
     */
    public function wcblu_verify_recaptcha_keys() {
        check_ajax_referer( 'wcblu_verify_recaptcha_nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'You are not allowed to verify these settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), 403 );
        }
        $general_option = json_decode( get_option( 'wcblu_general_option' ), true );
        $general_option = ( is_array( $general_option ) ? $general_option : array() );
        $version = ( !empty( $general_option['wcbfc_recaptcha_version'] ) ? $general_option['wcbfc_recaptcha_version'] : '' );
        if ( 'wcblu_v2_keys' !== $version && 'wcblu_v3_keys' !== $version ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'Please select a reCAPTCHA version and save the settings first.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        $site_key_field = ( 'wcblu_v2_keys' === $version ? 'wcblu_v2_keys_value' : 'wcblu_v3_keys_value' );
        $secret_key_field = ( 'wcblu_v2_keys' === $version ? 'wcblu_v2_secret_keys_value' : 'wcblu_v3_secret_keys_value' );
        $site_key = ( !empty( $general_option[$site_key_field] ) ? $general_option[$site_key_field] : '' );
        $secret_key = ( !empty( $general_option[$secret_key_field] ) ? $general_option[$secret_key_field] : '' );
        if ( empty( $site_key ) || empty( $secret_key ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'Please enter and save both the Site Key and the Secret Key before verifying.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
            'timeout' => 15,
            'body'    => array(
                'secret'   => $secret_key,
                'response' => 'wcblu-connection-test',
            ),
        ) );
        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array(
                'message' => sprintf( esc_html__( 'Could not reach Google reCAPTCHA: %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), $response->get_error_message() ),
            ) );
        }
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $error_codes = ( !empty( $body['error-codes'] ) ? (array) $body['error-codes'] : array() );
        if ( in_array( 'invalid-input-secret', $error_codes, true ) || in_array( 'missing-input-secret', $error_codes, true ) || in_array( 'bad-request', $error_codes, true ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'The Secret Key was rejected by Google. Please check the saved keys and try again.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        wp_send_json_success( array(
            'message' => esc_html__( 'Connection successful. Your saved reCAPTCHA keys are accepted by Google.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ) );
    }

    /**
     * Remove email address from blacklist if whiltelisted.
     */
    public function wcblu_check_blacklist_whitelist() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
        $wcblu_option = json_decode( $wcblu_option, true );
        $wcblu_block_email = ( isset( $wcblu_option['wcblu_block_email'] ) ? $wcblu_option['wcblu_block_email'] : '' );
        if ( !empty( $wcblu_block_email ) ) {
            $whitelistarray = filter_input( INPUT_POST, 'whitelist', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $expwhitearray = explode( "\n", $whitelistarray );
            $result = array_diff( $wcblu_block_email, $expwhitearray );
            $wcblu_option['wcblu_block_email'] = $result;
            $wcblu_option_array = wp_json_encode( $wcblu_option );
            update_option( 'wcblu_option', $wcblu_option_array );
        }
        wp_die();
    }

    /**
     * function for update general settings.
     */
    function wcblu_update_general_settings() {
        $getformsumbitaction = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $wcblu_save_settings_nonce = filter_input( INPUT_POST, 'wcblu_plugin_general_settings_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $wcblugeneraloption_array = array();
        /**
         * check form action
         *
         */
        if ( !empty( $getformsumbitaction ) && 'submit_general_setting_form_wcblu' === $getformsumbitaction && wp_verify_nonce( sanitize_text_field( $wcblu_save_settings_nonce ), 'wcblu_plugin_general_settings' ) ) {
            $wcbfc_recaptcha_status = filter_input( INPUT_POST, 'wcbfc_recaptcha_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_recaptcha_version = filter_input( INPUT_POST, 'wcbfc_recaptcha_version', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcblu_v2_keys_value = filter_input( INPUT_POST, 'wcblu_v2_keys_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcblu_v3_keys_value = filter_input( INPUT_POST, 'wcblu_v3_keys_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcblu_v2_secret_keys_value = filter_input( INPUT_POST, 'wcblu_v2_secret_keys_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcblu_v3_secret_keys_value = filter_input( INPUT_POST, 'wcblu_v3_secret_keys_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_recaptcha_status = ( empty( $wcbfc_recaptcha_status ) ? 'off' : $wcbfc_recaptcha_status );
            $wcbfc_recaptcha_version = ( empty( $wcbfc_recaptcha_version ) ? '' : $wcbfc_recaptcha_version );
            $wcblu_v2_keys_value = ( empty( $wcblu_v2_keys_value ) ? '' : $wcblu_v2_keys_value );
            $wcblu_v3_keys_value = ( empty( $wcblu_v3_keys_value ) ? '' : $wcblu_v3_keys_value );
            $wcblu_v2_secret_keys_value = ( empty( $wcblu_v2_secret_keys_value ) ? '' : $wcblu_v2_secret_keys_value );
            $wcblu_v3_secret_keys_value = ( empty( $wcblu_v3_secret_keys_value ) ? '' : $wcblu_v3_secret_keys_value );
            /**
             * Whitelist the user roles
             */
            if ( '1' === $wcbfc_enable_whitelist_user_roles && !empty( $wcblu_whitelist_user_roles ) ) {
                $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
                $wcblu_option = json_decode( $wcblu_option, true );
                $wcblu_block_userrole = ( isset( $wcblu_option['wcblu_block_userrole'] ) ? $wcblu_option['wcblu_block_userrole'] : '' );
                if ( !empty( $wcblu_block_userrole ) ) {
                    $expwhitearray = $wcblu_whitelist_user_roles;
                    $result = array_diff( $wcblu_block_userrole, $expwhitearray );
                    $wcblu_option['wcblu_block_userrole'] = $result;
                    $wcblu_option_array = wp_json_encode( $wcblu_option );
                    update_option( 'wcblu_option', $wcblu_option_array );
                }
            }
            /**
             * Whitelist the user IP
             */
            if ( '1' === $wcbfc_enable_whitelist_ips && !empty( $wcblu_settings_whitelist_ips ) ) {
                $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
                $wcblu_option = json_decode( $wcblu_option, true );
                $wcblu_block_ip = ( isset( $wcblu_option['wcblu_block_ip'] ) ? $wcblu_option['wcblu_block_ip'] : '' );
                if ( !empty( $wcblu_block_ip ) ) {
                    $expwhitearray = explode( "\n", $wcblu_settings_whitelist_ips );
                    $result = array_diff( $wcblu_block_ip, $expwhitearray );
                    $wcblu_option['wcblu_block_ip'] = $result;
                    $wcblu_option_array = wp_json_encode( $wcblu_option );
                    update_option( 'wcblu_option', $wcblu_option_array );
                }
            }
            /**
             * Whitelist first name, last name, and phone.
             */
            if ( isset( $wcbfc_enable_whitelist_first_name ) && '1' === $wcbfc_enable_whitelist_first_name && !empty( $wcblu_settings_whitelist_first_name ) ) {
                $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
                $wcblu_option = json_decode( $wcblu_option, true );
                if ( is_array( $wcblu_option ) && !empty( $wcblu_option['wcblu_block_first_name'] ) ) {
                    $wcblu_option['wcblu_block_first_name'] = wcbfc_strip_whitelisted_names_from_block_list( $wcblu_option['wcblu_block_first_name'], $wcblu_settings_whitelist_first_name );
                    update_option( 'wcblu_option', wp_json_encode( $wcblu_option ) );
                }
            }
            if ( isset( $wcbfc_enable_whitelist_last_name ) && '1' === $wcbfc_enable_whitelist_last_name && !empty( $wcblu_settings_whitelist_last_name ) ) {
                $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
                $wcblu_option = json_decode( $wcblu_option, true );
                if ( is_array( $wcblu_option ) && !empty( $wcblu_option['wcblu_block_last_name'] ) ) {
                    $wcblu_option['wcblu_block_last_name'] = wcbfc_strip_whitelisted_names_from_block_list( $wcblu_option['wcblu_block_last_name'], $wcblu_settings_whitelist_last_name );
                    update_option( 'wcblu_option', wp_json_encode( $wcblu_option ) );
                }
            }
            if ( isset( $wcbfc_enable_whitelist_phone ) && '1' === $wcbfc_enable_whitelist_phone && !empty( $wcblu_settings_whitelist_phone ) ) {
                $wcblu_option = ( !empty( get_option( 'wcblu_option' ) ) ? get_option( 'wcblu_option' ) : '' );
                $wcblu_option = json_decode( $wcblu_option, true );
                if ( is_array( $wcblu_option ) && !empty( $wcblu_option['wcblu_block_phone'] ) ) {
                    $wcblu_option['wcblu_block_phone'] = wcbfc_strip_whitelisted_phones_from_block_list( $wcblu_option['wcblu_block_phone'], $wcblu_settings_whitelist_phone );
                    update_option( 'wcblu_option', wp_json_encode( $wcblu_option ) );
                }
            }
            $wcblugeneraloption_array['wcbfc_recaptcha_status'] = $wcbfc_recaptcha_status;
            $wcblugeneraloption_array['wcbfc_recaptcha_version'] = $wcbfc_recaptcha_version;
            $wcblugeneraloption_array['wcblu_v2_keys_value'] = $wcblu_v2_keys_value;
            $wcblugeneraloption_array['wcblu_v3_keys_value'] = $wcblu_v3_keys_value;
            $wcblugeneraloption_array['wcblu_v2_secret_keys_value'] = $wcblu_v2_secret_keys_value;
            $wcblugeneraloption_array['wcblu_v3_secret_keys_value'] = $wcblu_v3_secret_keys_value;
            $wcblugeneralopt_array = wp_json_encode( $wcblugeneraloption_array );
            update_option( 'wcblu_general_option', $wcblugeneralopt_array );
        }
        wp_safe_redirect( add_query_arg( array(
            'page'    => 'wcblu-general-settings',
            'success' => 'true',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * function for update rules settings.
     */
    public function wcblu_update_rules_settings() {
        $getformsumbitaction = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $wcblu_save_rule_settings = filter_input( INPUT_POST, 'wcblu_save_rule_settings_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $wcbluruleoption_array = array();
        /**
         * check form action
         *
         */
        if ( !empty( $getformsumbitaction ) && 'submit_general_rules_form_wcblu' === $getformsumbitaction && wp_verify_nonce( sanitize_text_field( $wcblu_save_rule_settings ), 'wcblu_save_rule_settings' ) ) {
            $wcbfc_first_order_status = filter_input( INPUT_POST, 'wcbfc_first_order_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_first_order_weight = filter_input( INPUT_POST, 'wcbfc_first_order_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_first_order_custom = filter_input( INPUT_POST, 'wcbfc_first_order_custom', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_first_order_custom_weight = filter_input( INPUT_POST, 'wcbfc_first_order_custom_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_bca_order = filter_input( INPUT_POST, 'wcbfc_bca_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_bca_order_weight = filter_input( INPUT_POST, 'wcbfc_bca_order_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_proxy_order = filter_input( INPUT_POST, 'wcbfc_proxy_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_proxy_order_weight = filter_input( INPUT_POST, 'wcbfc_proxy_order_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_international_order = filter_input( INPUT_POST, 'wcbfc_international_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_international_order_weight = filter_input( INPUT_POST, 'wcbfc_international_order_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_unknown_origin = filter_input( INPUT_POST, 'wcbfc_unknown_origin', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_unknown_origin_weight = filter_input( INPUT_POST, 'wcbfc_unknown_origin_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_suspecius_email = filter_input( INPUT_POST, 'wcbfc_suspecius_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_suspecius_email_list = filter_input(
                INPUT_POST,
                'wcbfc_suspecius_email_list',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcbfc_suspecious_email_weight = filter_input( INPUT_POST, 'wcbfc_suspecious_email_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_blocked_user_email_check = filter_input( INPUT_POST, 'wcbfc_blocked_user_email_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_blocked_user_email_weight = filter_input( INPUT_POST, 'wcbfc_blocked_user_email_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_unsafe_countries = filter_input( INPUT_POST, 'wcbfc_unsafe_countries', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcblu_define_unsafe_countries_list = filter_input(
                INPUT_POST,
                'wcblu_define_unsafe_countries_list',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcbfc_unsafe_countries_ip = filter_input( INPUT_POST, 'wcbfc_unsafe_countries_ip', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_unsafe_countries_block_others = filter_input( INPUT_POST, 'wcbfc_unsafe_countries_block_others', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_unsafe_countries_weight = filter_input( INPUT_POST, 'wcbfc_unsafe_countries_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_billing_phone_number_order = filter_input( INPUT_POST, 'wcbfc_billing_phone_number_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_billing_shipping_geo_match = filter_input( INPUT_POST, 'wcbfc_billing_shipping_geo_match', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_billing_shipping_geo_match_weight = filter_input( INPUT_POST, 'wcbfc_billing_shipping_geo_match_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_phone_number_order_weight = filter_input( INPUT_POST, 'wcbfc_billing_phone_number_order_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_ip_multiple_check = filter_input( INPUT_POST, 'wcbfc_ip_multiple_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_ip_multiple_time_span = filter_input( INPUT_POST, 'wcbfc_ip_multiple_time_span', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_ip_multiple_weight = filter_input( INPUT_POST, 'wcbfc_ip_multiple_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_order_avg_amount_check = filter_input( INPUT_POST, 'wcbfc_order_avg_amount_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_order_avg_amount_time_span = filter_input( INPUT_POST, 'wcbfc_order_avg_amount_time_span', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_order_avg_amount_weight = filter_input( INPUT_POST, 'wcbfc_order_avg_amount_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_order_amount_check = filter_input( INPUT_POST, 'wcbfc_order_amount_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_max_order_attempt_span = filter_input( INPUT_POST, 'wcbfc_max_order_attempt_span', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_max_order_attempt_weight = filter_input( INPUT_POST, 'wcbfc_max_order_attempt_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_oa_check = filter_input( INPUT_POST, 'wcbfc_too_many_oa_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_oats_attempt_span = filter_input( INPUT_POST, 'wcbfc_too_many_oats_attempt_span', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_oatos_attempt_span = filter_input( INPUT_POST, 'wcbfc_too_many_oatos_attempt_span', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_oa_attempt_weight = filter_input( INPUT_POST, 'wcbfc_too_many_oa_attempt_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_o_failed_a_check = filter_input( INPUT_POST, 'wcbfc_too_many_o_failed_a_check', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_o_failed_a_check_weight = filter_input( INPUT_POST, 'wcbfc_too_many_o_failed_a_check_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_failed_oats_attempt_try = filter_input( INPUT_POST, 'wcbfc_too_many_failed_oats_attempt_try', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_too_many_failed_oats_strings = filter_input( INPUT_POST, 'wcbfc_too_many_failed_oats_strings', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_no_of_allow_order_between_time = filter_input( INPUT_POST, 'wcbfc_no_of_allow_order_between_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_no_of_allow_order_between_time_weight = filter_input( INPUT_POST, 'wcbfc_no_of_allow_order_between_time_weight', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_maximum_allowed_number_of_orders_between_time = filter_input( INPUT_POST, 'wcbfc_maximum_allowed_number_of_orders_between_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_start_time_timepicker = filter_input( INPUT_POST, 'wcbfc_start_time_timepicker', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_end_time_timepicker = filter_input( INPUT_POST, 'wcbfc_end_time_timepicker', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_geo_match_key = filter_input( INPUT_POST, 'wcbfc_geo_match_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $wcbfc_first_order_status = ( empty( $wcbfc_first_order_status ) ? '0' : $wcbfc_first_order_status );
            $wcbfc_first_order_weight = ( empty( $wcbfc_first_order_weight ) ? '0' : $wcbfc_first_order_weight );
            $wcbfc_first_order_custom = ( empty( $wcbfc_first_order_custom ) ? '0' : $wcbfc_first_order_custom );
            $wcbfc_first_order_custom_weight = ( empty( $wcbfc_first_order_custom_weight ) ? '0' : $wcbfc_first_order_custom_weight );
            $wcbfc_bca_order = ( empty( $wcbfc_bca_order ) ? '0' : $wcbfc_bca_order );
            $wcbfc_bca_order_weight = ( empty( $wcbfc_bca_order_weight ) ? '0' : $wcbfc_bca_order_weight );
            $wcbfc_proxy_order = ( empty( $wcbfc_proxy_order ) ? '0' : $wcbfc_proxy_order );
            $wcbfc_proxy_order_weight = ( empty( $wcbfc_proxy_order_weight ) ? '0' : $wcbfc_proxy_order_weight );
            $wcbfc_international_order = ( empty( $wcbfc_international_order ) ? '0' : $wcbfc_international_order );
            $wcbfc_international_order_weight = ( empty( $wcbfc_international_order_weight ) ? '0' : $wcbfc_international_order_weight );
            $wcbfc_unknown_origin = ( empty( $wcbfc_unknown_origin ) ? '0' : $wcbfc_unknown_origin );
            $wcbfc_unknown_origin_weight = ( empty( $wcbfc_unknown_origin_weight ) ? '0' : $wcbfc_unknown_origin_weight );
            $wcbfc_suspecius_email = ( empty( $wcbfc_suspecius_email ) ? '0' : $wcbfc_suspecius_email );
            $wcbfc_suspecius_email_list = ( empty( $wcbfc_suspecius_email_list ) ? array() : $wcbfc_suspecius_email_list );
            $wcbfc_suspecious_email_weight = ( empty( $wcbfc_suspecious_email_weight ) ? '0' : $wcbfc_suspecious_email_weight );
            $wcbfc_blocked_user_email_check = ( empty( $wcbfc_blocked_user_email_check ) ? '0' : $wcbfc_blocked_user_email_check );
            $wcbfc_blocked_user_email_weight = ( empty( $wcbfc_blocked_user_email_weight ) ? '0' : $wcbfc_blocked_user_email_weight );
            $wcbfc_unsafe_countries = ( empty( $wcbfc_unsafe_countries ) ? '0' : $wcbfc_unsafe_countries );
            $wcbfc_unsafe_countries_ip = ( empty( $wcbfc_unsafe_countries_ip ) ? '0' : $wcbfc_unsafe_countries_ip );
            $wcbfc_unsafe_countries_block_others = ( empty( $wcbfc_unsafe_countries_block_others ) ? '0' : $wcbfc_unsafe_countries_block_others );
            $wcblu_define_unsafe_countries_list = ( empty( $wcblu_define_unsafe_countries_list ) ? array() : $wcblu_define_unsafe_countries_list );
            $wcbfc_unsafe_countries_weight = ( empty( $wcbfc_unsafe_countries_weight ) ? '0' : $wcbfc_unsafe_countries_weight );
            $wcbfc_billing_phone_number_order = ( empty( $wcbfc_billing_phone_number_order ) ? '0' : $wcbfc_billing_phone_number_order );
            $wcbfc_billing_shipping_geo_match = ( empty( $wcbfc_billing_shipping_geo_match ) ? '0' : $wcbfc_billing_shipping_geo_match );
            $wcbfc_billing_shipping_geo_match_weight = ( empty( $wcbfc_billing_shipping_geo_match_weight ) ? '0' : $wcbfc_billing_shipping_geo_match_weight );
            $wcbfc_phone_number_order_weight = ( empty( $wcbfc_phone_number_order_weight ) ? '0' : $wcbfc_phone_number_order_weight );
            $wcbfc_ip_multiple_check = ( empty( $wcbfc_ip_multiple_check ) ? '0' : $wcbfc_ip_multiple_check );
            $wcbfc_ip_multiple_time_span = ( empty( $wcbfc_ip_multiple_time_span ) ? '30' : $wcbfc_ip_multiple_time_span );
            $wcbfc_ip_multiple_weight = ( empty( $wcbfc_ip_multiple_weight ) ? '0' : $wcbfc_ip_multiple_weight );
            $wcbfc_order_avg_amount_check = ( empty( $wcbfc_order_avg_amount_check ) ? '0' : $wcbfc_order_avg_amount_check );
            $wcbfc_order_avg_amount_time_span = ( empty( $wcbfc_order_avg_amount_time_span ) ? '2' : $wcbfc_order_avg_amount_time_span );
            $wcbfc_order_avg_amount_weight = ( empty( $wcbfc_order_avg_amount_weight ) ? '0' : $wcbfc_order_avg_amount_weight );
            $wcbfc_order_amount_check = ( empty( $wcbfc_order_amount_check ) ? '0' : $wcbfc_order_amount_check );
            $wcbfc_max_order_attempt_span = ( empty( $wcbfc_max_order_attempt_span ) ? '0' : $wcbfc_max_order_attempt_span );
            $wcbfc_max_order_attempt_weight = ( empty( $wcbfc_max_order_attempt_weight ) ? '0' : $wcbfc_max_order_attempt_weight );
            $wcbfc_too_many_oa_check = ( empty( $wcbfc_too_many_oa_check ) ? '0' : $wcbfc_too_many_oa_check );
            $wcbfc_too_many_oats_attempt_span = ( empty( $wcbfc_too_many_oats_attempt_span ) ? '24' : $wcbfc_too_many_oats_attempt_span );
            $wcbfc_too_many_oatos_attempt_span = ( empty( $wcbfc_too_many_oatos_attempt_span ) ? '5' : $wcbfc_too_many_oatos_attempt_span );
            $wcbfc_too_many_oa_attempt_weight = ( empty( $wcbfc_too_many_oa_attempt_weight ) ? '0' : $wcbfc_too_many_oa_attempt_weight );
            $wcbfc_too_many_o_failed_a_check = ( empty( $wcbfc_too_many_o_failed_a_check ) ? '0' : $wcbfc_too_many_o_failed_a_check );
            $wcbfc_too_many_o_failed_a_check_weight = ( empty( $wcbfc_too_many_o_failed_a_check_weight ) ? '0' : $wcbfc_too_many_o_failed_a_check_weight );
            $wcbfc_too_many_failed_oats_attempt_try = ( empty( $wcbfc_too_many_failed_oats_attempt_try ) ? '0' : $wcbfc_too_many_failed_oats_attempt_try );
            $wcbfc_too_many_failed_oats_strings = ( empty( $wcbfc_too_many_failed_oats_strings ) ? '' : $wcbfc_too_many_failed_oats_strings );
            $wcbfc_no_of_allow_order_between_time = ( empty( $wcbfc_no_of_allow_order_between_time ) ? '0' : $wcbfc_no_of_allow_order_between_time );
            $wcbfc_no_of_allow_order_between_time_weight = ( empty( $wcbfc_no_of_allow_order_between_time_weight ) ? '0' : $wcbfc_no_of_allow_order_between_time_weight );
            $wcbfc_maximum_allowed_number_of_orders_between_time = ( empty( $wcbfc_maximum_allowed_number_of_orders_between_time ) ? '0' : $wcbfc_maximum_allowed_number_of_orders_between_time );
            $wcbfc_start_time_timepicker = ( empty( $wcbfc_start_time_timepicker ) ? '0' : $wcbfc_start_time_timepicker );
            $wcbfc_end_time_timepicker = ( empty( $wcbfc_end_time_timepicker ) ? '0' : $wcbfc_end_time_timepicker );
            $wcbfc_geo_match_key = ( empty( $wcbfc_geo_match_key ) ? '0' : $wcbfc_geo_match_key );
            $wcbluruleoption_array['wcbfc_first_order_status'] = $wcbfc_first_order_status;
            $wcbluruleoption_array['wcbfc_first_order_weight'] = $wcbfc_first_order_weight;
            $wcbluruleoption_array['wcbfc_first_order_custom'] = $wcbfc_first_order_custom;
            $wcbluruleoption_array['wcbfc_first_order_custom_weight'] = $wcbfc_first_order_custom_weight;
            $wcbluruleoption_array['wcbfc_bca_order'] = $wcbfc_bca_order;
            $wcbluruleoption_array['wcbfc_bca_order_weight'] = $wcbfc_bca_order_weight;
            $wcbluruleoption_array['wcbfc_proxy_order'] = $wcbfc_proxy_order;
            $wcbluruleoption_array['wcbfc_proxy_order_weight'] = $wcbfc_proxy_order_weight;
            $wcbluruleoption_array['wcbfc_international_order'] = $wcbfc_international_order;
            $wcbluruleoption_array['wcbfc_international_order_weight'] = $wcbfc_international_order_weight;
            $wcbluruleoption_array['wcbfc_unknown_origin'] = $wcbfc_unknown_origin;
            $wcbluruleoption_array['wcbfc_unknown_origin_weight'] = $wcbfc_unknown_origin_weight;
            $wcbluruleoption_array['wcbfc_suspecius_email'] = $wcbfc_suspecius_email;
            $wcbluruleoption_array['wcbfc_suspecius_email_list'] = $wcbfc_suspecius_email_list;
            $wcbluruleoption_array['wcbfc_suspecious_email_weight'] = $wcbfc_suspecious_email_weight;
            $wcbluruleoption_array['wcbfc_blocked_user_email_check'] = $wcbfc_blocked_user_email_check;
            $wcbluruleoption_array['wcbfc_blocked_user_email_weight'] = $wcbfc_blocked_user_email_weight;
            $wcbluruleoption_array['wcbfc_unsafe_countries'] = $wcbfc_unsafe_countries;
            $wcbluruleoption_array['wcbfc_unsafe_countries_ip'] = $wcbfc_unsafe_countries_ip;
            $wcbluruleoption_array['wcbfc_unsafe_countries_block_others'] = $wcbfc_unsafe_countries_block_others;
            $wcbluruleoption_array['wcblu_define_unsafe_countries_list'] = $wcblu_define_unsafe_countries_list;
            $wcbluruleoption_array['wcbfc_unsafe_countries_weight'] = $wcbfc_unsafe_countries_weight;
            $wcbluruleoption_array['wcbfc_billing_phone_number_order'] = $wcbfc_billing_phone_number_order;
            $wcbluruleoption_array['wcbfc_billing_phone_number_order_weight'] = $wcbfc_phone_number_order_weight;
            $wcbluruleoption_array['wcbfc_billing_shipping_geo_match'] = $wcbfc_billing_shipping_geo_match;
            $wcbluruleoption_array['wcbfc_billing_shipping_geo_match_weight'] = $wcbfc_billing_shipping_geo_match_weight;
            $wcbluruleoption_array['wcbfc_ip_multiple_check'] = $wcbfc_ip_multiple_check;
            $wcbluruleoption_array['wcbfc_ip_multiple_time_span'] = $wcbfc_ip_multiple_time_span;
            $wcbluruleoption_array['wcbfc_ip_multiple_weight'] = $wcbfc_ip_multiple_weight;
            $wcbluruleoption_array['wcbfc_order_avg_amount_check'] = $wcbfc_order_avg_amount_check;
            $wcbluruleoption_array['wcbfc_order_avg_amount_time_span'] = $wcbfc_order_avg_amount_time_span;
            $wcbluruleoption_array['wcbfc_order_avg_amount_weight'] = $wcbfc_order_avg_amount_weight;
            $wcbluruleoption_array['wcbfc_order_amount_check'] = $wcbfc_order_amount_check;
            $wcbluruleoption_array['wcbfc_max_order_attempt_span'] = $wcbfc_max_order_attempt_span;
            $wcbluruleoption_array['wcbfc_max_order_attempt_weight'] = $wcbfc_max_order_attempt_weight;
            $wcbluruleoption_array['wcbfc_too_many_oa_check'] = $wcbfc_too_many_oa_check;
            $wcbluruleoption_array['wcbfc_too_many_oats_attempt_span'] = $wcbfc_too_many_oats_attempt_span;
            $wcbluruleoption_array['wcbfc_too_many_oatos_attempt_span'] = $wcbfc_too_many_oatos_attempt_span;
            $wcbluruleoption_array['wcbfc_too_many_oa_attempt_weight'] = $wcbfc_too_many_oa_attempt_weight;
            $wcbluruleoption_array['wcbfc_too_many_o_failed_a_check'] = $wcbfc_too_many_o_failed_a_check;
            $wcbluruleoption_array['wcbfc_too_many_o_failed_a_check_weight'] = $wcbfc_too_many_o_failed_a_check_weight;
            $wcbluruleoption_array['wcbfc_too_many_failed_oats_attempt_try'] = $wcbfc_too_many_failed_oats_attempt_try;
            $wcbluruleoption_array['wcbfc_too_many_failed_oats_strings'] = $wcbfc_too_many_failed_oats_strings;
            $wcbluruleoption_array['wcbfc_no_of_allow_order_between_time'] = $wcbfc_no_of_allow_order_between_time;
            $wcbluruleoption_array['wcbfc_no_of_allow_order_between_time_weight'] = $wcbfc_no_of_allow_order_between_time_weight;
            $wcbluruleoption_array['wcbfc_maximum_allowed_number_of_orders_between_time'] = $wcbfc_maximum_allowed_number_of_orders_between_time;
            $wcbluruleoption_array['wcbfc_start_time_timepicker'] = $wcbfc_start_time_timepicker;
            $wcbluruleoption_array['wcbfc_end_time_timepicker'] = $wcbfc_end_time_timepicker;
            $wcbluruleoption_array['wcbfc_geo_match_key'] = $wcbfc_geo_match_key;
            $wcbluruleopt_array = wp_json_encode( $wcbluruleoption_array );
            update_option( 'wcblu_rules_option', $wcbluruleopt_array );
        }
        wp_safe_redirect( add_query_arg( array(
            'page'    => 'wcblu-auto-rules',
            'success' => 'true',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Apply a One Click Setup preset from the Rules page.
     *
     * @return void
     */
    public function wcblu_apply_one_click_setup() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'You are not allowed to update these settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        if ( !function_exists( 'wcbfc_apply_one_click_preset' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'Setup is not available.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        $preset = ( isset( $_POST['preset'] ) ? sanitize_key( wp_unslash( $_POST['preset'] ) ) : '' );
        $result = wcbfc_apply_one_click_preset( $preset );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message(),
            ) );
        }
        wp_send_json_success( array(
            'message' => esc_html__( 'Setup applied. Reloading rules…', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ) );
    }

    /**
     * Add risk column.
     */
    public function wcblu_add_column( $columns ) {
        $getGeneralSettings = get_option( 'wcblu_general_option' );
        $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
        $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
        $wcbfc_fraud_check_status = ( empty( $getGeneralSettingsArray['wcbfc_fraud_check_status'] ) ? 'off' : $getGeneralSettingsArray['wcbfc_fraud_check_status'] );
        $reordered_columns = array();
        foreach ( $columns as $key => $column ) {
            $reordered_columns[$key] = $column;
            if ( 'order_status' === $key ) {
                // Inserting after "Status" column.
                if ( !empty( $wcbfc_fraud_check_status ) && 'on' === $wcbfc_fraud_check_status ) {
                    $reordered_columns['wcblu_anti_fraud'] = 'Risk Status';
                }
            }
        }
        $columns = $reordered_columns;
        return $columns;
    }

    /**
     * Add risk column.
     */
    public function edd_wcblu_add_column( $columns ) {
        $getGeneralSettings = get_option( 'wcblu_general_option' );
        $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
        $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
        $wcbfc_fraud_check_status = ( empty( $getGeneralSettingsArray['wcbfc_fraud_check_status'] ) ? 'off' : $getGeneralSettingsArray['wcbfc_fraud_check_status'] );
        $reordered_columns = array();
        if ( isset( $wcbfc_fraud_check_status ) && 'on' === $wcbfc_fraud_check_status ) {
            foreach ( $columns as $key => $column ) {
                $reordered_columns[$key] = $column;
                if ( 'status' === $key ) {
                    // Inserting after "Status" column.
                    $reordered_columns['edd_wcblu_anti_frauds'] = 'Risk Status';
                }
            }
            $columns = $reordered_columns;
        }
        return $columns;
    }

    /**
     * Render column.
     */
    public function wcblu_render_column( $column, $order ) {
        $order_id = $order->get_id();
        $fscore = 0;
        if ( 'wcblu_anti_fraud' === $column ) {
            if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
                $fscore = floatval( $order->get_meta( 'wcbfc_order_score', true ) );
                $wcbfc_whitelisted_email = $order->get_meta( 'wcbfc_whitelisted_email', true );
            } else {
                $fscore = floatval( get_post_meta( $order_id, 'wcbfc_order_score', true ) );
                $wcbfc_whitelisted_email = get_post_meta( $order_id, 'wcbfc_whitelisted_email', true );
            }
            $fscore = ( empty( $fscore ) ? 0 : $fscore );
            $getGeneralSettings = get_option( 'wcblu_general_option' );
            $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
            $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
            $mediumRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
            $highRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
        }
        if ( 'wcblu_ai_anti_fraud' === $column ) {
            if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
                $fscore = floatval( $order->get_meta( '_wcblu_ai_score', true ) );
                $highRisk = floatval( $order->get_meta( '_wcblu_ai_threshold', true ) );
            } else {
                $fscore = floatval( get_post_meta( $order_id, '_wcblu_ai_score', true ) );
                $highRisk = floatval( get_post_meta( $order_id, '_wcblu_ai_threshold', true ) );
            }
            $mediumRisk = ceil( $highRisk * 33 / 100 );
        }
        if ( $fscore >= 0 && in_array( $column, array('wcblu_anti_fraud', 'wcblu_ai_anti_fraud'), true ) ) {
            $allow_html_args = array(
                'span' => array(
                    'class' => array(),
                ),
            );
            $whitelisted = ( 'wcblu_anti_fraud' === $column && isset( $wcbfc_whitelisted_email ) ? $wcbfc_whitelisted_email : '' );
            $label = $this->wcblu_get_risk_badge_html(
                $fscore,
                $mediumRisk,
                $highRisk,
                $whitelisted
            );
            if ( 'wcblu_anti_fraud' === $column ) {
                $label .= $this->wcblu_get_monitor_badge_html( $order );
            }
            echo wp_kses( $label, $allow_html_args );
        }
    }

    /**
     * Build the Risk Status badge markup for the order list column.
     *
     * @param float  $fscore      Fraud score.
     * @param float  $medium_risk Medium risk threshold.
     * @param float  $high_risk   High risk threshold.
     * @param string $whitelisted Whitelisted email, when the order bypassed the fraud checks.
     *
     * @return string
     */
    private function wcblu_get_risk_badge_html(
        $fscore,
        $medium_risk,
        $high_risk,
        $whitelisted = ''
    ) {
        if ( !empty( $whitelisted ) ) {
            return '<span class="wcblu-risk-badge whitelisted"><span class="wcblu-risk-dot"></span>' . esc_html__( 'Whitelisted', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '</span>';
        }
        $fscore = floatval( $fscore );
        $medium_risk = floatval( $medium_risk );
        $high_risk = floatval( $high_risk );
        if ( $fscore > 0 && $fscore <= $medium_risk ) {
            $level = 'low';
            $text = esc_html__( 'Low', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        } else {
            if ( $fscore > $medium_risk && $fscore <= $high_risk ) {
                $level = 'medium';
                $text = esc_html__( 'Medium', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            } else {
                if ( $fscore > $high_risk ) {
                    $level = 'high';
                    $text = esc_html__( 'High', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                } else {
                    $level = 'empty';
                    $text = '';
                }
            }
        }
        $display_score = min( 100, max( 0, (int) round( $fscore ) ) );
        $label = sprintf( '%d%%', $display_score );
        $label = ( '' === $text ? $label : $label . ' ' . $text );
        return '<span class="wcblu-risk-badge ' . esc_attr( $level ) . '"><span class="wcblu-risk-dot"></span>' . esc_html( $label ) . '</span>';
    }

    /**
     * Badge shown when the order was scored in Monitor Only mode.
     *
     * @param mixed $order Order object or post ID.
     * @return string
     */
    private function wcblu_get_monitor_badge_html( $order ) {
        $mode = '';
        if ( $order instanceof WC_Order ) {
            $mode = $order->get_meta( 'wcbfc_protection_mode', true );
        } elseif ( is_numeric( $order ) ) {
            $mode = get_post_meta( (int) $order, 'wcbfc_protection_mode', true );
        }
        if ( 'monitor' !== $mode ) {
            return '';
        }
        return '<span class="wcblu-risk-badge monitored">' . esc_html__( 'Monitored', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '</span>';
    }

    /**
     * Render column.
     */
    public function wcblu_render_column_post_table( $column ) {
        global $post;
        $fscore = 0;
        if ( 'wcblu_anti_fraud' === $column ) {
            $fscore = floatval( get_post_meta( $post->ID, 'wcbfc_order_score', true ) );
            $fscore = ( empty( $fscore ) ? 0 : $fscore );
            $getGeneralSettings = get_option( 'wcblu_general_option' );
            $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
            $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
            $wcbfc_whitelisted_email = get_post_meta( $post->ID, 'wcbfc_whitelisted_email', true );
            $mediumRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
            $highRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
        }
        if ( 'wcblu_ai_anti_fraud' === $column ) {
            $fscore = floatval( get_post_meta( $post->ID, '_wcblu_ai_score', true ) );
            $highRisk = floatval( get_post_meta( $post->ID, '_wcblu_ai_threshold', true ) );
            $mediumRisk = ceil( $highRisk * 33 / 100 );
        }
        if ( $fscore >= 0 && in_array( $column, array('wcblu_anti_fraud', 'wcblu_ai_anti_fraud'), true ) ) {
            $allow_html_args = array(
                'span' => array(
                    'class' => array(),
                ),
            );
            $whitelisted = ( 'wcblu_anti_fraud' === $column && isset( $wcbfc_whitelisted_email ) ? $wcbfc_whitelisted_email : '' );
            $label = $this->wcblu_get_risk_badge_html(
                $fscore,
                $mediumRisk,
                $highRisk,
                $whitelisted
            );
            if ( 'wcblu_anti_fraud' === $column ) {
                $label .= $this->wcblu_get_monitor_badge_html( $post->ID );
            }
            echo wp_kses( $label, $allow_html_args );
        }
    }

    /**
     * Render column.
     */
    public function edd_wcblu_render_column_post_table( $value, $payment_ID, $column_name ) {
        if ( 'edd_wcblu_anti_frauds' === $column_name ) {
            $fscore = floatval( get_post_meta( $payment_ID, 'edd_wcbfc_order_score', true ) );
            $fscore = ( empty( $fscore ) ? 0 : $fscore );
            $getGeneralSettings = get_option( 'wcblu_general_option' );
            $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
            $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
            $wcbfc_whitelisted_email = get_post_meta( $payment_ID, 'wcbfc_whitelisted_email', true );
            $mediumRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
            $heighRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
            $allow_html_args = array(
                'span' => array(
                    'class' => array(),
                ),
            );
            if ( $fscore > 0 && $fscore <= $mediumRisk ) {
                $label = '<span class="dashicons dashicons-shield-alt low"></span>';
            } else {
                if ( $fscore > $mediumRisk && $fscore <= $heighRisk ) {
                    $label = '<span class="dashicons dashicons-shield-alt medium"></span>';
                } else {
                    if ( $fscore > $heighRisk ) {
                        $label = '<span class="dashicons dashicons-shield-alt high"></span>';
                    } else {
                        $label = '<span class="dashicons dashicons-shield-alt empty"></span>';
                    }
                }
            }
            if ( isset( $wcbfc_whitelisted_email ) && !empty( $wcbfc_whitelisted_email ) ) {
                $label = '<span class="dashicons dashicons-shield-alt wh-empty"></span>';
            }
            $value = wp_kses( $label, $allow_html_args );
        }
        return $value;
    }

    /**
     * Score widget in order 
     */
    public function wcblu_add_meta_boxes( $post_type, $post ) {
        $order = ( $post instanceof WP_Post ? wc_get_order( $post->ID ) : $post );
        if ( 'shop_order' === $post_type || 'woocommerce_page_wc-orders' === $post_type ) {
            if ( !$order instanceof WC_Order ) {
                return;
            }
            $getGeneralSettings = get_option( 'wcblu_general_option' );
            $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
            $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
            $wcbfc_fraud_check_status = ( empty( $getGeneralSettingsArray['wcbfc_fraud_check_status'] ) ? 'off' : $getGeneralSettingsArray['wcbfc_fraud_check_status'] );
            if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
                $order_score = $order->get_meta( 'wcbfc_order_score', true );
            } else {
                $order_score = get_post_meta( $order->get_id(), 'wcbfc_order_score', true );
            }
            if ( class_exists( "Automattic\\WooCommerce\\Internal\\DataStores\\Orders\\CustomOrdersTableController" ) ) {
                $screen = ( wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order' );
            } else {
                $screen = 'shop_order';
            }
            if ( isset( $wcbfc_fraud_check_status ) && 'on' === $wcbfc_fraud_check_status && "" !== $order_score ) {
                add_meta_box(
                    'wcblu-meta-box-id',
                    esc_html__( 'Fraud Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    array($this, 'wcblu_fc_meta_box_callback'),
                    $screen,
                    'side',
                    'high'
                );
            }
            // Block order widget
            add_meta_box(
                'wcblu-bo-meta-box-id',
                esc_html__( 'Fraud Prevention - actions', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                array($this, 'wcblu_bo_meta_box_callback'),
                $screen,
                'side',
                'high'
            );
        }
    }

    function wcblu_fc_meta_box_callback( $order ) {
        if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
            // HPOS usage is enabled.
            $order_id = $order->get_id();
        } else {
            // Traditional CPT-based orders are in use.
            $order_id = $order->ID;
        }
        $getGeneralSettings = get_option( 'wcblu_general_option' );
        $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
        $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
        $mediumRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
        $heighRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
        if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $wcbfc_score_arry = $order->get_meta( 'wcbfc_score', true );
            $wcbfc_score_msg_arry = $order->get_meta( 'wcbfc_score_msg', true );
            $wcbfc_score_details = $order->get_meta( 'wcbfc_score_details', true );
            $wcbfc_whitelisted_email = $order->get_meta( 'wcbfc_whitelisted_email', true );
            $order_score = $order->get_meta( 'wcbfc_order_score', true );
        } else {
            $wcbfc_score_arry = get_post_meta( $order_id, 'wcbfc_score', true );
            $wcbfc_score_msg_arry = get_post_meta( $order_id, 'wcbfc_score_msg', true );
            $wcbfc_score_details = get_post_meta( $order_id, 'wcbfc_score_details', true );
            $wcbfc_whitelisted_email = get_post_meta( $order_id, 'wcbfc_whitelisted_email', true );
            $order_score = get_post_meta( $order_id, 'wcbfc_order_score', true );
        }
        $wcbfc_score_arry = ( empty( $wcbfc_score_arry ) ? array() : $wcbfc_score_arry );
        $wcbfc_score_msg_arry = ( empty( $wcbfc_score_msg_arry ) ? array() : $wcbfc_score_msg_arry );
        $wcbfc_score_details = ( isset( $wcbfc_score_details ) && is_array( $wcbfc_score_details ) ? $wcbfc_score_details : array() );
        $fscore = 0;
        $color = '#ccc';
        $label = 'risk';
        $is_whitelisted = isset( $wcbfc_whitelisted_email ) && !empty( $wcbfc_whitelisted_email );
        $order_obj = ( $order instanceof WC_Order ? $order : wc_get_order( $order_id ) );
        if ( '0' === $order_score ) {
            $fscore = '0';
        } else {
            foreach ( $wcbfc_score_arry as $key => $val ) {
                $fscore = $fscore + (int) $val;
            }
        }
        $fscore = ( $fscore >= 100 ? 100 : $fscore );
        if ( $fscore > 0 && $fscore <= $mediumRisk ) {
            $color = '#7AD03A';
            $label = esc_html__( 'Low Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        } else {
            if ( $fscore >= $mediumRisk && $fscore <= $heighRisk ) {
                $color = '#FFAE00';
                $label = esc_html__( 'Medium Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            } else {
                if ( $fscore >= $heighRisk ) {
                    $color = '#D54E21';
                    $label = esc_html__( 'High Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                }
            }
        }
        if ( $is_whitelisted ) {
            $color = '#4f575d';
        }
        $rule_ui_map = ( function_exists( 'wcbfc_get_fraud_rule_ui_map' ) ? wcbfc_get_fraud_rule_ui_map() : array() );
        $visible_limit = 2;
        $rule_index = 0;
        $hidden_count = max( 0, count( $wcbfc_score_arry ) - $visible_limit );
        ?>
		<div class="wcblu-fc-score-wrap">
			<div class="wcblu-fc-score-text" style="color:<?php 
        echo esc_attr( $color );
        ?>;">
				<span class="wcblu-fc-score-pct"><?php 
        echo esc_html( (string) $fscore );
        ?>%</span>
				<span class="wcblu-fc-score-label"><?php 
        echo esc_html( $label );
        ?></span>
			</div>
			<div class="wcbfc_position">
				<input class="wcblu_chart" data-width="80" data-height="80" data-fgColor="<?php 
        echo esc_attr( $color );
        ?>" data-bgColor="#f0f0f1" data-thickness=".18" data-displayInput="true" data-readOnly=true value="0" rel="<?php 
        echo esc_attr( $fscore );
        ?>">
			</div>
		</div>
		<?php 
        $monitor_badge = $this->wcblu_get_monitor_badge_html( ( $order instanceof WC_Order ? $order : $order_id ) );
        if ( $monitor_badge ) {
            echo '<p class="wcblu-monitor-note">' . wp_kses( $monitor_badge, array(
                'span' => array(
                    'class' => array(),
                ),
            ) ) . '</p>';
        }
        if ( $is_whitelisted ) {
            echo '<p class="wcblu-fc-whitelist-note">' . esc_html( $wcbfc_whitelisted_email ) . '</p>';
        }
        ?>
		<div class="mb-score-label-list wcblu-fc-rules<?php 
        echo ( $is_whitelisted ? ' is-whitelisted' : '' );
        ?>">
			<?php 
        if ( !empty( $wcbfc_score_arry ) ) {
            ?>
				<strong class="wcblu-fc-rules-title"><?php 
            echo esc_html__( 'Matched fraud rules', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></strong>
				<?php 
            foreach ( $wcbfc_score_arry as $rule_key => $rule_weight ) {
                $rule_index++;
                $ui = ( isset( $rule_ui_map[$rule_key] ) ? $rule_ui_map[$rule_key] : array() );
                $title = ( isset( $ui['title'] ) ? $ui['title'] : (( isset( $wcbfc_score_msg_arry[$rule_key] ) ? $wcbfc_score_msg_arry[$rule_key] : $rule_key )) );
                $description = ( isset( $ui['description'] ) ? $ui['description'] : '' );
                $icon = ( isset( $ui['icon'] ) ? $ui['icon'] : 'dashicons-shield' );
                $details = ( isset( $wcbfc_score_details[$rule_key] ) && is_array( $wcbfc_score_details[$rule_key] ) ? $wcbfc_score_details[$rule_key] : array() );
                if ( empty( $details ) && $order_obj && function_exists( 'wcbfc_get_fraud_score_rule_details' ) ) {
                    $details = wcbfc_get_fraud_score_rule_details( $order_obj, $rule_key );
                }
                $is_hidden = $rule_index > $visible_limit;
                ?>
					<div class="wcblu-fc-rule<?php 
                echo ( $is_hidden ? ' wcblu-fc-rule-more' : '' );
                ?>"<?php 
                echo ( $is_hidden ? ' style="display:none;"' : '' );
                ?>>
						<div class="wcblu-fc-rule-head">
							<span class="dashicons <?php 
                echo esc_attr( $icon );
                ?>" aria-hidden="true"></span>
							<div class="wcblu-fc-rule-copy">
								<strong><?php 
                echo esc_html( $title );
                ?></strong>
								<?php 
                if ( '' !== $description ) {
                    ?>
									<p><?php 
                    echo esc_html( $description );
                    ?></p>
								<?php 
                }
                ?>
								<?php 
                if ( !empty( $details ) ) {
                    ?>
									<div class="wcblu-fc-rule-details">
										<?php 
                    foreach ( $details as $detail_label => $detail_value ) {
                        ?>
											<div><strong><?php 
                        echo esc_html( $detail_label );
                        ?>:</strong> <?php 
                        echo esc_html( html_entity_decode( wp_strip_all_tags( (string) $detail_value ), ENT_QUOTES ) );
                        ?></div>
										<?php 
                    }
                    ?>
									</div>
								<?php 
                }
                ?>
							</div>
							<span class="wcblu-fc-rule-weight">+<?php 
                echo esc_html( (string) (int) $rule_weight );
                ?>%</span>
						</div>
					</div>
					<?php 
            }
            if ( $hidden_count > 0 ) {
                ?>
					<button type="button" class="button wcblu-fc-details-toggle" data-show-text="<?php 
                esc_attr_e( 'Show more', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?>" data-hide-text="<?php 
                esc_attr_e( 'Hide details', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?>">
						<span class="wcblu-toggle-text"><?php 
                esc_html_e( 'Show more', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></span>
						<span class="dashicons dashicons-arrow-down-alt2 wcblu-toggle-icon" aria-hidden="true"></span>
					</button>
					<?php 
            }
        }
        ?>
		</div>
	
		<script type="text/javascript">
			jQuery('#wcblu-meta-box-id .wcblu_chart').knob();
			var data = jQuery('#wcblu-meta-box-id .wcblu_chart').attr('rel');
			jQuery({value: 0}).animate({value: data}, {
				duration: 3000,
				easing  : 'swing',
				step    : function () {
					jQuery('#wcblu-meta-box-id .wcblu_chart').val(Math.ceil(this.value)).trigger('change');
				}
			});
			jQuery('#wcblu-meta-box-id .wcblu-fc-details-toggle').off('click').on('click', function(e) {
				e.preventDefault();
				var $button = jQuery(this);
				var $content = $button.closest('.mb-score-label-list').find('.wcblu-fc-rule-more');
				var $text = $button.find('.wcblu-toggle-text');
				var $icon = $button.find('.wcblu-toggle-icon');
				$content.slideToggle(200);
				setTimeout(function() {
					if ($content.filter(':visible').length) {
						$text.text($button.data('hide-text') || 'Hide details');
						$icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
					} else {
						$text.text($button.data('show-text') || 'Show more');
						$icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
					}
				}, 250);
			});
		</script>
		<?php 
    }

    /**
     * Block order widget in order 
     */
    public function wcblu_bo_meta_box_callback( $order ) {
        $is_pro = function_exists( 'wbpfoabfc_fs' ) && wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code();
        if ( is_a( $order, 'WC_Order' ) ) {
            $order_id = $order->get_id();
        } elseif ( isset( $order->ID ) ) {
            $order_id = $order->ID;
        } else {
            return;
        }
        $order = wc_get_order( $order_id );
        if ( !is_a( $order, 'WC_Order' ) ) {
            return;
        }
        $fields = wcbfc_get_order_blockable_fields( $order );
        $wcblu_option = json_decode( get_option( 'wcblu_option', '' ), true );
        $wcblu_option = ( is_array( $wcblu_option ) ? $wcblu_option : array() );
        $is_blocked = false;
        $field_state = array();
        foreach ( $fields as $key => $field ) {
            $list = ( isset( $wcblu_option[$field['list_key']] ) && is_array( $wcblu_option[$field['list_key']] ) ? $wcblu_option[$field['list_key']] : array() );
            $has_value = '' !== $field['value'];
            $already_blocked = $has_value && wcbfc_is_value_in_block_list( $field['value'], $list, $field['match'] );
            if ( $already_blocked ) {
                $is_blocked = true;
            }
            $field_state[$key] = array(
                'checked'  => $already_blocked || $has_value && !empty( $field['default'] ),
                'disabled' => !$has_value,
            );
        }
        if ( !$is_pro ) {
            $is_blocked = true;
            $fields = array(
                'first_name' => array(
                    'label' => __( 'First Name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'value' => 'John',
                ),
                'last_name'  => array(
                    'label' => __( 'Last Name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'value' => 'Doe',
                ),
                'email'      => array(
                    'label' => __( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'value' => 'dummy@dummy.com',
                ),
                'ip'         => array(
                    'label' => __( 'IP Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                    'value' => '0.0.0.0',
                ),
            );
            $field_state = array(
                'first_name' => array(
                    'checked'  => false,
                    'disabled' => false,
                ),
                'last_name'  => array(
                    'checked'  => false,
                    'disabled' => false,
                ),
                'email'      => array(
                    'checked'  => false,
                    'disabled' => false,
                ),
                'ip'         => array(
                    'checked'  => false,
                    'disabled' => false,
                ),
            );
        }
        $wrapper_class = ( $is_pro ? 'wcblu-bo-widget' : 'wcblu-bo-widget wcblu-upgrade-pro-to-unlock' );
        ?>
		<div class="<?php 
        echo esc_attr( $wrapper_class );
        ?>" data-order-id="<?php 
        echo esc_attr( (string) $order_id );
        ?>">
			<?php 
        if ( !$is_pro ) {
            ?>
				<div class="wcblu-pro-label"></div>
			<?php 
        }
        ?>
			<?php 
        if ( $is_blocked ) {
            ?>
				<div class="wcblu-bo-section wcblu-bo-section-unblock">
					<div class="wcblu-bo-section-head">
						<span class="dashicons dashicons-unlock" aria-hidden="true"></span>
						<strong><?php 
            echo esc_html__( 'Unblock Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></strong>
					</div>
					<p><?php 
            echo esc_html__( 'Remove customer details from blacklist and allow future orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<button type="button" class="button wcblu-bo-btn wcblu-bo-btn-unblock" id="wcbli_ubo_btn" <?php 
            disabled( !$is_pro );
            ?>>
						<span class="dashicons dashicons-unlock" aria-hidden="true"></span>
						<span class="wcblu-bo-btn-text"><?php 
            echo esc_html__( 'Unblock Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></span>
					</button>
				</div>
			<?php 
        } else {
            ?>
			<div class="wcblu-bo-section wcblu-bo-section-block">
				<div class="wcblu-bo-section-head">
					<span class="dashicons dashicons-lock" aria-hidden="true"></span>
					<strong><?php 
            echo esc_html__( 'Block Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></strong>
				</div>
				<p><?php 
            echo esc_html__( 'Block this order and add details to blacklist.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
				<button type="button" class="button wcblu-bo-btn wcblu-bo-btn-block" id="wcbli_bo_btn" <?php 
            disabled( !$is_pro );
            ?>>
					<span class="dashicons dashicons-lock" aria-hidden="true"></span>
					<span class="wcblu-bo-btn-text"><?php 
            echo esc_html__( 'Block Order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></span>
				</button>
			</div>
			<?php 
        }
        ?>
			<div class="wcblu-bo-fields">
				<strong><?php 
        echo esc_html__( 'Block What?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
				<?php 
        foreach ( $fields as $key => $field ) {
            $state = ( isset( $field_state[$key] ) ? $field_state[$key] : array(
                'checked'  => false,
                'disabled' => true,
            ) );
            $disabled = !$is_pro || !empty( $state['disabled'] );
            $label = $field['label'];
            if ( '' !== $field['value'] ) {
                $label .= ' (' . $field['value'] . ')';
            }
            ?>
					<label>
						<input type="checkbox" name="wcblu_bo_fields[]" value="<?php 
            echo esc_attr( $key );
            ?>" <?php 
            checked( !empty( $state['checked'] ) );
            ?> <?php 
            disabled( $disabled );
            ?>>
						<span><?php 
            echo esc_html( $label );
            ?></span>
					</label>
				<?php 
        }
        ?>
			</div>
		</div>
		<div class="wcblu-bo-modal" id="wcblu_bo_modal" style="display:none;" role="dialog" aria-modal="true">
			<div class="wcblu-bo-modal-inner">
				<div class="wcblu-bo-modal-header">
					<h3 class="wcblu-bo-modal-title-block"><?php 
        echo esc_html__( 'What happens when you block an order?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></h3>
					<h3 class="wcblu-bo-modal-title-unblock" style="display:none;"><?php 
        echo esc_html__( 'What happens when you unblock an order?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></h3>
					<button type="button" class="wcblu-bo-modal-close" aria-label="<?php 
        echo esc_attr__( 'Close', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>"><span class="dashicons dashicons-no-alt"></span></button>
				</div>
				<div class="wcblu-bo-modal-body wcblu-bo-modal-body-block">
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-block"><span class="dashicons dashicons-dismiss"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'Customer details are added to blacklist', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'Selected details (email, IP, etc.) will be registered in the blacklist.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-shield"><span class="dashicons dashicons-shield"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'Future orders will be blocked', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'This customer won\'t be able to place future orders from the selected details.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-view"><span class="dashicons dashicons-visibility"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'You can unblock anytime', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'You can remove them from the blacklist anytime if needed.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<p class="wcblu-bo-modal-example"><?php 
        echo esc_html__( 'Example: if you block email and IP, the customer cannot place orders using the same email or from the same IP again.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
				</div>
				<div class="wcblu-bo-modal-body wcblu-bo-modal-body-unblock" style="display:none;">
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-view"><span class="dashicons dashicons-unlock"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'Selected details are removed from blacklist', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'Only the checked details will be removed from the blacklist.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-shield"><span class="dashicons dashicons-yes-alt"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'Future orders will be allowed', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'This customer can place future orders using the unblocked details.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<div class="wcblu-bo-modal-item">
						<span class="wcblu-bo-modal-icon is-block"><span class="dashicons dashicons-lock"></span></span>
						<div>
							<strong><?php 
        echo esc_html__( 'You can block again anytime', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></strong>
							<p><?php 
        echo esc_html__( 'You can add the same details back to the blacklist if needed.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
						</div>
					</div>
					<p class="wcblu-bo-modal-example"><?php 
        echo esc_html__( 'Example: if you unblock email and IP, the customer can place orders again using the same email or IP.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
				</div>
				<div class="wcblu-bo-modal-footer">
					<button type="button" class="button wcblu-bo-modal-cancel"><?php 
        echo esc_html__( 'Cancel', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></button>
					<button type="button" class="button wcblu-bo-modal-confirm"></button>
				</div>
			</div>
		</div>
		<?php 
    }

    /**
     * Block order widget in order 
     */
    public function wcblu_block_order_details_update_blacklist() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json( 'not_allowed' );
        }
        if ( !(function_exists( 'wbpfoabfc_fs' ) && wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code()) ) {
            wp_send_json( 'not_allowed' );
        }
        $order_id = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT );
        $action = filter_input( INPUT_POST, 'wcblu_action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $fields = filter_input(
            INPUT_POST,
            'fields',
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $order = wc_get_order( $order_id );
        $action = ( 'unblock' === $action ? 'unblock' : 'block' );
        if ( !is_a( $order, 'WC_Order' ) ) {
            wp_send_json( 'not_blocked' );
        }
        $allowed_fields = wcbfc_get_order_blockable_fields( $order );
        $selected_fields = ( is_array( $fields ) ? array_values( array_intersect( array_keys( $allowed_fields ), $fields ) ) : array() );
        $wcbluoption_array = json_decode( get_option( 'wcblu_option', '' ), true );
        $wcbluoption_array = ( is_array( $wcbluoption_array ) ? $wcbluoption_array : array() );
        $has_value = false;
        foreach ( $selected_fields as $field_key ) {
            $field = $allowed_fields[$field_key];
            $value = $field['value'];
            if ( '' === $value ) {
                continue;
            }
            $list_key = $field['list_key'];
            if ( !isset( $wcbluoption_array[$list_key] ) || !is_array( $wcbluoption_array[$list_key] ) ) {
                $wcbluoption_array[$list_key] = array();
            }
            if ( 'unblock' === $action ) {
                $wcbluoption_array[$list_key] = wcbfc_remove_value_from_block_list( $wcbluoption_array[$list_key], $value, $field['match'] );
                $has_value = true;
            } elseif ( !wcbfc_is_value_in_block_list( $value, $wcbluoption_array[$list_key], $field['match'] ) ) {
                $wcbluoption_array[$list_key][] = $value;
                $has_value = true;
            } else {
                $has_value = true;
            }
        }
        if ( $has_value ) {
            update_option( 'wcblu_option', wp_json_encode( $wcbluoption_array ) );
            wp_send_json( ( 'unblock' === $action ? 'Unblocked' : 'Blocked' ) );
        }
        wp_send_json( 'not_blocked' );
    }

    /**
     * Score widget in order 
     */
    public function edd_wcblu_add_meta_boxes( $order_id ) {
        $getGeneralSettings = get_option( 'wcblu_general_option' );
        $getGeneralSettings = ( empty( $getGeneralSettings ) ? '' : $getGeneralSettings );
        $getGeneralSettingsArray = json_decode( $getGeneralSettings, true );
        $mediumRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_low_risk_threshold'] );
        $heighRisk = ( empty( $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] ) ? '' : $getGeneralSettingsArray['wcbfc_settings_high_risk_threshold'] );
        $wcbfc_score_arry = get_post_meta( $order_id, 'edd_wcbfc_score', true );
        $wcbfc_score_msg_arry = get_post_meta( $order_id, 'edd_wcbfc_score_msg', true );
        $wcbfc_whitelisted_email = get_post_meta( $order_id, 'wcbfc_whitelisted_email', true );
        $order_score = get_post_meta( $order_id, 'edd_wcbfc_order_score', true );
        $wcbfc_score_arry = ( empty( $wcbfc_score_arry ) ? array() : $wcbfc_score_arry );
        $wcbfc_score_msg_arry = ( empty( $wcbfc_score_msg_arry ) ? array() : $wcbfc_score_msg_arry );
        $fscore = 0;
        $score_lables = '';
        $color = '#ccc';
        $label = 'risk';
        $risk_label_color = '';
        if ( isset( $wcbfc_whitelisted_email ) && !empty( $wcbfc_whitelisted_email ) ) {
            $score_lables .= '<li>' . $wcbfc_whitelisted_email . '</li>';
        }
        if ( '0' === $order_score ) {
            $fscore = '0';
        } else {
            foreach ( $wcbfc_score_arry as $key => $val ) {
                $fscore = $fscore + (int) $val;
                $score_lables .= '<li>' . $wcbfc_score_msg_arry[$key] . '</li>';
            }
        }
        $fscore = ( $fscore >= 100 ? 100 : $fscore );
        if ( $fscore > 0 && $fscore <= $mediumRisk ) {
            $color = '#7AD03A';
            $label = esc_html__( 'Low Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        } else {
            if ( $fscore >= $mediumRisk && $fscore <= $heighRisk ) {
                $color = '#FFAE00';
                $label = esc_html__( 'Medium Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            } else {
                if ( $fscore >= $heighRisk ) {
                    $color = '#D54E21';
                    $label = esc_html__( 'High Risk', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                }
            }
        }
        $allow_html_args = array(
            'li' => array(),
        );
        if ( isset( $wcbfc_whitelisted_email ) && !empty( $wcbfc_whitelisted_email ) ) {
            $color = '#4f575d';
            $risk_label_color = '#4f575d';
        } else {
            $risk_label_color = '#D54E21';
        }
        ?>
		<div class="edd_fraud_order_widget" style="position: relative;min-width: 255px;border: 1px solid #c3c4c7;box-shadow: 0 1px 1px rgba(0, 0, 0, .04);background: #fff;padding: 10px;">
			<h2 class="hndle">
				<span><?php 
        echo esc_html( 'Fraud Risk' );
        ?></span>
			</h2>
			<div class="wcbfc_position">
				<input class="wcblu_chart" data-width="50%" data-fgColor="<?php 
        echo esc_attr( $color );
        ?>" data-thickness=".4" data-readOnly=true value="0" rel="<?php 
        echo esc_attr( $fscore );
        ?>">
			</div>
			<span class="mb-score-label" style="color:<?php 
        echo esc_attr( $color );
        ?>;"><?php 
        echo esc_html( $fscore . ' % ' );
        echo esc_html( $label );
        ?></span>
			<?php 
        $monitor_badge = $this->wcblu_get_monitor_badge_html( $order_id );
        if ( $monitor_badge ) {
            echo '<p class="wcblu-monitor-note">' . wp_kses( $monitor_badge, array(
                'span' => array(
                    'class' => array(),
                ),
            ) ) . '</p>';
        }
        ?>
			<div class="mb-score-label-list">
				<ul style="color:<?php 
        echo esc_attr( $risk_label_color );
        ?>">
					<?php 
        echo wp_kses( $score_lables, $allow_html_args );
        ?>
				</ul>
			</div>
		
			<script type="text/javascript">
				jQuery('.wcblu_chart').knob();
				var data = jQuery('.wcblu_chart').attr('rel');
				jQuery({value: 0}).animate({value: data}, {
					duration: 3000,
					easing  : 'swing',
					step    : function () {
						jQuery('.wcblu_chart').val(Math.ceil(this.value)).trigger('change');
					}
				});
				jQuery('.mb-score-label-list').click( function(){
					jQuery(this).find('ul').fadeToggle();
				});
				jQuery(".mb-score-label-list ul").click(function(e) {
					e.stopPropagation();
				});
			</script>
		</div>
		<?php 
    }

    /**
     * function for export settings in JSON file
     */
    public function wcblu_export_settings() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'You are not allowed to export settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        $main_arr = array();
        $current_setting = ( get_option( 'wcblu_option' ) ? get_option( 'wcblu_option' ) : '' );
        $general_setting = ( get_option( 'wcblu_general_option' ) ? get_option( 'wcblu_general_option' ) : '' );
        $rules_setting = ( get_option( 'wcblu_rules_option' ) ? get_option( 'wcblu_rules_option' ) : '' );
        $main_arr['wcblu_option'] = json_decode( $current_setting, true );
        $main_arr['wcblu_general_option'] = json_decode( $general_setting, true );
        $main_arr['wcblu_rules_option'] = json_decode( $rules_setting, true );
        $merge_all_arr = wp_json_encode( $main_arr );
        if ( empty( $merge_all_arr ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'No data to export! please setup setting then export.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        } else {
            $filename = 'export_settings_' . time() . '.json';
            wp_send_json_success( array(
                'message'  => esc_html__( 'Export Done!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'data'     => $merge_all_arr,
                'filename' => $filename,
            ) );
        }
    }

    /**
     * function for import setting from JSON file
     */
    public function wcblu_import_settings() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        if ( !current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array(
                'message' => esc_html__( 'You are not allowed to import settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ) );
        }
        // Allow certain file formats
        $allowTypes = array('json');
        $import_file_args = array(
            'import_file' => array(
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_FORCE_ARRAY,
            ),
        );
        $import_file_arr = filter_var_array( $_FILES, $import_file_args );
        $filename = ( isset( $import_file_arr['import_file']['name'] ) && !empty( $import_file_arr['import_file']['name'] ) ? $import_file_arr['import_file']['name'] : '' );
        $fileType = pathinfo( $filename, PATHINFO_EXTENSION );
        if ( !in_array( $fileType, $allowTypes, true ) ) {
            $return = array(
                'success' => false,
                'message' => esc_html__( 'Please add JSON file!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            );
        } else {
            $filetempname = ( isset( $import_file_arr['import_file']['tmp_name'] ) && !empty( $import_file_arr['import_file']['tmp_name'] ) ? $import_file_arr['import_file']['tmp_name'] : '' );
            // Read JSON file
            $json = file_get_contents( $filetempname );
            // phpcs:ignore
            $decode_json = json_decode( $json, true );
            $import_flag = false;
            if ( isset( $decode_json['wcblu_option'] ) && !empty( $decode_json['wcblu_option'] ) && null !== $decode_json['wcblu_option'] ) {
                $wcblu_option = wp_json_encode( $decode_json['wcblu_option'] );
                update_option( 'wcblu_option', $wcblu_option );
                $import_flag = true;
            }
            if ( isset( $decode_json['wcblu_general_option'] ) && !empty( $decode_json['wcblu_general_option'] ) && null !== $decode_json['wcblu_general_option'] ) {
                $wcblu_general_option = wp_json_encode( $decode_json['wcblu_general_option'] );
                update_option( 'wcblu_general_option', $wcblu_general_option );
                $import_flag = true;
            }
            if ( isset( $decode_json['wcblu_rules_option'] ) && !empty( $decode_json['wcblu_rules_option'] ) && null !== $decode_json['wcblu_rules_option'] ) {
                $wcblu_rules_option = wp_json_encode( $decode_json['wcblu_rules_option'] );
                update_option( 'wcblu_rules_option', $wcblu_rules_option );
                $import_flag = true;
            }
            if ( true === $import_flag ) {
                $return = array(
                    'success' => true,
                    'message' => esc_html__( 'Data has been successfully imported!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                );
            }
        }
        wp_send_json( $return );
    }

    /**
     * Publick Function for create custom post type
     *
     */
    public function register_custom_post_type_banned_user() {
        register_post_type( 'blocked_user', array(
            'labels'            => array(
                'name'               => __( 'Blocked Users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'singular_name'      => __( 'Blocked Users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'menu_name'          => __( 'Blocked Users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'add_new'            => __( 'Add New', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'add_new_item'       => __( 'Add New Block User', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'edit'               => __( 'Edit', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'edit_item'          => __( 'Edit Block User', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'new_item'           => __( 'New Block User', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'view'               => __( 'View', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'view_item'          => __( 'View Block User', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'search_items'       => __( 'Search Block Users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'not_found'          => __( 'No Block User found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'not_found_in_trash' => __( 'No Block User found in Trash', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'parent'             => __( 'Parent Block User Listing', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ),
            'public'            => false,
            'show_in_nav_menus' => false,
            'show_in_menu'      => false,
            'show_ui'           => true,
            'supports'          => array('title', 'custom-fields'),
            'taxonomies'        => array(''),
            'has_archive'       => false,
            'capabilities'      => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap'      => true,
        ) );
    }

    /**
     * Ajax call for subscription
     */
    public function wp_add_plugin_wbl_pro() {
        $email_id = filter_input( INPUT_POST, 'email_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $email_id = ( isset( $email_id ) && !empty( $email_id ) ? $email_id : '' );
        $log_url = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $cur_date = gmdate( 'Y-m-d' );
        $request_url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/wp-add-plugin-users.php';
        if ( !empty( $email_id ) ) {
            wp_remote_post( $request_url, array(
                'method'      => 'POST',
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'user' => array(
                        'plugin_id'       => '35',
                        'user_email'      => $email_id,
                        'plugin_site'     => $log_url,
                        'status'          => 1,
                        'activation_date' => $cur_date,
                    ),
                ),
                'cookies'     => array(),
            ) );
            update_option( 'wbl_plugin_notice_shown', 'true' );
        }
    }

    /**
     * function for wooCommerce blacklist users create welcom screen page.
     */
    public function welcome_screen_pages_blacklist_users() {
        add_dashboard_page(
            'Welcome To Fraud Prevention for WC',
            'Welcome To Fraud Prevention for WC',
            'read',
            'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers',
            array($this, 'welcome_screen_content_blacklist_users')
        );
        remove_submenu_page( 'index.php', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        global $GLOBALS;
        if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
            add_menu_page(
                'Dotstore Plugins',
                __( 'Dotstore Plugins', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                'manage_option',
                'dots_store',
                array($this, ''),
                'dashicons-marker',
                25
            );
        }
        if ( wbpfoabfc_fs()->is_plan( 'free', true ) ) {
            add_submenu_page(
                'dots_store',
                'woocommerce-blacklist-users',
                'Fraud Prevention',
                'manage_options',
                'woocommerce_blacklist_users',
                array($this, 'wcblu_custom_admin_setting_options')
            );
        } else {
            add_submenu_page(
                'dots_store',
                'woocommerce-blacklist-users',
                'Fraud Prevention',
                'manage_options',
                'woocommerce_blacklist_users',
                array($this, 'wcblu_custom_admin_setting_options')
            );
        }
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
            add_submenu_page(
                'dots_store',
                'Dashboard',
                'Dashboard',
                'manage_options',
                'wcblu-dashboard',
                array($this, 'wbclu_dashboard_page')
            );
        }
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            add_submenu_page(
                'dots_store',
                'Dashboard for EDD',
                'Dashboard for EDD',
                'manage_options',
                'edd-wcblu-dashboard',
                array($this, 'edd_wbclu_dashboard_page__premium_only')
            );
        }
        add_submenu_page(
            'dots_store',
            'General Settings',
            'General Settings',
            'manage_options',
            'wcblu-general-settings',
            array($this, 'wbclu_general_settings_page')
        );
        add_submenu_page(
            'dots_store',
            'Rules',
            'Rules',
            'manage_options',
            'wcblu-auto-rules',
            array($this, 'wbclu_rules_page')
        );
        if ( !(wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code()) ) {
            add_submenu_page(
                'dots_store',
                'Get Premium',
                'Get Premium',
                'manage_options',
                'wcblu-upgrade-dashboard',
                array($this, 'wbclu_free_user_upgrade_page')
            );
        }
        add_submenu_page(
            'dots_store',
            'banned-user',
            __( 'Block Users Lite', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'manage_options',
            'blocked_user',
            array($this, 'custom_banned_user_listing')
        );
        add_submenu_page(
            'dots_store',
            'Get Started',
            'Get Started',
            'manage_options',
            'wblp-get-started',
            array($this, 'wblp_get_started_page')
        );
        add_submenu_page(
            'dots_store',
            'Import Export Data',
            'Import Export Data',
            'manage_options',
            'wcblu-import-export-setting',
            array($this, 'wbclu_import_export_page')
        );
    }

    /**
     * function for admin side settings option
     */
    public function wcblu_custom_admin_setting_options() {
        $file_dir = '/partials/header/plugin-header.php';
        if ( file_exists( dirname( __FILE__ ) . $file_dir ) ) {
            include dirname( __FILE__ ) . $file_dir;
        }
        $success_note = filter_input( INPUT_GET, 'success', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        ?>

        <div class="wcblu-col-container wcblu-main-table">
            <?php 
        if ( !empty( $success_note ) ) {
            ?>
                <div id="message" class="updated notice is-dismissible"><p><?php 
            esc_html_e( 'Data has been updated.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p></div>
            <?php 
        }
        ?>
            <form id="wcblu_plugin_form_id" method="post"
                    action="<?php 
        esc_url( get_admin_url() );
        ?>admin-post.php"
                    enctype="multipart/form-data" novalidate="novalidate">
                <input type='hidden' name='action' value='submit_form_wcblu'/>
                <input type='hidden' name='action-which' value='add'/>
                <?php 
        wp_nonce_field( 'wcblu_blacklist_settings', 'wcblu_blacklist_settings_nonce' );
        ?>
                <?php 
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $allow_html_args = array(
            'input'      => array(
                'type'     => array(
                    'checkbox' => true,
                    'text'     => true,
                    'submit'   => true,
                    'button'   => true,
                    'file'     => true,
                ),
                'class'    => true,
                'name'     => true,
                'value'    => true,
                'id'       => true,
                'style'    => true,
                'selected' => true,
                'checked'  => true,
                'disabled' => array(),
            ),
            'select'     => array(
                'id'               => true,
                'data-placeholder' => true,
                'name'             => true,
                'multiple'         => true,
                'class'            => true,
                'style'            => true,
                'selected'         => array(),
                'disabled'         => true,
            ),
            'a'          => array(
                'href'   => array(),
                'title'  => array(),
                'target' => array(),
            ),
            'b'          => array(
                'class' => true,
            ),
            'i'          => array(
                'class' => true,
            ),
            'p'          => array(
                'class' => true,
            ),
            'blockquote' => array(
                'class' => true,
            ),
            'h2'         => array(
                'class' => true,
            ),
            'h3'         => array(
                'class' => true,
            ),
            'ul'         => array(
                'class' => true,
            ),
            'ol'         => array(
                'class' => true,
            ),
            'li'         => array(
                'class' => true,
            ),
            'option'     => array(
                'value'    => true,
                'selected' => true,
            ),
            'table'      => array(
                'class' => true,
            ),
            'td'         => array(
                'class' => true,
            ),
            'th'         => array(
                'class' => true,
                'scope' => true,
            ),
            'tr'         => array(
                'class' => true,
            ),
            'tbody'      => array(
                'class' => true,
            ),
            'label'      => array(
                'for'   => true,
                'class' => true,
            ),
            'span'       => array(
                'class' => true,
            ),
            'div'        => array(
                'id'    => true,
                'class' => true,
                'title' => true,
                'style' => true,
            ),
            'textarea'   => array(
                'id'    => true,
                'class' => true,
                'name'  => true,
                'style' => true,
            ),
            'button'     => array(
                'type'  => true,
                'id'    => true,
                'class' => true,
                'name'  => true,
                'value' => true,
            ),
        );
        echo wp_kses( wcblu_get_setting_html_for_free_user( $getpluginoptionarray ), $allow_html_args );
        ?>
            </form>

        </div>
        
        </div>
        </div>
        <?php 
    }

    /**
     * function to return link for menu
     */
    public function custom_banned_user_listing() {
        $url = admin_url() . 'edit.php?post_type=blocked_user';
        ?>
        <script>location.href = '<?php 
        esc_attr_e( esc_url( $url ), 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>';</script>
        <?php 
    }

    /**
     * function return to welcom screen of plugin
     */
    public function welcome_screen_content_blacklist_users() {
        ?>
		<div class="wrap about-wrap">
			<h1 class="welcome_message_plugin"><?php 
        printf( esc_html__( 'Welcome To Woocommerce Blocker', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
        ?></h1>

			<div class="about-text woocommerce-about-text">
				<?php 
        $message = '';
        printf( esc_html__( '%s Prevent fake orders and Blacklist fraud customers plugin allows your WooCommerce store to refuse orders from specific user, based on blacklist rules.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), esc_html( $message ), esc_html( $this->version ) );
        ?>
				<img class="version_logo_img"
				     src="<?php 
        esc_url( plugin_dir_url( __FILE__ ) . 'images/black_list.png' );
        ?>">
			</div>
			
			<?php 
        $setting_tabs_wc = apply_filters( 'woocommerce_black_list_user_tab', array(
            "about"         => "Overview",
            "other_plugins" => "Checkout our other plugins",
        ) );
        $current_tab_wc = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $current_tab_wc = ( isset( $current_tab_wc ) ? $current_tab_wc : 'general' );
        ?>
			<h2 id="woo-black-list-tab-wrapper" class="nav-tab-wrapper">
				<?php 
        if ( is_array( $setting_tabs_wc ) ) {
            foreach ( $setting_tabs_wc as $name => $label ) {
                echo '<a  href="' . esc_url( home_url( 'wp-admin/index.php?page=woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers&tab=' . $name ) ) . '" class="nav-tab ' . (( $name === $current_tab_wc ? 'nav-tab-active' : '' )) . '">' . wp_kses_post( $label ) . '</a>';
            }
        }
        ?>
			</h2>
			<?php 
        if ( is_array( $setting_tabs_wc ) ) {
            foreach ( $setting_tabs_wc as $setting_tabkey_wc ) {
                switch ( $setting_tabkey_wc ) {
                    case $current_tab_wc:
                        do_action( 'woocommerce_blocker_prevent_fake_orders_and_blacklist_fraud_customers_' . $current_tab_wc );
                        break;
                }
            }
        }
        ?>
			<hr/>
			<div class="return-to-dashboard">
				<a href="<?php 
        echo esc_url( home_url( '/wp-admin/admin.php?page=woocommerce_blacklist_users' ) );
        ?>"><?php 
        esc_html_e( 'Go to Woocommerce Blocker - Prevent fake orders and Blacklist fraud customers Settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></a>
			</div>
		</div>
		
		<?php 
    }

    /**
     * function return to messages of blocker selection
     */
    public function woocommerce_blocker_prevent_fake_orders_and_blacklist_fraud_customers_about() {
        ?>
		<div class="changelog">
			<div class="changelog about-integrations">
				<div class="wc-feature feature-section col three-col">
					<div>
						<div class="woocommerce_black_list_content">
							<h3><?php 
        esc_html_e( 'Plugin Overview', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></h3>
							<p><?php 
        esc_html_e( 'This plugin can be used to refuse orders from specific users, based on customize blacklisting criteria.
You could specify a list of Email Addresses, IP address, State, Country, Zip-code and Domain names,  that will be blacklisted.
When a user will try to place an order or register using one of the blacklisted email, domain name, IP ect.. the checkout or account will be interrupted and the user will be notified of the reason why the operation was blocked.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
							<p><?php 
        esc_html_e( 'Additionally, an admin can get the detail report of genuine block user "who try to buy products more than one time" and contact them to resolve issue.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
							<p>
								<span><?php 
        esc_html_e( 'Key Features:', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></span>
							</p>
							<ul class="bulets">
								<li><?php 
        esc_html_e( 'Block user based on Email, IP, State, Country, Zipcode, and Domain.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Block user from web centralize data using API.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Ability to review fraudulent checkouts OR registration attempts based on rule.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Works in both checkout and registration pages.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Display standard WooCommerce notices, error messages can be customized via back-end settings.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Display blocked users report and admin can check how many time they tried to website.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Work with WooCommerce 2.0+ and Word Press 3.9+', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Easy to use interface to create and review blacklist rules.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
								<li><?php 
        esc_html_e( 'Localization compatible.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></li>
							</ul>
						</div>

					</div>
				</div>
			</div>
		</div>
		
		<?php 
    }

    /**
     * function create dashboad widget.
     * view   in dashboard.
     *
     */
    function my_custom_dashboard_widgets() {
        wp_add_dashboard_widget( 'custom_help_widget', __( 'Blacklist Users Report', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array($this, 'custom_dashboard_help') );
    }

    /**
     * Render Blacklist Users Report dashboard widget.
     */
    public function custom_dashboard_help() {
        $attempt_value = 3;
        $args_user_data = array(
            'post_type'      => 'blocked_user',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'post_parent'    => 0,
            'meta_query'     => array(array(
                'key'     => 'Attempt',
                'value'   => (int) $attempt_value,
                'type'    => 'numeric',
                'compare' => '>=',
            )),
        );
        $user_data = get_posts( $args_user_data );
        $blocked_user_list = admin_url( 'edit.php?post_type=blocked_user' );
        $rows_html = '';
        if ( !empty( $user_data ) && is_array( $user_data ) ) {
            foreach ( $user_data as $values ) {
                $attempt = absint( get_post_meta( $values->ID, 'Attempt', true ) );
                if ( $attempt < $attempt_value ) {
                    continue;
                }
                $badge_class = 'is-low';
                if ( $attempt >= 40 ) {
                    $badge_class = 'is-high';
                } elseif ( $attempt >= 20 ) {
                    $badge_class = 'is-medium';
                }
                $edit_link = get_edit_post_link( $values->ID );
                if ( empty( $edit_link ) ) {
                    continue;
                }
                $rows_html .= '<tr>';
                $rows_html .= '<td class="wcblu-dash-email" data-colname="' . esc_attr__( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '"><span class="wcblu-dash-email__text" title="' . esc_attr( $values->post_title ) . '">' . esc_html( $values->post_title ) . '</span></td>';
                $rows_html .= '<td class="wcblu-dash-attempts" data-colname="' . esc_attr__( 'Attempts', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '"><span class="wcblu-dash-badge ' . esc_attr( $badge_class ) . '">' . esc_html( (string) $attempt ) . '</span></td>';
                $rows_html .= '<td class="wcblu-dash-action" data-colname="' . esc_attr__( 'Action', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '"><a class="wcblu-dash-link" href="' . esc_url( $edit_link ) . '">' . esc_html__( 'View details', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . ' <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></a></td>';
                $rows_html .= '</tr>';
            }
        }
        $allowed_html = array(
            'div'   => array(
                'class' => true,
            ),
            'p'     => array(
                'class' => true,
            ),
            'table' => array(
                'class' => true,
            ),
            'thead' => array(),
            'tbody' => array(),
            'tfoot' => array(),
            'tr'    => array(),
            'th'    => array(
                'class' => true,
                'scope' => true,
            ),
            'td'    => array(
                'class'        => true,
                'data-colname' => true,
                'colspan'      => true,
            ),
            'span'  => array(
                'class'       => true,
                'title'       => true,
                'aria-hidden' => true,
            ),
            'a'     => array(
                'class' => true,
                'href'  => true,
            ),
        );
        ob_start();
        ?>
        <div class="wcblu-dashboard-widget">
            <p class="wcblu-dashboard-widget__intro">
                <?php 
        esc_html_e( 'Top blocked users by failed checkout attempts.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
            </p>

            <?php 
        if ( '' !== $rows_html ) {
            ?>
                <div class="wcblu-dashboard-widget__table-wrap">
                    <table class="wcblu-dashboard-widget__table">
                        <thead>
                            <tr>
                                <th scope="col" class="wcblu-dash-email"><?php 
            esc_html_e( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></th>
                                <th scope="col" class="wcblu-dash-attempts"><?php 
            esc_html_e( 'Attempts', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></th>
                                <th scope="col" class="wcblu-dash-action"><?php 
            esc_html_e( 'Action', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
            echo wp_kses( $rows_html, $allowed_html );
            ?>
                        </tbody>
                    </table>
                </div>
                <div class="wcblu-dashboard-widget__footer">
                    <a class="button button-secondary wcblu-dashboard-widget__cta" href="<?php 
            echo esc_url( $blocked_user_list );
            ?>">
                        <?php 
            esc_html_e( 'View all records', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?>
                    </a>
                </div>
            <?php 
        } else {
            ?>
                <div class="wcblu-dashboard-widget__empty">
                    <span class="dashicons dashicons-shield-alt" aria-hidden="true"></span>
                    <p><?php 
            esc_html_e( 'No blocked users with 3 or more attempts yet.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
                    <a class="button button-secondary" href="<?php 
            echo esc_url( $blocked_user_list );
            ?>">
                        <?php 
            esc_html_e( 'Open blocked user list', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?>
                    </a>
                </div>
            <?php 
        }
        ?>
        </div>
        <?php 
        echo wp_kses( ob_get_clean(), $allowed_html );
    }

    public function wcblu_admin_footer_review() {
        $url = '';
        $url = esc_url( 'https://wordpress.org/plugins/woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers/#reviews' );
        $html = sprintf( wp_kses( __( '<strong>We need your support</strong> to keep updating and improving the plugin. Please <a href="%1$s" target="_blank">help us by leaving a good review</a> :) Thanks!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
            'strong' => array(),
            'a'      => array(
                'href'   => array(),
                'target' => 'blank',
            ),
        ) ), esc_url( $url ) );
        echo wp_kses_post( $html );
    }

    /**
     * Modal for contacting admin when products get out of stock.
     */
    function wcblu_admin__notify_modal() {
        ob_start();
        ?>
		<div class="notification_popup">
			<span class="notification_close"></span>
			<div class="notification_icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
			<div class="notification_message">
				<h3 class="title"></h3>
				<p class="message"></p>
			</div>
		</div>
		<?php 
        echo wp_kses_post( ob_get_clean() );
    }

    /**
     * @param $actions
     * @param $post
     *
     * @return mixed
     * Function to return delete action of blocker plugin.
     */
    function wcblu_permanent_delete_action( $actions, $post ) {
        if ( 'blocked_user' === $post->post_type ) {
            $delete_url = wp_nonce_url( add_query_arg( array(
                'post_type'            => 'blocked_user',
                'was_permanent_delete' => $post->ID,
            ), admin_url( 'edit.php' ) ), 'wcblu_permanent_delete_' . $post->ID, '_wcblu_delete_nonce' );
            $actions['was-delete-permanent'] = '<a href="' . esc_url( $delete_url ) . '" class="was-permanent-user">' . esc_html__( 'Delete Permanently', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) . '</a>';
        }
        return $actions;
    }

    /**
     * function to return process of delete blocked user
     */
    function wcblu_permanent_delete_process() {
        $unblock_user_id = filter_input( INPUT_GET, 'was_permanent_delete', FILTER_SANITIZE_NUMBER_INT );
        if ( empty( $unblock_user_id ) ) {
            return;
        }
        // Security: Require admin capability.
        if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'manage_options' ) ) {
            return;
        }
        // Security: Verify nonce.
        if ( !isset( $_GET['_wcblu_delete_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wcblu_delete_nonce'] ) ), 'wcblu_permanent_delete_' . $unblock_user_id ) ) {
            return;
        }
        // Security: Ensure we only delete blocked_user posts, not arbitrary content.
        $post = get_post( $unblock_user_id );
        if ( !$post instanceof WP_Post || 'blocked_user' !== $post->post_type ) {
            return;
        }
        wcblu_permanent_delete_data( $unblock_user_id );
    }

    /**
     * Add custom css for dotstore icon in admin area
     *
     * @since  1.0.0
     *
     */
    public function mmqw_dot_store_icon_css() {
        echo '<style>
	    .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
	    li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
	    @media only screen and (max-width: 960px){
	    	.toplevel_page_dots_store .dashicons-marker::after{left:14px;}
	    }
	  	</style>';
    }

    /**
     * Set convert_to_pro flag after Freemius purchase.
     *
     * @since 4.5.2
     */
    public function wcblu_convert_to_pro_purchase() {
        check_ajax_referer( 'wcblu_convert_to_pro_purchase', 'security' );
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }
        update_option( 'wcblu_convert_to_pro', true );
        wp_send_json_success();
    }

    /**
     * Dismiss convert to pro notice.
     *
     * @since 4.5.2
     */
    public function wcblu_handle_convert_to_pro_dismiss() {
        $dismiss = filter_input( INPUT_GET, 'wcblu-dismiss-convert-to-pro', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $nonce = filter_input( INPUT_GET, '_wcblu_convert_to_pro_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( '1' !== sanitize_text_field( $dismiss ) ) {
            return;
        }
        if ( !wp_verify_nonce( sanitize_text_field( $nonce ), 'wcblu_convert_to_pro_dismiss' ) ) {
            return;
        }
        update_option( 'wcblu_convert_to_pro', false );
    }

    /**
     * Get dynamic promotional bar of plugin
     *
     * @param   String  $plugin_slug  slug of the plugin added in the site option
     * @since    3.9.3
     * 
     * @return  null
     */
    public function wb_get_promotional_bar( $plugin_slug = '' ) {
        $promotional_bar_upi_url = WB_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request = wp_remote_get( $promotional_bar_upi_url );
        //phpcs:ignore
        if ( empty( $promotional_banner_request->errors ) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo '<div class="dynamicbar_wrapper">';
            if ( !empty( $promotional_banner_request_body ) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
                    $promotional_banner_id = $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];
                    if ( !empty( $promotional_banner_target_audience ) ) {
                        $plugin_keys = array();
                        if ( is_array( $promotional_banner_target_audience ) ) {
                            foreach ( $promotional_banner_target_audience as $list ) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }
                        $display_banner_flag = false;
                        if ( in_array( 'all_customers', $plugin_keys, true ) || in_array( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    if ( true === $display_banner_flag ) {
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + 86400 * 7 );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
                            	<div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
                                    <?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
                                        <img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
                                        <?php 
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                    <a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
                                                    <?php 
                                        }
                                    }
                                    ?>
                                    	</p>
                                    </div>
                                    <a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_attr( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php 
                                }
                            }
                        } else {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) {
                                    ?>
                    			<div class="dpb-popup <?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>">
                                    <?php 
                                    if ( !empty( $promotional_banner_image ) ) {
                                        ?>
                                            <img src="<?php 
                                        echo esc_url( $promotional_banner_image );
                                        ?>"/>
                                        <?php 
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php 
                                    echo wp_kses_post( str_replace( array('<p>', '</p>'), '', $promotional_banner_description ) );
                                    if ( !empty( $promotional_banner_button_group ) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                    <a href="<?php 
                                            echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] );
                                            ?>" target="_blank"><?php 
                                            echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] );
                                            ?></a>
                                                    <?php 
                                        }
                                    }
                                    ?>
                                        </p>
                                    </div>
                                    <a href="javascript:void(0);" data-bar-id="<?php 
                                    echo esc_attr( $promotional_banner_id );
                                    ?>" data-popup-name="<?php 
                                    echo ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' );
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php 
                                }
                            }
                        }
                    }
                }
            }
            echo '</div>';
        }
    }

    /**
     * Get and save plugin setup wizard data
     * 
     * @since    3.9.3
     * 
     */
    public function wcblu_plugin_setup_wizard_submit() {
        check_ajax_referer( 'wcblu-ajax-nonce', 'nonce' );
        $survey_list = filter_input( INPUT_GET, 'survey_list', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !empty( $survey_list ) && 'Select One' !== $survey_list ) {
            update_option( 'wcblu_where_hear_about_us', $survey_list );
        }
        wp_die();
    }

    /**
     * Send setup wizard data to sendinblue
     * 
     * @since    3.9.3
     * 
     */
    public function wcblu_send_wizard_data_after_plugin_activation() {
        $send_wizard_data = filter_input( INPUT_GET, 'send-wizard-data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $send_wizard_data ) && !empty( $send_wizard_data ) ) {
            if ( !get_option( 'wcblu_data_submited_in_sendiblue' ) ) {
                $wcblu_where_hear = get_option( 'wcblu_where_hear_about_us' );
                $get_user = wbpfoabfc_fs()->get_user();
                $data_insert_array = array();
                if ( isset( $get_user ) && !empty( $get_user ) ) {
                    $data_insert_array = array(
                        'user_email'              => $get_user->email,
                        'ACQUISITION_SURVEY_LIST' => $wcblu_where_hear,
                    );
                }
                $feedback_api_url = WB_STORE_URL . 'wp-json/dotstore-sendinblue-data/v2/dotstore-sendinblue-data?' . wp_rand();
                $query_url = $feedback_api_url . '&' . http_build_query( $data_insert_array );
                if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
                    $response = vip_safe_wp_remote_get(
                        $query_url,
                        3,
                        1,
                        20
                    );
                } else {
                    $response = wp_remote_get( $query_url );
                }
                if ( !is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                    update_option( 'wcblu_data_submited_in_sendiblue', '1' );
                    delete_option( 'wcblu_where_hear_about_us' );
                }
            }
        }
    }

}
