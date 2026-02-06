<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers
 * @subpackage Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers
 * @subpackage Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers/public
 * @author     multidots <info@multidots.in>
 */
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;
class Woocommerce_Blocker_Prevent_Fake_Orders_And_Blacklist_Fraud_Customers_Public {
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
     * @param string $plugin_name The name of the plugin.
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
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**`
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
        $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        $is_woocommerce_active = in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) && class_exists( 'WooCommerce' );
        $is_edd_active = class_exists( 'Easy_Digital_Downloads' );
        if ( $is_woocommerce_active || $is_edd_active ) {
            if ( $is_woocommerce_active ) {
                if ( is_checkout() ) {
                    $wcblu_rules_option = get_option( 'wcblu_rules_option' );
                    $wcblu_rules_optionarray = json_decode( $wcblu_rules_option, true );
                    $wcbfc_geo_match = ( !empty( $wcblu_rules_optionarray['wcbfc_billing_shipping_geo_match'] ) ? $wcblu_rules_optionarray['wcbfc_billing_shipping_geo_match'] : '' );
                    wp_enqueue_style(
                        $this->plugin_name . '-public',
                        plugin_dir_url( __FILE__ ) . 'css/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-public.css',
                        array(),
                        $this->version,
                        'all'
                    );
                    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                    wp_enqueue_script(
                        $this->plugin_name,
                        plugin_dir_url( __FILE__ ) . 'js/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-public' . $suffix . '.js',
                        array('jquery'),
                        $this->version,
                        false
                    );
                    wp_localize_script( $this->plugin_name, 'adminajax', array(
                        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                        'nonce'     => wp_create_nonce( 'wcblu-ajax-nonce' ),
                        'geo_match' => $wcbfc_geo_match,
                    ) );
                    $getplugingeneralopt = get_option( 'wcblu_general_option' );
                    if ( isset( $getplugingeneralopt ) && !empty( $getplugingeneralopt ) ) {
                        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
                        $wcbfc_recaptcha_status = ( !empty( $getplugingeneraloptarray['wcbfc_recaptcha_status'] ) ? $getplugingeneraloptarray['wcbfc_recaptcha_status'] : '0' );
                        $wcbfc_recaptcha_version = ( !empty( $getplugingeneraloptarray['wcbfc_recaptcha_version'] ) ? $getplugingeneraloptarray['wcbfc_recaptcha_version'] : 'v2' );
                        $wcblu_v2_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v2_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v2_keys_value'] : '' );
                        $wcblu_v3_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v3_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v3_keys_value'] : '' );
                        $checkout_page_id = wc_get_page_id( 'checkout' );
                        $checkout_page_content = get_post_field( 'post_content', $checkout_page_id );
                        if ( has_block( 'woocommerce/checkout', $checkout_page_content ) ) {
                            $wcblu_v3_keys_value = '';
                        }
                        $args = [
                            'render' => ( $wcbfc_recaptcha_version === 'wcblu_v2_keys' ? '' : $wcblu_v3_keys_value ),
                        ];
                        if ( '1' === $wcbfc_recaptcha_status ) {
                            wp_register_script(
                                'wcblu-re-captcha',
                                add_query_arg( $args, 'https://www.google.com/recaptcha/api.js' ),
                                array(),
                                '1.0'
                            );
                            wp_enqueue_script( 'wcblu-re-captcha' );
                            if ( $wcbfc_recaptcha_version === 'wcblu_v2_keys' ) {
                                wp_register_script(
                                    'wcbfc_captcha-block-frontend',
                                    plugins_url( 'public/js/block/build/wcbfc_captcha-block-frontend.js', __DIR__ ),
                                    array(
                                        'react',
                                        'wc-blocks-checkout',
                                        'wp-element',
                                        'wp-i18n'
                                    ),
                                    // Dependencies
                                    false,
                                    // No version
                                    true
                                );
                                wp_localize_script( 'wcbfc_captcha-block-frontend', 'wcbfc_captcha_ajax', array(
                                    'wcbfc_captcha_key'      => $wcblu_v2_keys_value,
                                    'wcbfc_recaptcha_status' => $wcbfc_recaptcha_status,
                                ) );
                                wp_enqueue_script( 'wcbfc_captcha-block-frontend' );
                            }
                        }
                    }
                }
            } else {
                wp_enqueue_style(
                    $this->plugin_name . '-public',
                    plugin_dir_url( __FILE__ ) . 'css/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-public.css',
                    array(),
                    $this->version,
                    'all'
                );
                $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
                wp_enqueue_script(
                    $this->plugin_name,
                    plugin_dir_url( __FILE__ ) . 'js/woocommerce-blocker-prevent-fake-orders-and-blacklist-fraud-customers-public' . $suffix . '.js',
                    array('jquery'),
                    $this->version,
                    false
                );
            }
        }
    }

    // woocommerce checkout page functionality
    /**
     * Function to return email and domain validation
     */
    public function woo_email_domain_validation( $order ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $getplaceordertype = ( !empty( $getpluginoptionarray['wcblu_place_order_type'] ) ? $getpluginoptionarray['wcblu_place_order_type'] : '' );
        $getaddresstype = ( !empty( $getpluginoptionarray['wcblu_address_type'] ) ? $getpluginoptionarray['wcblu_address_type'] : '' );
        $wc_first_name_relation = ( !empty( $getpluginoptionarray['wcblu_first_name_relation'] ) ? $getpluginoptionarray['wcblu_first_name_relation'] : '' );
        $wc_last_name_relation = ( !empty( $getpluginoptionarray['wcblu_last_name_relation'] ) ? $getpluginoptionarray['wcblu_last_name_relation'] : '' );
        $flagForEnterUserToBannedList = 0;
        $email = '';
        $checkout_page_id = wc_get_page_id( 'checkout' );
        $checkout_page_content = get_post_field( 'post_content', $checkout_page_id );
        if ( has_block( 'woocommerce/checkout', $checkout_page_content ) && is_a( $order, 'WC_Order' ) ) {
            $billing_email = $order->get_billing_email();
        } else {
            $billing_email = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        }
        if ( isset( $getplaceordertype ) && !empty( $getplaceordertype ) && '1' === $getplaceordertype ) {
            // return if billing email is empty
            $billing_email = ( isset( $billing_email ) && !empty( $billing_email ) && !wp_verify_nonce( sanitize_email( $billing_email ), 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) ? sanitize_email( $billing_email ) : '' );
            if ( !$billing_email ) {
                wc_add_notice( esc_html__( 'Please add email address to place order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), 'error' );
            }
            $email = wcblu_safe_trim( $billing_email );
            // return if billing email is unvalid
            if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                wc_add_notice( esc_html__( 'Please add valid email address to place order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), 'error' );
            }
            // validate billing email
            $errorEmail = $this->verify_email( $email );
            if ( $errorEmail ) {
                wc_add_notice( $errorEmail, 'error' );
                $flagForEnterUserToBannedList = 1;
            }
            // validate email domain
            if ( !empty( $email ) ) {
                $errorDomain = $this->verify_domain( $email );
                if ( $errorDomain ) {
                    wc_add_notice( $errorDomain, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
            }
            if ( has_block( 'woocommerce/checkout', $checkout_page_content ) && is_a( $order, 'WC_Order' ) ) {
                // @phpstan-ignore-next-line
                $ship_to_different_address = ( isset( $request ) && $request instanceof WP_REST_Request ? $request->get_param( 'ship_to_different_address' ) : null );
                // phpcs:ignore
                $billing_add_1 = $order->get_billing_address_1();
                $billing_add_2 = $order->get_billing_address_2();
                $phone = $order->get_billing_phone();
                $country = $order->get_billing_country();
                $state = $order->get_billing_state();
                $zip = $order->get_billing_postcode();
            } else {
                $ship_to_different_address = filter_input( INPUT_POST, 'ship_to_different_address', FILTER_SANITIZE_NUMBER_INT );
                $billing_add_1 = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_address_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                $billing_add_2 = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_address_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                $phone = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                $country = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                $state = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                $zip = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
            }
            if ( "1" !== $ship_to_different_address || "shipping_address_type" !== $getaddresstype ) {
                $errorAddress = '';
                if ( isset( $billing_add_1 ) && !empty( $billing_add_1 ) ) {
                    $errorAddress = $this->verify_address( $billing_add_1, $billing_add_2 );
                }
                if ( $errorAddress ) {
                    wc_add_notice( $errorAddress, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                $errorPhone = $this->verify_phone( $phone );
                if ( $errorPhone ) {
                    wc_add_notice( $errorPhone, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                $errorZone = '';
                if ( isset( $country ) && !empty( $country ) ) {
                    $errorZone = $this->verify_zone( $country, $state, $zip );
                }
                if ( $errorZone ) {
                    wc_add_notice( $errorZone, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
            }
            // validate IP (Test if it is a shared client)
            $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
            $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
            $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
            if ( !empty( $http_client_ip ) ) {
                $ip = $http_client_ip;
                //Is it a proxy address
            } elseif ( !empty( $http_x_forwarded_for ) ) {
                $ip = $http_x_forwarded_for;
            } else {
                $ip = $remote_addr;
            }
            $errorIp = $this->verify_ip( $ip );
            if ( $errorIp ) {
                wc_add_notice( $errorIp, 'error' );
                $flagForEnterUserToBannedList = 1;
            }
            if ( isset( $ship_to_different_address ) && "1" !== $ship_to_different_address || "shipping_address_type" !== $getaddresstype ) {
                if ( has_block( 'woocommerce/checkout', $checkout_page_content ) && is_a( $order, 'WC_Order' ) ) {
                    $country = $order->get_billing_country();
                    $state = $order->get_billing_state();
                } else {
                    $country = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                    $state = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                }
                // validate billing state
                $errorState = '';
                if ( isset( $state ) && !empty( $state ) ) {
                    $errorState = $this->verify_state( $country, $state );
                }
                if ( $errorState ) {
                    wc_add_notice( $errorState, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // validate billing country
                $errorCountry = '';
                if ( isset( $country ) && !empty( $country ) ) {
                    $errorCountry = $this->verify_country( $country );
                }
                if ( $errorCountry ) {
                    wc_add_notice( $errorCountry, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // validate billing zip
                if ( has_block( 'woocommerce/checkout', $checkout_page_content ) && is_a( $order, 'WC_Order' ) ) {
                    $zip = $order->get_billing_postcode();
                } else {
                    $zip = wcblu_safe_trim( filter_input( INPUT_POST, 'billing_postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                }
                $errorzip = '';
                if ( isset( $zip ) && !empty( $zip ) ) {
                    $errorzip = $this->verify_zip( $zip );
                }
                if ( $errorzip ) {
                    wc_add_notice( $errorzip, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // Address blocking
                $errorAddress = '';
                if ( isset( $billing_add_1 ) && !empty( $billing_add_1 ) && 1 !== $flagForEnterUserToBannedList ) {
                    $errorAddress = $this->verify_address( $billing_add_1, $billing_add_2 );
                }
                if ( $errorAddress ) {
                    wc_add_notice( $errorAddress, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
            }
            // end here
        } else {
            $createaccount = filter_input( INPUT_POST, 'createaccount', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            if ( '1' === $createaccount ) {
                // return if billing email doesn't exists
                $billing_email = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                if ( !$billing_email ) {
                    return;
                }
                $email = wcblu_safe_trim( $billing_email );
                // return if billing email is unvalid
                if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                    return;
                }
                $errorEmail = $this->verify_email( $email );
                if ( $errorEmail ) {
                    wc_add_notice( $errorEmail, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // validate email domain
                if ( !empty( $email ) ) {
                    $errorDomain = $this->verify_domain( $email );
                    if ( $errorDomain ) {
                        wc_add_notice( $errorDomain, 'error' );
                        $flagForEnterUserToBannedList = 1;
                    }
                }
                //Test if it is a shared client
                $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
                $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
                $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
                if ( !empty( $http_client_ip ) ) {
                    $ip = $http_client_ip;
                    //Is it a proxy address
                } elseif ( !empty( $http_x_forwarded_for ) ) {
                    $ip = $http_x_forwarded_for;
                } else {
                    $ip = $remote_addr;
                }
                $errorIp = $this->verify_ip( $ip );
                if ( $errorIp ) {
                    wc_add_notice( $errorIp, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                $state = filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $country = filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                if ( isset( $state ) && !empty( $state ) ) {
                    $errorState = $this->verify_state( $country, $state );
                }
                if ( $errorState ) {
                    wc_add_notice( $errorState, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                if ( isset( $country ) && !empty( $country ) ) {
                    $errorCountry = $this->verify_country( $country );
                }
                if ( $errorCountry ) {
                    wc_add_notice( $errorCountry, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                $zip = filter_input( INPUT_POST, 'billing_postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                if ( isset( $zip ) && !empty( $zip ) ) {
                    $errorzip = $this->verify_zip( $zip );
                }
                if ( $errorzip ) {
                    wc_add_notice( $errorzip, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // zone wise code
                $state = filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $country = filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                if ( isset( $state ) && !empty( $state ) || isset( $country ) && !empty( $country ) ) {
                    $errorZone = $this->verify_zone( $country, $state, $zip );
                }
                if ( $errorZone ) {
                    wc_add_notice( $errorZone, 'error' );
                    $flagForEnterUserToBannedList = 1;
                }
                // end here
            }
        }
        if ( 1 === $flagForEnterUserToBannedList ) {
            if ( has_block( 'woocommerce/checkout', $checkout_page_content ) && is_a( $order, 'WC_Order' ) ) {
                $billing_email = $order->get_billing_email();
            } else {
                $billing_email = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            }
            $email = wcblu_safe_trim( $billing_email );
            $query = wp_cache_get( 'blocked_user_data_key' );
            if ( false === $query ) {
                $args = array(
                    'post_status' => 'publish',
                    'post_type'   => 'blocked_user',
                    's'           => $email,
                );
                $args_query = new WP_Query($args);
                if ( !empty( $args_query->posts ) ) {
                    $get_posts = $args_query->posts;
                    $query = $get_posts[0]->ID;
                }
                wp_cache_set( 'blocked_user_data_key', $query );
            }
            $query = wp_cache_get( 'blocked_user_data_key' );
            if ( false !== $query ) {
                $post_id = wp_cache_get( 'blocked_user_data_key_for_post_id' );
                if ( false === $post_id ) {
                    $args_for_post_id = array(
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                        's'           => $email,
                    );
                    $args_for_post_id_query = new WP_Query($args_for_post_id);
                    if ( !empty( $args_for_post_id_query->posts ) ) {
                        $get_posts = $args_for_post_id_query->posts;
                        $post_id = $get_posts[0]->ID;
                    }
                    wp_cache_set( 'blocked_user_data_key_for_post_id', $post_id );
                }
                $meta = get_post_meta( $post_id, 'Attempt', true );
                $meta++;
                update_post_meta( $post_id, 'Attempt', $meta );
                update_post_meta( $post_id, 'First Name', filter_input( INPUT_POST, 'billing_first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Last Name', filter_input( INPUT_POST, 'billing_last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'City', filter_input( INPUT_POST, 'billing_city', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Country', filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Phone', filter_input( INPUT_POST, 'billing_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Company', filter_input( INPUT_POST, 'billing_company', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Postcode', filter_input( INPUT_POST, 'billing_postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Address 1', filter_input( INPUT_POST, 'billing_address_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Address 2', filter_input( INPUT_POST, 'billing_address_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'State', filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'WhereUserBanned', 'Place Order' );
                $post_status = get_post_status( $post_id );
                if ( 'trash' === $post_status ) {
                    wp_update_post( array(
                        'ID'          => $post_id,
                        'post_status' => 'publish',
                    ) );
                }
            } else {
                $user = array(
                    'post_title'  => $email,
                    'post_status' => 'publish',
                    'post_type'   => 'blocked_user',
                );
                $post_id = wp_insert_post( $user );
                update_post_meta( $post_id, 'First Name', filter_input( INPUT_POST, 'billing_first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Last Name', filter_input( INPUT_POST, 'billing_last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'City', filter_input( INPUT_POST, 'billing_city', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Country', filter_input( INPUT_POST, 'billing_country', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Phone', filter_input( INPUT_POST, 'billing_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Company', filter_input( INPUT_POST, 'billing_company', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Postcode', filter_input( INPUT_POST, 'billing_postcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Address 1', filter_input( INPUT_POST, 'billing_address_1', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Address 2', filter_input( INPUT_POST, 'billing_address_2', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'State', filter_input( INPUT_POST, 'billing_state', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Attempt', '1' );
                update_post_meta( $post_id, 'WhereUserBanned', 'Place Order' );
            }
            //compatible with WooCommerce PayPal Payments Plugin Start
            if ( is_plugin_active( 'woocommerce-paypal-payments/woocommerce-paypal-payments.php' ) && class_exists( 'WooCommerce\\PayPalCommerce\\Session\\SessionHandler' ) ) {
                $reset_paypal_obj = new WooCommerce\PayPalCommerce\Session\SessionHandler();
                if ( !isset( $reset_paypal_obj ) || empty( $reset_paypal_obj->order() ) ) {
                    WC()->session->set( 'reload_checkout', 'true' );
                }
                WC()->session->set( 'ppcp', $reset_paypal_obj );
            }
            //compatible with WooCommerce PayPal Payments Plugin End
        }
    }

    // woocommerce checkout page functionality
    /**
     * Function to return email and domain validation
     */
    public function edd_woo_email_domain_validation() {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $getplaceordertype = ( !empty( $getpluginoptionarray['wcblu_place_order_type'] ) ? $getpluginoptionarray['wcblu_place_order_type'] : '' );
        $wc_first_name_relation = ( !empty( $getpluginoptionarray['wcblu_first_name_relation'] ) ? $getpluginoptionarray['wcblu_first_name_relation'] : '' );
        $wc_last_name_relation = ( !empty( $getpluginoptionarray['wcblu_last_name_relation'] ) ? $getpluginoptionarray['wcblu_last_name_relation'] : '' );
        $flagForEnterUserToBannedList = 0;
        $email = '';
        $edd_email = filter_input( INPUT_POST, 'edd_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $getplaceordertype ) && !empty( $getplaceordertype ) && '1' === $getplaceordertype ) {
            // return if billing email is empty
            $billing_email = ( isset( $edd_email ) && !empty( $edd_email ) && !wp_verify_nonce( sanitize_email( $edd_email ), 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) ? sanitize_email( $edd_email ) : '' );
            $email = wcblu_safe_trim( $billing_email );
            // validate billing email
            $errorEmail = $this->verify_email( $email );
            if ( $errorEmail && function_exists( 'edd_set_error' ) ) {
                edd_set_error( 'Blocked_email', $errorEmail );
                $flagForEnterUserToBannedList = 1;
            }
            // validate email domain
            if ( !empty( $email ) ) {
                $errorDomain = $this->verify_domain( $email );
                if ( $errorDomain && function_exists( 'edd_set_error' ) ) {
                    edd_set_error( 'Blocked_email_domain', $errorDomain );
                    $flagForEnterUserToBannedList = 1;
                }
            }
            // validate IP (Test if it is a shared client)
            $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
            $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
            $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
            if ( !empty( $http_client_ip ) ) {
                $ip = $http_client_ip;
                //Is it a proxy address
            } elseif ( !empty( $http_x_forwarded_for ) ) {
                $ip = $http_x_forwarded_for;
            } else {
                $ip = $remote_addr;
            }
            $errorIp = $this->verify_ip( $ip );
            if ( $errorIp && function_exists( 'edd_set_error' ) ) {
                edd_set_error( 'Blocked_user_browser', $errorIp );
                $flagForEnterUserToBannedList = 1;
            }
            // end here
        } else {
            $createaccount = filter_input( INPUT_POST, 'edd-purchase-var', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            // bhavesh check acount create steps here
            if ( 'needs-to-register' === $createaccount ) {
                // return if billing email doesn't exists
                $billing_email = filter_input( INPUT_POST, 'edd_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                if ( !$billing_email ) {
                    return;
                }
                $email = wcblu_safe_trim( $billing_email );
                // return if billing email is unvalid
                if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                    return;
                }
                $errorEmail = $this->verify_email( $email );
                if ( $errorEmail && function_exists( 'edd_set_error' ) ) {
                    edd_set_error( 'Blocked_email', $errorEmail );
                    $flagForEnterUserToBannedList = 1;
                }
                // validate email domain
                if ( !empty( $email ) ) {
                    $errorDomain = $this->verify_domain( $email );
                    if ( $errorDomain && function_exists( 'edd_set_error' ) ) {
                        edd_set_error( 'Blocked_email_domain', $errorDomain );
                        $flagForEnterUserToBannedList = 1;
                    }
                }
                //Test if it is a shared client
                $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
                $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
                $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
                if ( !empty( $http_client_ip ) ) {
                    $ip = $http_client_ip;
                    //Is it a proxy address
                } elseif ( !empty( $http_x_forwarded_for ) ) {
                    $ip = $http_x_forwarded_for;
                } else {
                    $ip = $remote_addr;
                }
                $errorIp = $this->verify_ip( $ip );
                if ( $errorIp && function_exists( 'edd_set_error' ) ) {
                    edd_set_error( 'Blocked_IP', $errorIp );
                    $flagForEnterUserToBannedList = 1;
                }
                // end here
            }
        }
        if ( 1 === $flagForEnterUserToBannedList ) {
            $billing_email = filter_input( INPUT_POST, 'edd_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $email = wcblu_safe_trim( $billing_email );
            $query = wp_cache_get( 'edd_blocked_user_data_key' );
            if ( false === $query ) {
                $args = array(
                    'post_status' => 'publish',
                    'post_type'   => 'blocked_user',
                    's'           => $email,
                );
                $args_query = new WP_Query($args);
                if ( !empty( $args_query->posts ) ) {
                    $get_posts = $args_query->posts;
                    $query = $get_posts[0]->ID;
                }
                wp_cache_set( 'edd_blocked_user_data_key', $query );
            }
            $query = wp_cache_get( 'edd_blocked_user_data_key' );
            if ( false !== $query ) {
                $post_id = wp_cache_get( 'edd_blocked_user_data_key_for_post_id' );
                if ( false === $post_id ) {
                    $args_for_post_id = array(
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                        's'           => $email,
                    );
                    $args_for_post_id_query = new WP_Query($args_for_post_id);
                    if ( !empty( $args_for_post_id_query->posts ) ) {
                        $get_posts = $args_for_post_id_query->posts;
                        $post_id = $get_posts[0]->ID;
                    }
                    wp_cache_set( 'edd_blocked_user_data_key_for_post_id', $post_id );
                }
                $meta = get_post_meta( $post_id, 'Attempt', true );
                $meta++;
                update_post_meta( $post_id, 'Attempt', $meta );
                update_post_meta( $post_id, 'First Name', filter_input( INPUT_POST, 'edd_first', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Last Name', filter_input( INPUT_POST, 'edd_last', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'WhereUserBanned', 'Place Order' );
                $post_status = get_post_status( $post_id );
                if ( 'trash' === $post_status ) {
                    wp_update_post( array(
                        'ID'          => $post_id,
                        'post_status' => 'publish',
                    ) );
                }
            } else {
                $user = array(
                    'post_title'  => $email,
                    'post_status' => 'publish',
                    'post_type'   => 'blocked_user',
                );
                $post_id = wp_insert_post( $user );
                update_post_meta( $post_id, 'First Name', filter_input( INPUT_POST, 'edd_first', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Last Name', filter_input( INPUT_POST, 'edd_last', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
                update_post_meta( $post_id, 'Attempt', '1' );
                update_post_meta( $post_id, 'WhereUserBanned', 'Place Order' );
            }
        }
    }

    /**
     * @param $email
     *
     * @return string
     * function to return error notice if blacklisted, otherwise false
     */
    private function verify_email( $email ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelectedEmail = ( !empty( $getpluginoptionarray['wcblu_block_email'] ) ? $getpluginoptionarray['wcblu_block_email'] : '' );
        if ( '' !== $fetchSelectedEmail ) {
            // check if user email is blacklisted
            if ( is_array( $fetchSelectedEmail ) ) {
                foreach ( $fetchSelectedEmail as $blacklist ) {
                    $blacklist = strtolower( $blacklist );
                    $email = strtolower( $email );
                    if ( $blacklist === $email ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_email_msg'] ) ? __( 'This email has been blacklisted. Please try another email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_email_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $email
     *
     * @return string
     * Function to return validate domain
     */
    private function verify_domain( $email ) {
        // store only the domain part of email address
        $email = explode( '@', $email );
        $email = $email[1];
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelecetedDomain = ( !empty( $getpluginoptionarray['wcblu_block_domain'] ) ? $getpluginoptionarray['wcblu_block_domain'] : array() );
        if ( '' !== $fetchSelecetedDomain ) {
            // add external domains
            if ( !empty( $getpluginoptionarray['wcblu_enable_ext_bl'] ) && '1' === $getpluginoptionarray['wcblu_enable_ext_bl'] ) {
                $external_list = $this->get_external_blacklisted_domains__premium_only();
                if ( !empty( $external_list ) && '' !== $external_list ) {
                    $blacklists = array_merge( $fetchSelecetedDomain, $external_list );
                } else {
                    $blacklists = array();
                }
            } else {
                $blacklists = $fetchSelecetedDomain;
            }
            // check if user email is blacklisted
            if ( is_array( $blacklists ) ) {
                foreach ( $blacklists as $blacklist ) {
                    $blacklist = strtolower( $blacklist );
                    $email = strtolower( $email );
                    if ( $email === $blacklist ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            // add external domains
            if ( !empty( $getpluginoptionarray['wcblu_enable_ext_bl'] ) && '1' === $getpluginoptionarray['wcblu_enable_ext_bl'] ) {
                $external_list = $this->get_external_blacklisted_domains__premium_only();
                // check if user email is blacklisted
                if ( is_array( $external_list ) ) {
                    foreach ( $external_list as $blacklist ) {
                        $blacklist = strtolower( $blacklist );
                        $email = strtolower( $email );
                        if ( $email === $blacklist ) {
                            $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] ) );
                            break;
                        }
                    }
                }
            } else {
                $status = '';
            }
        }
        return $status;
    }

    /**
     * @param $phone
     *
     * @return string
     * Function to return verify phone number
     */
    private function verify_phone( $phone ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchSelectedPhone = ( !empty( $getpluginoptionarray['wcblu_block_phone'] ) ? $getpluginoptionarray['wcblu_block_phone'] : '' );
        $status = '';
        if ( isset( $fetchSelectedPhone ) && !empty( $fetchSelectedPhone ) ) {
            if ( is_array( $fetchSelectedPhone ) ) {
                foreach ( $fetchSelectedPhone as $singlePhone ) {
                    $phone_exc = explode( '*', $singlePhone );
                    if ( isset( $phone_exc[0] ) && !empty( $phone_exc[0] ) ) {
                        $phone_wildcard = $phone_exc[0];
                    } else {
                        $phone_wildcard = '';
                    }
                    $phone_wildcar_length = strlen( $phone_wildcard );
                    $match_phone_wildcard = substr( $phone, 0, $phone_wildcar_length );
                    if ( $singlePhone === $phone || $phone_wildcard === $match_phone_wildcard ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'Your Phone number has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_phone_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $country
     * @param $state
     * @param $zip
     * @param $packages
     *
     * @return string
     * function to return verify zones
     */
    private function verify_zone( $country, $state, $zip ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted zone from database
        $fetchSelecetedzone = ( !empty( $getpluginoptionarray['wcblu_block_zone'] ) ? array_filter( $getpluginoptionarray['wcblu_block_zone'] ) : '' );
        if ( isset( $fetchSelecetedzone ) && !empty( $fetchSelecetedzone ) ) {
            $delivery_zones = WC_Shipping_Zones::get_zones();
            if ( !empty( $delivery_zones ) && isset( $delivery_zones ) ) {
                if ( is_array( $delivery_zones ) ) {
                    foreach ( $delivery_zones as $delivery_zones_result ) {
                        $delivery_zone_country = $delivery_zones_result['zone_locations'];
                        if ( !empty( $delivery_zone_country ) ) {
                            if ( is_array( $delivery_zone_country ) ) {
                                foreach ( $delivery_zone_country as $delivery_zone_location_result ) {
                                    $code = $delivery_zone_location_result->code;
                                    $type = $delivery_zone_location_result->type;
                                    if ( !empty( $type ) && 'continent' === $type ) {
                                        $continents = WC_Countries::get_continents();
                                        // @phpstan-ignore-line
                                        $continents_and_ccs = wp_list_pluck( $continents, 'countries' );
                                        if ( is_array( $continents_and_ccs ) ) {
                                            foreach ( $continents_and_ccs as $continent_code => $countries ) {
                                                if ( !empty( $continent_code ) && $code === $continent_code ) {
                                                    if ( in_array( $country, $countries, true ) ) {
                                                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_zone_msg'] ) ? __( 'Your Zone has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_zone_msg'] ) );
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //country wise check code
                                    if ( $country === $code || $zip === $code || $zip === $state ) {
                                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_zone_msg'] ) ? __( 'Your Zone has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_zone_msg'] ) );
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }

    /**
     * 
     * function to return varify address
     */
    private function verify_address( $address1, $address2 ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchSelecetedAddress = ( !empty( $getpluginoptionarray['wcblu_block_address'] ) ? $getpluginoptionarray['wcblu_block_address'] : '' );
        $status = '';
        $Address1 = wc_strtolower( $address1 );
        $Address2 = wc_strtolower( $address2 );
        if ( isset( $fetchSelecetedAddress ) && !empty( $fetchSelecetedAddress ) ) {
            if ( is_array( $fetchSelecetedAddress ) ) {
                foreach ( $fetchSelecetedAddress as $value ) {
                    $value = wc_strtolower( $value );
                    if ( strpos( $Address1, $value ) !== false || strpos( $Address2, $value ) !== false ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_address_msg'] ) ? __( 'Your Address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_address_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $ip
     *
     * @return string
     * Function to return verify ip address
     */
    private function verify_ip( $ip ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelectedIpAddress = ( !empty( $getpluginoptionarray['wcblu_block_ip'] ) ? $getpluginoptionarray['wcblu_block_ip'] : array() );
        if ( '' !== $fetchSelectedIpAddress ) {
            if ( is_array( $fetchSelectedIpAddress ) ) {
                foreach ( $fetchSelectedIpAddress as $ipSingle ) {
                    /**
                     * Check the IP range validation
                     */
                    if ( strpos( $ipSingle, '-' ) !== false ) {
                        $customer_ip_array = explode( ', ', $ip );
                        if ( !empty( $customer_ip_array ) && is_array( $customer_ip_array ) ) {
                            foreach ( $customer_ip_array as $ip_val ) {
                                $customer_ip_slots_array = explode( '.', $ip_val );
                                /**
                                 * Create IP range array
                                 */
                                $saved_ip_range_array = explode( '-', $ipSingle );
                                /**
                                 * Create IP slot array
                                 */
                                $saved_ip_start_range_array = explode( '.', $saved_ip_range_array[0] );
                                $saved_ip_end_range_array = explode( '.', $saved_ip_range_array[1] );
                                /**
                                 * Here checking the first three slot of the IP is same or not and then
                                 * checking the IP last slot range is in between or not
                                 */
                                if ( $saved_ip_start_range_array[0] === $customer_ip_slots_array[0] && $saved_ip_start_range_array[1] === $customer_ip_slots_array[1] && $saved_ip_start_range_array[2] === $customer_ip_slots_array[2] && ($saved_ip_start_range_array[3] <= $customer_ip_slots_array[3] && $saved_ip_end_range_array[3] >= $customer_ip_slots_array[3]) ) {
                                    $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] ) );
                                    break;
                                }
                            }
                        }
                    } else {
                        /** IP is not in range then go for simple checking */
                        if ( strpos( $ip, ',' ) !== false ) {
                            $network_id_array = explode( ', ', $ip );
                            if ( is_array( $network_id_array ) ) {
                                foreach ( $network_id_array as $network_ip ) {
                                    if ( $ipSingle === $network_ip ) {
                                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] ) );
                                        break;
                                    }
                                }
                            }
                        } else {
                            if ( $ipSingle === $ip ) {
                                $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] ) );
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $country
     * @param $state
     *
     * @return string
     * function to retirn verify state
     */
    private function verify_state( $country, $state ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelecetedState = ( !empty( $getpluginoptionarray['wcblu_block_state'] ) ? array_filter( $getpluginoptionarray['wcblu_block_state'] ) : '' );
        if ( isset( $fetchSelecetedState ) && !empty( $fetchSelecetedState ) ) {
            $stateFullName = '';
            if ( isset( WC()->countries->states[$country][$state] ) ) {
                $stateFullName = strtolower( WC()->countries->states[$country][$state] );
            }
            if ( is_array( $fetchSelecetedState ) && !empty( $stateFullName ) ) {
                foreach ( $fetchSelecetedState as $singleList ) {
                    $singleList = strtolower( $singleList );
                    if ( $stateFullName === $singleList ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_state_msg'] ) ? __( 'Your State has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_state_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $country
     *
     * @return string
     * function to retirn verify country
     */
    private function verify_country( $country ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelecetedountry = ( !empty( $getpluginoptionarray['wcblu_block_country'] ) ? array_filter( $getpluginoptionarray['wcblu_block_country'] ) : '' );
        if ( isset( $fetchSelecetedountry ) && !empty( $fetchSelecetedountry ) ) {
            if ( in_array( $country, $fetchSelecetedountry, true ) ) {
                $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_country_msg'] ) ? __( 'Your Country has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_country_msg'] ) );
                $country_name = WC()->countries->countries[$country];
                if ( !empty( $country_name ) ) {
                    $status = str_replace( "{country}", $country_name, $status );
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $zip
     *
     * @return string
     * function to return verify zip
     */
    private function verify_zip( $zip ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        $fetchSelecetedZip = ( !empty( $getpluginoptionarray['wcblu_block_zip'] ) ? $getpluginoptionarray['wcblu_block_zip'] : '' );
        if ( isset( $fetchSelecetedZip ) && !empty( $fetchSelecetedZip ) ) {
            if ( is_array( $fetchSelecetedZip ) ) {
                foreach ( $fetchSelecetedZip as $singleZip ) {
                    $zip = strtolower( $zip );
                    $singleZip = strtolower( $singleZip );
                    if ( $singleZip === $zip ) {
                        $status = convert_smilies( ( empty( $getpluginoptionarray['wcblu_zpcode_msg'] ) ? __( 'Your Zip has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_zpcode_msg'] ) );
                        break;
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * function to return valudate extra field for registration (For WooCommerce registration)
     * 
     * @param $validation_error
     * @param $username
     * @param $password
     * @param $email
     */
    public function wooc_validate_extra_register_fields(
        $validation_error,
        $username,
        $password,
        $email
    ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $getregistertype = ( !empty( $getpluginoptionarray['wcblu_register_type'] ) ? $getpluginoptionarray['wcblu_register_type'] : '' );
        if ( isset( $getregistertype ) && !empty( $getregistertype ) && '1' === $getregistertype ) {
            $flagForEnterUserToBannedList = 0;
            //Test if it is a shared client
            $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
            $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
            $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
            if ( !empty( $http_client_ip ) ) {
                $ip = $http_client_ip;
                //Is it a proxy address
            } elseif ( !empty( $http_x_forwarded_for ) ) {
                $ip = $http_x_forwarded_for;
            } else {
                $ip = $remote_addr;
            }
            $errorIp = $this->verify_ip_register( $ip );
            if ( $errorIp ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            $email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            // return if billing email doesn't exists
            if ( !$email ) {
                $validation_error_msg = __( 'Please enter email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                $validation_error->add( 'error', $validation_error_msg );
                return $validation_error;
            }
            $email = wcblu_safe_trim( $email );
            // return if billing email is unvalid
            if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $validation_error_msg = __( 'Please enter valid email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                $validation_error->add( 'error', $validation_error_msg );
                return $validation_error;
            }
            $errorEmail = $this->verify_email_register( $email );
            if ( $errorEmail ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_email_msg'] ) ? __( 'This email has been blacklisted. Please try another email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_email_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            $errorDomain = $this->verify_domain_register( $email );
            if ( $errorDomain ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            if ( 1 === $flagForEnterUserToBannedList ) {
                $query = wp_cache_get( 'blocked_another_user_data_key' );
                if ( false === $query ) {
                    $args = array(
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                        's'           => $email,
                    );
                    $args_query = new WP_Query($args);
                    if ( !empty( $args_query->posts ) ) {
                        $get_posts = $args_query->posts;
                        $query = $get_posts[0]->ID;
                    }
                    wp_cache_set( 'blocked_another_user_data_key', $query );
                }
                $query = wp_cache_get( 'blocked_another_user_data_key' );
                if ( false !== $query ) {
                    $post_id = wp_cache_get( 'blocked_another_user_data_key_for_post_id' );
                    if ( false === $post_id ) {
                        $args_for_post_id = array(
                            'post_status' => 'publish',
                            'post_type'   => 'blocked_user',
                            's'           => $email,
                        );
                        $args_for_post_id_query = new WP_Query($args_for_post_id);
                        if ( !empty( $args_for_post_id_query->posts ) ) {
                            $get_posts = $args_for_post_id_query->posts;
                            $post_id = $get_posts[0]->ID;
                        }
                        wp_cache_set( 'blocked_another_user_data_key_for_post_id', $post_id );
                    }
                    $meta = get_post_meta( $post_id, 'Attempt', true );
                    $meta++;
                    update_post_meta( $post_id, 'Attempt', $meta );
                    update_post_meta( $post_id, 'WhereUserBanned', 'Register' );
                } else {
                    $user = array(
                        'post_title'  => $email,
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                    );
                    $post_id = wp_insert_post( $user );
                    update_post_meta( $post_id, 'Attempt', '1' );
                    update_post_meta( $post_id, 'WhereUserBanned', 'Register' );
                }
            }
        }
        return $validation_error;
    }

    /**
     * Function to return validate admin user validation (For WordPress admin user registration)
     * 
     * @param $validation_error
     * @param $username
     * @param $email
     * 
     * @return $validation_error
     * 
     * @since 2.3.0
     */
    public function wcbfc_admin_user_validation( $validation_error, $username, $email ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $getregistertype = ( !empty( $getpluginoptionarray['wcblu_register_type'] ) ? $getpluginoptionarray['wcblu_register_type'] : '' );
        if ( isset( $getregistertype ) && !empty( $getregistertype ) && '1' === $getregistertype ) {
            $flagForEnterUserToBannedList = 0;
            //Test if it is a shared client
            $http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $http_client_ip = filter_var( $http_client_ip, FILTER_VALIDATE_IP );
            $http_x_forwarded_for = filter_var( $http_x_forwarded_for, FILTER_VALIDATE_IP );
            $remote_addr = filter_var( $remote_addr, FILTER_VALIDATE_IP );
            if ( !empty( $http_client_ip ) ) {
                $ip = $http_client_ip;
                //Is it a proxy address
            } elseif ( !empty( $http_x_forwarded_for ) ) {
                $ip = $http_x_forwarded_for;
            } else {
                $ip = $remote_addr;
            }
            $errorIp = $this->verify_ip_register( $ip );
            if ( $errorIp ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            $email = wcblu_safe_trim( $email );
            // return if billing email is unvalid
            if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $validation_error_msg = __( 'Please enter valid email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                $validation_error->add( 'error', $validation_error_msg );
                return $validation_error;
            }
            $errorEmail = $this->verify_email_register( $email );
            if ( $errorEmail ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_email_msg'] ) ? __( 'This email has been blacklisted. Please try another email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_email_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            $errorDomain = $this->verify_domain_register( $email );
            if ( $errorDomain ) {
                $validation_error_msg = ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] );
                $validation_error->add( 'error', $validation_error_msg );
                $flagForEnterUserToBannedList = 1;
            }
            if ( 1 === $flagForEnterUserToBannedList ) {
                $query = wp_cache_get( 'blocked_another_user_data_key' );
                if ( false === $query ) {
                    $args = array(
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                        's'           => $email,
                    );
                    $args_query = new WP_Query($args);
                    if ( !empty( $args_query->posts ) ) {
                        $get_posts = $args_query->posts;
                        $query = $get_posts[0]->ID;
                    }
                    wp_cache_set( 'blocked_another_user_data_key', $query );
                }
                $query = wp_cache_get( 'blocked_another_user_data_key' );
                if ( false !== $query ) {
                    $post_id = wp_cache_get( 'blocked_another_user_data_key_for_post_id' );
                    if ( false === $post_id ) {
                        $args_for_post_id = array(
                            'post_status' => 'publish',
                            'post_type'   => 'blocked_user',
                            's'           => $email,
                        );
                        $args_for_post_id_query = new WP_Query($args_for_post_id);
                        if ( !empty( $args_for_post_id_query->posts ) ) {
                            $get_posts = $args_for_post_id_query->posts;
                            $post_id = $get_posts[0]->ID;
                        }
                        wp_cache_set( 'blocked_another_user_data_key_for_post_id', $post_id );
                    }
                    $meta = get_post_meta( $post_id, 'Attempt', true );
                    $meta++;
                    update_post_meta( $post_id, 'Attempt', $meta );
                    update_post_meta( $post_id, 'WhereUserBanned', 'Register' );
                } else {
                    $user = array(
                        'post_title'  => $email,
                        'post_status' => 'publish',
                        'post_type'   => 'blocked_user',
                    );
                    $post_id = wp_insert_post( $user );
                    update_post_meta( $post_id, 'Attempt', '1' );
                    update_post_meta( $post_id, 'WhereUserBanned', 'Register' );
                }
            }
        }
        return $validation_error;
    }

    /**
     * @param $ip
     *
     * @return string|void
     * function to return verify ip for registration.
     */
    private function verify_ip_register( $ip ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelectedIpAddress = ( !empty( $getpluginoptionarray['wcblu_block_ip'] ) ? $getpluginoptionarray['wcblu_block_ip'] : array() );
        if ( '' !== $fetchSelectedIpAddress ) {
            if ( is_array( $fetchSelectedIpAddress ) ) {
                foreach ( $fetchSelectedIpAddress as $ipSingle ) {
                    if ( $ip === $ipSingle ) {
                        $status = ( empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? __( 'Your IP address has been blacklisted.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_ip_msg'] );
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    /**
     * @param $email
     *
     * @return string|void
     * function to return verify emails in registration
     */
    private function verify_email_register( $email ) {
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $status = '';
        //get blacklisted domains from database
        $fetchSelectedEmail = ( !empty( $getpluginoptionarray['wcblu_block_email'] ) ? $getpluginoptionarray['wcblu_block_email'] : '' );
        if ( '' !== $fetchSelectedEmail ) {
            if ( is_array( $fetchSelectedEmail ) ) {
                // check if user email is blacklisted
                foreach ( $fetchSelectedEmail as $blacklist ) {
                    $blacklist = strtolower( $blacklist );
                    $email = strtolower( $email );
                    if ( $email === $blacklist ) {
                        $status = ( empty( $getpluginoptionarray['wcblu_email_msg'] ) ? __( 'This email has been blacklisted. Please try another email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_email_msg'] );
                    }
                }
            }
        } else {
            $status = '';
        }
        return $status;
    }

    public function get_client_ip() {
        $ipaddress = '';
        $HTTP_CLIENT_IP = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $HTTP_X_FORWARDED_FOR = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $HTTP_X_FORWARDED = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $HTTP_FORWARDED_FOR = filter_input( INPUT_SERVER, 'HTTP_FORWARDED_FOR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $HTTP_FORWARDED = filter_input( INPUT_SERVER, 'HTTP_FORWARDED', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $REMOTE_ADDR = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $HTTP_CLIENT_IP ) ) {
            $ipaddress = $HTTP_CLIENT_IP;
        } else {
            if ( isset( $HTTP_X_FORWARDED_FOR ) ) {
                $ipaddress = $HTTP_X_FORWARDED_FOR;
            } else {
                if ( isset( $HTTP_X_FORWARDED ) ) {
                    $ipaddress = $HTTP_X_FORWARDED;
                } else {
                    if ( isset( $HTTP_FORWARDED_FOR ) ) {
                        $ipaddress = $HTTP_FORWARDED_FOR;
                    } else {
                        if ( isset( $HTTP_FORWARDED ) ) {
                            $ipaddress = $HTTP_FORWARDED;
                        } else {
                            if ( isset( $REMOTE_ADDR ) ) {
                                $ipaddress = $REMOTE_ADDR;
                            } else {
                                $ipaddress = 'UNKNOWN';
                            }
                        }
                    }
                }
            }
        }
        return $ipaddress;
    }

    /**
     * @param $email
     *
     * @return string|void
     * function to return verify domain for registration
     */
    private function verify_domain_register( $email ) {
        // store only the domain part of email address
        $email = explode( '@', $email );
        $email = $email[1];
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        //get blacklisted domains from database
        $fetchSelecetedDomain = ( !empty( $getpluginoptionarray['wcblu_block_domain'] ) ? $getpluginoptionarray['wcblu_block_domain'] : array() );
        $status = '';
        if ( '' !== $fetchSelecetedDomain ) {
            // add external domains
            if ( !empty( $getpluginoptionarray['wcblu_enable_ext_bl'] ) && '1' === $getpluginoptionarray['wcblu_enable_ext_bl'] ) {
                $external_list = $this->get_external_blacklisted_domains__premium_only();
                if ( !empty( $external_list ) && '' !== $external_list ) {
                    $blacklists = array_merge( $fetchSelecetedDomain, $external_list );
                } else {
                    $blacklists = array();
                }
            } else {
                $blacklists = $fetchSelecetedDomain;
            }
            // check if user email is blacklisted
            if ( is_array( $blacklists ) ) {
                foreach ( $blacklists as $blacklist ) {
                    $blacklist = strtolower( $blacklist );
                    $email = strtolower( $email );
                    if ( $email === $blacklist ) {
                        $status = ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] );
                    }
                }
            }
        } else {
            // add external domains
            if ( !empty( $getpluginoptionarray['wcblu_enable_ext_bl'] ) && '1' === $getpluginoptionarray['wcblu_enable_ext_bl'] ) {
                $external_list = $this->get_external_blacklisted_domains__premium_only();
                // check if user email is blacklisted
                if ( is_array( $external_list ) ) {
                    foreach ( $external_list as $blacklist ) {
                        $blacklist = strtolower( $blacklist );
                        $email = strtolower( $email );
                        if ( $email === $blacklist ) {
                            $status = ( empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? __( 'This email domain has been blacklisted. Please try another email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : $getpluginoptionarray['wcblu_domain_msg'] );
                        }
                    }
                }
            } else {
                $status = '';
            }
        }
        return $status;
    }

    /**
     * @param $order_id
     *
     * Check checkout page is block based or classic.
     */
    public function wcbfc_is_checkout_block() {
        $page_to_check = wc_get_page_id( 'checkout' );
        $content = get_post( $page_to_check )->post_content;
        return strpos( $content, 'block' ) !== false;
    }

    /**
     * Ajax function to check the geo location of the user.
     * 
     * @param $lat, $long
     */
    public function wcblu_geo_location_ajax() {
        if ( !empty( $_POST['latitude'] ) && !empty( $_POST['longitude'] ) ) {
            if ( wp_verify_nonce( isset( $_REQUEST['_wpnonce'] ), 'my-nonce' ) ) {
                return false;
            }
            $getpluginruleopt = get_option( 'wcblu_rules_option', '{}' );
            $getpluginruleoptarray = ( json_decode( $getpluginruleopt, true ) ?: [] );
            $wcbfc_geo_match_key = $getpluginruleoptarray['wcbfc_geo_match_key'] ?? 'bdc_b77be8654f4b4943902160a5d123a21f';
            $lat = sanitize_text_field( $_POST['latitude'] );
            $lng = sanitize_text_field( $_POST['longitude'] );
            $response = wp_remote_get( 'https://api-bdc.net/data/reverse-geocode?latitude=' . $lat . '&longitude=' . $lng . '&localityLanguage=en&key=' . $wcbfc_geo_match_key );
            if ( is_wp_error( $response ) ) {
                echo 'error';
                die;
            }
            if ( isset( $response ) ) {
                $output = json_decode( $response['body'], true );
                if ( !empty( $output ) ) {
                    if ( !empty( $output['city'] ) ) {
                        $g_city = strtolower( $output['city'] );
                        update_option( 'wcblu_geo_loc_city', $g_city );
                    } else {
                        if ( !empty( $output['countryCode'] ) ) {
                            $g_countryCode = strtolower( $output['countryCode'] );
                            update_option( 'wcblu_geo_loc_cntry', $g_countryCode );
                        }
                    }
                    if ( !empty( $output['principalSubdivision'] ) ) {
                        $g_state = strtolower( $output['principalSubdivision'] );
                        update_option( 'wcblu_geo_loc_state', $g_state );
                    }
                    echo 'success';
                    die;
                }
            }
        } else {
            delete_option( 'wcblu_geo_loc_state' );
            delete_option( 'wcblu_geo_loc_city' );
            delete_option( 'wcblu_geo_loc_cntry' );
        }
        die;
    }

    /**
     * Add the captcha field to the checkout page.
     */
    public function wcbfc_captcha_checkout_field() {
        wp_enqueue_script( 'jquery' );
        $getplugingeneralopt = get_option( 'wcblu_general_option' );
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcblu_v2_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v2_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v2_keys_value'] : '' );
        ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="reg_captcha"><?php 
        echo esc_html__( 'Captcha', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>&nbsp;<span class="required">*</span></label>
			<div id="wcbfc-recaptcha-checkout" name="g-recaptcha" class="g-recaptcha" data-sitekey="<?php 
        echo esc_attr( $wcblu_v2_keys_value );
        ?>" data-theme="light" data-size="normal" placeholder=""></div>
			</p>
			<script>
				 var wcbfc_Captcha = null;
				<?php 
        $intval = uniqid( 'interval_' );
        ?>

				var <?php 
        echo esc_attr( $intval );
        ?> = setInterval(function() {

					if(document.readyState === 'complete') {

						clearInterval(<?php 
        echo esc_attr( $intval );
        ?>);
						var $n = jQuery.noConflict();

						$n("#place_order").attr("title", "<?php 
        echo esc_html__( 'Recaptcha is a required field.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>");

						$n(document).on('updated_checkout', function () {
							if (typeof (grecaptcha.render) !== 'undefined' && wcbfc_Captcha === null && $n('#wcbfc-recaptcha-checkout').html().trim() === '') {
								wcbfc_Captcha = grecaptcha.render('wcbfc-recaptcha-checkout', {
										'sitekey': '<?php 
        echo esc_attr( $wcblu_v2_keys_value );
        ?>'
								});
							}
						});
					}
				 }, 100);
				 jQuery('body').trigger('update_checkout');
			</script>
		<?php 
    }

    /**
     * Validate the captcha field.
     */
    public function wcbfc_validate_captcha( $fields, $validation_errors ) {
        $getplugingeneralopt = get_option( 'wcblu_general_option' );
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcblu_v2_secret_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v2_secret_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v2_secret_keys_value'] : '' );
        if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) && !empty( $_POST['woocommerce-process-checkout-nonce'] ) ) {
            $nonce_value = '';
            if ( isset( $_REQUEST['woocommerce-process-checkout-nonce'] ) || isset( $_REQUEST['_wpnonce'] ) ) {
                if ( isset( $_REQUEST['woocommerce-process-checkout-nonce'] ) && !empty( $_REQUEST['woocommerce-process-checkout-nonce'] ) ) {
                    $nonce_value = sanitize_text_field( $_REQUEST['woocommerce-process-checkout-nonce'] );
                } else {
                    if ( isset( $_REQUEST['_wpnonce'] ) && !empty( $_REQUEST['_wpnonce'] ) ) {
                        $nonce_value = sanitize_text_field( $_REQUEST['_wpnonce'] );
                    }
                }
            }
            if ( wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
                if ( 'yes' === get_transient( $nonce_value ) ) {
                    return $validation_errors;
                }
                if ( isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ) {
                    // Google reCAPTCHA API secret key
                    $response = sanitize_text_field( $_POST['g-recaptcha-response'] );
                    // Verify the reCAPTCHA response
                    $verifyResponse = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $wcblu_v2_secret_keys_value . '&response=' . $response );
                    if ( !is_wp_error( $verifyResponse ) && isset( $verifyResponse['body'] ) ) {
                        // Decode json data
                        $responseData = json_decode( $verifyResponse['body'] );
                        // If reCAPTCHA response is valid
                        if ( !$responseData->success ) {
                            $validation_errors->add( 'g-recaptcha_error', __( 'Invalid recaptcha.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
                        } else {
                            if ( 0 !== 3 ) {
                                set_transient( $nonce_value, 'yes', 3 * 60 );
                            }
                        }
                    } else {
                        $validation_errors->add( 'g-recaptcha_error', __( 'Could not get response from recaptcha server.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
                    }
                } else {
                    $validation_errors->add( 'g-recaptcha_error', __( 'Recaptcha is a required field.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
                }
            } else {
                $validation_errors->add( 'g-recaptcha_error', __( 'Could not verify request.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            }
        }
        return $validation_errors;
    }

    /* Related to reCaptcha */
    public function wcbfc_recptcha_v3_request() {
        $getplugingeneralopt = get_option( 'wcblu_general_option' );
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcblu_v3_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v3_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v3_keys_value'] : '' );
        ?>

		<input type="hidden" name="googlerecaptchav3" id="wcafrecaptchav3checkout" class="g-recaptcha-response">
		
		<script type="text/javascript">
			var $n = jQuery.noConflict();
			var grecaptcha_action = '';
			$n(document).ready(function() {
				$n(document).on('updated_checkout', function (e) {
					e.preventDefault();
					grecaptcha.ready(function() {
						grecaptcha.execute('<?php 
        echo esc_attr( $wcblu_v3_keys_value );
        ?>', {action: 'wcbfc_validate_v3_recaptcha'}).then(function(token) {
							$n('#wcafrecaptchav3checkout').val(token);
						});
					});
				});
			});
		</script>
		<?php 
    }

    /* Related to reCaptcha v3 */
    public function wcbfc_validate_v3_recaptcha( $fields, $validation_errors ) {
        $getplugingeneralopt = get_option( 'wcblu_general_option' );
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcblu_v3_secret_keys_value = ( !empty( $getplugingeneraloptarray['wcblu_v3_secret_keys_value'] ) ? $getplugingeneraloptarray['wcblu_v3_secret_keys_value'] : '' );
        if ( wp_verify_nonce( isset( $_REQUEST['_wpnonce'] ), 'my-nonce' ) ) {
            return false;
        }
        $REMOTE_ADDR = '';
        $captcha = '';
        $nonce_value = '';
        if ( isset( $_REQUEST['woocommerce-process-checkout-nonce'] ) || isset( $_REQUEST['_wpnonce'] ) ) {
            if ( isset( $_REQUEST['woocommerce-process-checkout-nonce'] ) && !empty( $_REQUEST['woocommerce-process-checkout-nonce'] ) ) {
                $nonce_value = sanitize_text_field( $_REQUEST['woocommerce-process-checkout-nonce'] );
            } else {
                if ( isset( $_REQUEST['_wpnonce'] ) && !empty( $_REQUEST['_wpnonce'] ) ) {
                    $nonce_value = sanitize_text_field( $_REQUEST['_wpnonce'] );
                }
            }
        }
        if ( isset( $_POST['googlerecaptchav3'] ) && !empty( $_POST['googlerecaptchav3'] ) ) {
            $captcha = sanitize_text_field( $_POST['googlerecaptchav3'] );
            $get_REMOTE_ADDR = filter_input(
                INPUT_SERVER,
                'REMOTE_ADDR',
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
            if ( isset( $get_REMOTE_ADDR ) && !empty( $_POST['googlerecaptchav3'] ) ) {
                $REMOTE_ADDR = sanitize_text_field( $get_REMOTE_ADDR );
            }
            $response = file_get_contents( 
                // phpcs:ignore
                'https://www.google.com/recaptcha/api/siteverify?secret=' . $wcblu_v3_secret_keys_value . '&response=' . $captcha . '&remoteip=' . $REMOTE_ADDR
             );
            $response_data = json_decode( $response, true );
            if ( !is_wp_error( $response_data ) && isset( $response_data['success'] ) ) {
                if ( false === $response_data['success'] ) {
                    $validation_errors->add( 'g-recaptcha_error', __( 'Invalid recaptcha.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
                } else {
                    if ( $response_data['score'] <= 0.5 ) {
                        $validation_errors->add( 'g-recaptcha_error', __( 'You are not a human, please refresh page.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
                    }
                    if ( 0 !== 3 ) {
                        set_transient( $nonce_value, 'yes', 3 * 60 );
                    }
                }
            } else {
                $validation_errors->add( 'g-recaptcha_error', __( 'Could not get response from recaptcha server, please refresh page.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
            }
        } else {
            $validation_errors->add( 'g-recaptcha_error', __( 'Recaptcha not responding, please refresh page.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
        }
        return $validation_errors;
    }

    /**
     * Set transient to lock down WooCommerce after multiple failed orders.
     *
     * @param int $order_id Order ID.
     */
    public function wcblu_monitor_failed_orders_behavior() {
        $getplugingeneralopt = get_option( 'wcblu_general_option' );
        $getplugingeneraloptarray = json_decode( $getplugingeneralopt, true );
        $wcbfc_cbf_status = ( !empty( $getplugingeneraloptarray['wcbfc_cbf_status'] ) ? (int) $getplugingeneraloptarray['wcbfc_cbf_status'] : 0 );
        $wcbfc_cbf_attempts = ( !empty( $getplugingeneraloptarray['wcbfc_cbf_attempts'] ) ? (int) $getplugingeneraloptarray['wcbfc_cbf_attempts'] : 0 );
        $wcbfc_cbf_time = ( !empty( $getplugingeneraloptarray['wcbfc_cbf_time'] ) ? (int) $getplugingeneraloptarray['wcbfc_cbf_time'] : 0 );
        if ( isset( $wcbfc_cbf_status ) && 1 === $wcbfc_cbf_status ) {
            $args = array(
                'status'       => 'failed',
                'date_created' => '>' . (time() - $wcbfc_cbf_time * MINUTE_IN_SECONDS),
                'limit'        => -1,
                'return'       => 'ids',
            );
            $failed_orders = wc_get_orders( $args );
            $failed_count = ( is_array( $failed_orders ) ? count( $failed_orders ) : 0 );
            if ( $failed_count >= $wcbfc_cbf_attempts ) {
                set_transient( 'wcbfc_failed_order_lock', time(), 10 * MINUTE_IN_SECONDS );
            }
        }
    }

    /**
     * Disable credit card payments if a lock is active.
     *
     * @param array $gateways Available payment gateways.
     */
    public function wcblu_block_checkout_on_transition( $order ) {
        $plugin_options_raw = get_option( 'wcblu_general_option' );
        $plugin_options = json_decode( $plugin_options_raw, true );
        $block_status = ( !empty( $plugin_options['wcbfc_cbf_status'] ) ? (int) $plugin_options['wcbfc_cbf_status'] : 0 );
        if ( 1 === $block_status && get_transient( 'wcbfc_failed_order_lock' ) ) {
            $error_msg = __( 'Checkout is temporarily disabled due to multiple failed orders. Please try again later.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            $order_id = ( is_a( $order, 'WC_Order' ) ? $order->get_id() : (int) $order );
            if ( class_exists( 'Automattic\\WooCommerce\\Utilities\\OrderUtil' ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
                $order_obj = wc_get_order( $order_id );
                if ( $order_obj ) {
                    $order_obj->delete( true );
                }
            } else {
                wp_delete_post( $order_id, true );
            }
            if ( !$this->wcbfc_is_checkout_block() ) {
                wp_send_json( [
                    'result'   => 'failure',
                    'messages' => "<ul class='woocommerce-error' role='alert'><li>" . esc_html( $error_msg ) . "</li></ul>",
                ] );
            } else {
                throw new Exception(esc_html( $error_msg ));
            }
        }
    }

    /**
     * Function to get country by ip (Using wp_remote_post)
     * 
     * @param string $ip
     * 
     * @return array
     * 
     * @since 2.3.0
     */
    public function get_country_by_ip_wp( $ip ) {
        // Validate IP
        if ( !filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return [];
        }
        $url = 'https://ipapi.co/' . rawurlencode( $ip ) . '/json/';
        // wp_remote_post
        $response = wp_remote_post( $url, [
            'timeout'   => 5,
            'sslverify' => true,
        ] );
        if ( is_wp_error( $response ) ) {
            return [];
        }
        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            return [];
        }
        $data = json_decode( $body, true );
        return ( is_array( $data ) ? $data : [] );
    }

}
