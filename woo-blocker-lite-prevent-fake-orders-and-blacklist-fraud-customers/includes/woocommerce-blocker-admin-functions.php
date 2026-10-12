<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Safe trim function that handles null values for PHP 8.1+ compatibility
 *
 * @param mixed $value The value to trim.
 * @return string Trimmed string or empty string if null.
 */
function wcblu_safe_trim(  $value  ) {
    return trim( (string) ($value ?? '') );
}

/**
 * Saved protection mode for automatic fraud check.
 *
 * @return string `automatic` or `monitor`.
 */
function wcbfc_get_protection_mode() {
    $options = json_decode( get_option( 'wcblu_general_option' ), true );
    if ( !is_array( $options ) ) {
        return 'automatic';
    }
    return ( isset( $options['wcbfc_protection_mode'] ) && 'monitor' === $options['wcbfc_protection_mode'] ? 'monitor' : 'automatic' );
}

/**
 * Whether score-based enforcement should be skipped.
 *
 * @return bool
 */
function wcbfc_is_monitor_only_mode() {
    if ( !(function_exists( 'wbpfoabfc_fs' ) && wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code()) ) {
        return false;
    }
    $options = json_decode( get_option( 'wcblu_general_option' ), true );
    if ( !is_array( $options ) || empty( $options['wcbfc_fraud_check_status'] ) || 'on' !== $options['wcbfc_fraud_check_status'] ) {
        return false;
    }
    return 'monitor' === wcbfc_get_protection_mode();
}

/**
 * Whether the current request can use Pro blacklist toggles.
 *
 * @return bool
 */
function wcbfc_can_use_blacklist_toggles() {
    return function_exists( 'wbpfoabfc_fs' ) && wbpfoabfc_fs()->is__premium_only() && wbpfoabfc_fs()->can_use_premium_code();
}

/**
 * Whether the blocked-email list should be checked.
 * Lite always checks. Pro defaults on when the setting is missing.
 *
 * @return bool
 */
function wcbfc_is_email_blacklist_enabled() {
    if ( !wcbfc_can_use_blacklist_toggles() ) {
        return true;
    }
    $options = json_decode( get_option( 'wcblu_option' ), true );
    if ( !is_array( $options ) || !isset( $options['wcblu_enable_block_email'] ) ) {
        return true;
    }
    return '1' === (string) $options['wcblu_enable_block_email'];
}

/**
 * Whether the blocked-IP list should be checked.
 * Lite always checks. Pro defaults on when the setting is missing.
 *
 * @return bool
 */
function wcbfc_is_ip_blacklist_enabled() {
    if ( !wcbfc_can_use_blacklist_toggles() ) {
        return true;
    }
    $options = json_decode( get_option( 'wcblu_option' ), true );
    if ( !is_array( $options ) || !isset( $options['wcblu_enable_block_ip'] ) ) {
        return true;
    }
    return '1' === (string) $options['wcblu_enable_block_ip'];
}

/**
 * Split textarea whitelist values into trimmed lines.
 *
 * @param mixed $text Raw textarea value.
 * @return array
 */
function wcbfc_get_whitelist_lines(  $text  ) {
    if ( !is_string( $text ) || '' === $text ) {
        return array();
    }
    $lines = array_map( 'wcblu_safe_trim', explode( "\n", $text ) );
    return array_values( array_filter( $lines, static function ( $line ) {
        return '' !== $line;
    } ) );
}

/**
 * Normalize a name for exact case-insensitive whitelist matching.
 *
 * @param mixed $name Name value.
 * @return string
 */
function wcbfc_normalize_whitelist_name(  $name  ) {
    $name = wcblu_safe_trim( $name );
    if ( '' === $name ) {
        return '';
    }
    return ( function_exists( 'wc_strtolower' ) ? wc_strtolower( $name ) : strtolower( $name ) );
}

/**
 * Normalize a phone number to digits only.
 *
 * @param mixed $phone Phone value.
 * @return string
 */
function wcbfc_normalize_whitelist_phone(  $phone  ) {
    return preg_replace( '/\\D+/', '', (string) $phone );
}

/**
 * Whether a name exists in a whitelist list.
 *
 * @param mixed $name Name value.
 * @param array $list Whitelist lines.
 * @return bool
 */
function wcbfc_is_name_whitelisted(  $name, $list  ) {
    $normalized = wcbfc_normalize_whitelist_name( $name );
    if ( '' === $normalized || !is_array( $list ) ) {
        return false;
    }
    foreach ( $list as $item ) {
        if ( $normalized === wcbfc_normalize_whitelist_name( $item ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Whether a phone matches a whitelist list, including country-code variants.
 *
 * @param mixed $phone Phone value.
 * @param array $list  Whitelist lines.
 * @return bool
 */
function wcbfc_is_phone_whitelisted(  $phone, $list  ) {
    $normalized = wcbfc_normalize_whitelist_phone( $phone );
    if ( strlen( $normalized ) < 7 || !is_array( $list ) ) {
        return false;
    }
    foreach ( $list as $item ) {
        $item_norm = wcbfc_normalize_whitelist_phone( $item );
        if ( strlen( $item_norm ) < 7 ) {
            continue;
        }
        if ( $normalized === $item_norm ) {
            return true;
        }
        if ( substr( $normalized, -strlen( $item_norm ) ) === $item_norm || substr( $item_norm, -strlen( $normalized ) ) === $normalized ) {
            return true;
        }
    }
    return false;
}

/**
 * Check billing and shipping first or last name against a whitelist.
 *
 * @param WC_Order $order Order object.
 * @param array    $list  Whitelist lines.
 * @param string   $which `first` or `last`.
 * @return bool
 */
function wcbfc_order_has_whitelisted_name(  $order, $list, $which  ) {
    if ( !is_a( $order, 'WC_Order' ) ) {
        return false;
    }
    $values = ( 'last' === $which ? array($order->get_billing_last_name(), $order->get_shipping_last_name()) : array($order->get_billing_first_name(), $order->get_shipping_first_name()) );
    foreach ( $values as $value ) {
        if ( wcbfc_is_name_whitelisted( $value, $list ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Check billing and shipping phone against a whitelist.
 *
 * @param WC_Order $order Order object.
 * @param array    $list  Whitelist lines.
 * @return bool
 */
function wcbfc_order_has_whitelisted_phone(  $order, $list  ) {
    if ( !is_a( $order, 'WC_Order' ) ) {
        return false;
    }
    $values = array($order->get_billing_phone());
    if ( method_exists( $order, 'get_shipping_phone' ) ) {
        $values[] = $order->get_shipping_phone();
    }
    foreach ( $values as $value ) {
        if ( wcbfc_is_phone_whitelisted( $value, $list ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Skip score-based hold/cancel when a whitelist rule matches.
 *
 * @param WC_Order $order                 Order object.
 * @param int      $order_id              Order ID.
 * @param string   $note                  Order note.
 * @param mixed    $fscore                Current score.
 * @param int      $cancel_score          Cancel threshold.
 * @param int      $hold_score            Hold threshold.
 * @param array    $skip_status_change    Statuses to skip.
 * @return void
 */
function wcbfc_bypass_score_status_for_whitelist(
    $order,
    $order_id,
    $note,
    $fscore,
    $cancel_score,
    $hold_score,
    $skip_status_change
) {
    $new_status = apply_filters(
        'wcbfc_new_order_status',
        null,
        $order_id,
        $order
    );
    if ( $fscore >= $cancel_score && 0 !== $cancel_score ) {
        $new_status = 'processing';
    } elseif ( $fscore >= $hold_score && 0 !== $hold_score ) {
        $new_status = 'processing';
    }
    if ( !in_array( $order->get_status(), $skip_status_change, true ) && null !== $new_status ) {
        if ( 'processing' !== $order->get_status() ) {
            $order->update_status( $new_status, $note );
        } else {
            $order->add_order_note( $note );
        }
    }
    if ( empty( $new_status ) ) {
        $order->add_order_note( $note );
    }
    $order->update_meta_data( 'wcbfc_order_score', 0 );
    $order->save();
}

/**
 * Remove names from a block list when they match a whitelist.
 *
 * @param mixed  $block_list     Existing blocked names.
 * @param string $whitelist_text Whitelist textarea value.
 * @return array
 */
function wcbfc_strip_whitelisted_names_from_block_list(  $block_list, $whitelist_text  ) {
    if ( !is_array( $block_list ) || empty( $block_list ) ) {
        return ( is_array( $block_list ) ? $block_list : array() );
    }
    $whitelist = wcbfc_get_whitelist_lines( $whitelist_text );
    if ( empty( $whitelist ) ) {
        return $block_list;
    }
    return array_values( array_filter( $block_list, static function ( $item ) use($whitelist) {
        return !wcbfc_is_name_whitelisted( $item, $whitelist );
    } ) );
}

/**
 * Remove phones from a block list when they match a whitelist.
 *
 * @param mixed  $block_list     Existing blocked phones.
 * @param string $whitelist_text Whitelist textarea value.
 * @return array
 */
function wcbfc_strip_whitelisted_phones_from_block_list(  $block_list, $whitelist_text  ) {
    if ( !is_array( $block_list ) || empty( $block_list ) ) {
        return ( is_array( $block_list ) ? $block_list : array() );
    }
    $whitelist = wcbfc_get_whitelist_lines( $whitelist_text );
    if ( empty( $whitelist ) ) {
        return $block_list;
    }
    return array_values( array_filter( $block_list, static function ( $item ) use($whitelist) {
        return !wcbfc_is_phone_whitelisted( $item, $whitelist );
    } ) );
}

/**
 * Fields that can be blocked from an order.
 *
 * @param WC_Order|null $order Order object.
 * @return array
 */
function wcbfc_get_order_blockable_fields(  $order  ) {
    $first_name = ( is_a( $order, 'WC_Order' ) ? wcblu_safe_trim( $order->get_billing_first_name() ) : '' );
    $last_name = ( is_a( $order, 'WC_Order' ) ? wcblu_safe_trim( $order->get_billing_last_name() ) : '' );
    $email = ( is_a( $order, 'WC_Order' ) ? wcblu_safe_trim( $order->get_billing_email() ) : '' );
    $ip = ( is_a( $order, 'WC_Order' ) ? wcblu_safe_trim( $order->get_customer_ip_address() ) : '' );
    return array(
        'first_name' => array(
            'label'    => __( 'First Name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'value'    => $first_name,
            'list_key' => 'wcblu_block_first_name',
            'default'  => false,
            'match'    => 'name',
        ),
        'last_name'  => array(
            'label'    => __( 'Last Name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'value'    => $last_name,
            'list_key' => 'wcblu_block_last_name',
            'default'  => false,
            'match'    => 'name',
        ),
        'email'      => array(
            'label'    => __( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'value'    => $email,
            'list_key' => 'wcblu_block_email',
            'default'  => false,
            'match'    => 'exact',
        ),
        'ip'         => array(
            'label'    => __( 'IP Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'value'    => $ip,
            'list_key' => 'wcblu_block_ip',
            'default'  => false,
            'match'    => 'exact',
        ),
    );
}

/**
 * Whether a value is already in a block list.
 *
 * @param mixed  $value Value to check.
 * @param array  $list  Block list.
 * @param string $match `exact` or `name`.
 * @return bool
 */
function wcbfc_is_value_in_block_list(  $value, $list, $match = 'exact'  ) {
    if ( '' === $value || !is_array( $list ) ) {
        return false;
    }
    if ( 'name' === $match ) {
        return wcbfc_is_name_whitelisted( $value, $list );
    }
    return in_array( $value, $list, true );
}

/**
 * Remove one value from a block list.
 *
 * @param array  $list  Block list.
 * @param string $value Value to remove.
 * @param string $match `exact` or `name`.
 * @return array
 */
function wcbfc_remove_value_from_block_list(  $list, $value, $match = 'exact'  ) {
    if ( !is_array( $list ) || '' === $value ) {
        return ( is_array( $list ) ? $list : array() );
    }
    if ( 'name' === $match ) {
        return wcbfc_strip_whitelisted_names_from_block_list( $list, $value );
    }
    return array_values( array_filter( $list, static function ( $item ) use($value) {
        return (string) $item !== (string) $value;
    } ) );
}

/**
 * Country name from ISO code.
 *
 * @param string $code Country code.
 * @return string
 */
function wcbfc_get_country_label(  $code  ) {
    $code = wcblu_safe_trim( $code );
    if ( '' === $code ) {
        return '';
    }
    if ( function_exists( 'WC' ) && WC()->countries ) {
        $countries = WC()->countries->get_countries();
        if ( isset( $countries[$code] ) ) {
            return $countries[$code];
        }
    }
    return $code;
}

/**
 * UI map for fraud score rules.
 *
 * @return array
 */
function wcbfc_get_fraud_rule_ui_map() {
    return array(
        'first_order'                           => array(
            'title'       => __( 'First order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'This is the customer\'s first order with these billing details.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-cart',
        ),
        'first_order_processing'                => array(
            'title'       => __( 'No completed orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Customer has ordered before, but has never completed an order.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-clipboard',
        ),
        'billing_shipping_address'              => array(
            'title'       => __( 'Billing and shipping addresses differ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Customer entered different billing and delivery details.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-location',
        ),
        'behind_the_proxy'                      => array(
            'title'       => __( 'Ordered behind a proxy', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Customer ordered from behind a proxy or VPN.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-privacy',
        ),
        'international_order'                   => array(
            'title'       => __( 'International order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Billing country differs from your store\'s usual domestic orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-admin-site-alt3',
        ),
        'unknown_origin'                        => array(
            'title'       => __( 'Unknown order origin', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'The order origin could not be verified.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-hidden',
        ),
        'temporary_email'                       => array(
            'title'       => __( 'Free email address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Email is a known free email address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-email',
        ),
        'high_risk_email'                       => array(
            'title'       => __( 'High-risk email domain', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Email address is from a high-risk domain.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-email-alt',
        ),
        'blocked_user_email'                    => array(
            'title'       => __( 'Blocked user email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Billing email matches a blocked user.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-dismiss',
        ),
        'unsafe_countries'                      => array(
            'title'       => __( 'Risk country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Order is from a country marked as high risk.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-warning',
        ),
        'Billing_Phone_Matches_Billing_Country' => array(
            'title'       => __( 'Phone number country mismatch', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Phone country code does not match the billing country.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-phone',
        ),
        'geo_location_not_matched_address'      => array(
            'title'       => __( 'Geo location mismatch', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Geo location does not match the billing or shipping address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-admin-site',
        ),
        'ip_multiple_order_Details'             => array(
            'title'       => __( 'Multiple orders from this IP', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Several recent orders were placed from the same IP address with different details.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-groups',
        ),
        'high_value'                            => array(
            'title'       => __( 'High order value', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Order total is higher than the store average threshold.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-money-alt',
        ),
        'high_amount'                           => array(
            'title'       => __( 'Maximum order amount exceeded', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Order exceeds the maximum allowed amount.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-chart-bar',
        ),
        'velocities'                            => array(
            'title'       => __( 'Multiple orders from this IP', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Several recent orders were placed from the same IP address.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-admin-users',
        ),
        'cc_velocities'                         => array(
            'title'       => __( 'Too many card declines', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Maximum number of credit card declines was reached.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-index-card',
        ),
        'maximum_allowed_no_order'              => array(
            'title'       => __( 'Order limit reached', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description' => __( 'Maximum allowed orders in the selected time window was reached.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'        => 'dashicons-clock',
        ),
    );
}

/**
 * Detected phone dial code from an order phone number.
 *
 * @param string $phone Phone number.
 * @return string
 */
function wcbfc_get_detected_phone_dial_code(  $phone  ) {
    $phone = preg_replace( '/\\D+/', '', (string) $phone );
    if ( '' === $phone || !function_exists( 'WC' ) || !WC()->countries ) {
        return '';
    }
    $codes = array();
    if ( method_exists( WC()->countries, 'get_country_calling_code' ) ) {
        foreach ( WC()->countries->get_countries() as $iso => $name ) {
            $calling = WC()->countries->get_country_calling_code( $iso );
            $calling = ( is_array( $calling ) ? $calling[0] : $calling );
            $digits = preg_replace( '/\\D+/', '', (string) $calling );
            if ( '' !== $digits ) {
                if ( !isset( $codes[$digits] ) ) {
                    $codes[$digits] = array();
                }
                $codes[$digits][] = $iso;
            }
        }
    }
    uksort( $codes, static function ( $a, $b ) {
        return strlen( $b ) - strlen( $a );
    } );
    foreach ( $codes as $digits => $isos ) {
        if ( substr( $phone, 0, strlen( (string) $digits ) ) === (string) $digits ) {
            $label = '+' . $digits;
            $isos = array_values( array_unique( $isos ) );
            if ( '1' === (string) $digits ) {
                $isos = array('US', 'CA');
            } elseif ( count( $isos ) > 2 ) {
                $isos = array_slice( $isos, 0, 2 );
            }
            if ( !empty( $isos ) ) {
                $label .= ' (' . implode( '/', $isos ) . ')';
            }
            return $label;
        }
    }
    return '';
}

/**
 * Count recent orders from the same IP, including the current order.
 *
 * @param WC_Order $order Order object.
 * @param mixed    $span  Hours or days.
 * @param string   $unit  `hours` or `days`.
 * @return int
 */
function wcbfc_count_recent_ip_orders(  $order, $span, $unit = 'hours'  ) {
    if ( !is_a( $order, 'WC_Order' ) ) {
        return 0;
    }
    $ip = wcblu_safe_trim( $order->get_customer_ip_address() );
    $created = $order->get_date_created();
    if ( '' === $ip || !$created ) {
        return 0;
    }
    $after = clone $created;
    $after->modify( '-' . absint( $span ) . (( 'days' === $unit ? ' days' : ' hours' )) );
    $orders = wc_get_orders( array(
        'limit'               => 50,
        'return'              => 'ids',
        'customer_ip_address' => $ip,
        'type'                => wc_get_order_types( 'order-count' ),
        'date_after'          => $after->date( 'Y-m-d H:i:s' ),
        'date_before'         => $created->date( 'Y-m-d H:i:s' ),
    ) );
    return ( is_array( $orders ) ? count( $orders ) : 0 );
}

/**
 * Structured mismatch details for one matched rule.
 *
 * @param WC_Order $order    Order object.
 * @param string   $rule_key Rule key.
 * @return array
 */
function wcbfc_get_fraud_score_rule_details(  $order, $rule_key  ) {
    if ( !is_a( $order, 'WC_Order' ) ) {
        return array();
    }
    $details = array();
    $billing_country = wcbfc_get_country_label( $order->get_billing_country() );
    $shipping_country = wcbfc_get_country_label( $order->get_shipping_country() );
    $store_country = ( function_exists( 'WC' ) && WC()->countries ? wcbfc_get_country_label( WC()->countries->get_base_country() ) : '' );
    $email = wcblu_safe_trim( $order->get_billing_email() );
    $ip = wcblu_safe_trim( $order->get_customer_ip_address() );
    $rules = json_decode( get_option( 'wcblu_rules_option' ), true );
    $rules = ( is_array( $rules ) ? $rules : array() );
    switch ( $rule_key ) {
        case 'international_order':
            $details[__( 'Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $billing_country;
            $details[__( 'Store usual', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $store_country;
            break;
        case 'billing_shipping_address':
            $details[__( 'Billing Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $billing_country;
            $details[__( 'Shipping Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $shipping_country;
            break;
        case 'Billing_Phone_Matches_Billing_Country':
            $detected = wcbfc_get_detected_phone_dial_code( $order->get_billing_phone() );
            $details[__( 'Detected Code', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $detected;
            $details[__( 'Billing Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $billing_country;
            break;
        case 'ip_multiple_order_Details':
        case 'velocities':
            $is_velocity = 'velocities' === $rule_key;
            $span = ( $is_velocity ? ( isset( $rules['wcbfc_too_many_oats_attempt_span'] ) ? $rules['wcbfc_too_many_oats_attempt_span'] : '24' ) : (( isset( $rules['wcbfc_ip_multiple_time_span'] ) ? $rules['wcbfc_ip_multiple_time_span'] : '30' )) );
            $unit = ( $is_velocity ? 'hours' : 'days' );
            $count = wcbfc_count_recent_ip_orders( $order, $span, $unit );
            $details[__( 'IP Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $ip;
            $details[__( 'Recent Orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = ( $count > 0 ? sprintf(
                /* translators: 1: order count, 2: time span, 3: hours or days */
                __( '%1$s in %2$s %3$s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                $count,
                $span,
                ( $is_velocity ? __( 'hours', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : __( 'days', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) )
            ) : sprintf( 
                /* translators: %s: time span */
                __( 'Multiple in %s', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
                $span . ' ' . (( $is_velocity ? __( 'hours', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) : __( 'days', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) ))
             ) );
            break;
        case 'first_order':
        case 'first_order_processing':
            $details[__( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $email;
            $details[__( 'Completed orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = '0';
            break;
        case 'behind_the_proxy':
            $details[__( 'IP Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $ip;
            $details[__( 'Proxy', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = __( 'Yes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            break;
        case 'temporary_email':
        case 'high_risk_email':
        case 'blocked_user_email':
            $details[__( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $email;
            if ( false !== strpos( $email, '@' ) ) {
                $details[__( 'Domain', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = substr( strrchr( $email, '@' ), 1 );
            }
            break;
        case 'unsafe_countries':
            $details[__( 'Billing Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $billing_country;
            if ( '' !== $shipping_country ) {
                $details[__( 'Shipping Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $shipping_country;
            }
            break;
        case 'geo_location_not_matched_address':
            $details[__( 'Order Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $billing_country;
            $details[__( 'Geo Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = wcblu_safe_trim( get_option( 'wcblu_geo_loc_cntry' ) );
            break;
        case 'high_value':
        case 'high_amount':
            $details[__( 'Order Total', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = html_entity_decode( wp_strip_all_tags( $order->get_formatted_order_total() ), ENT_QUOTES );
            if ( 'high_amount' === $rule_key && !empty( $rules['wcbfc_max_order_attempt_span'] ) ) {
                $details[__( 'Max Allowed', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = $rules['wcbfc_max_order_attempt_span'];
            }
            break;
        case 'cc_velocities':
            $details[__( 'Declines allowed', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = ( isset( $rules['wcbfc_too_many_failed_oats_attempt_try'] ) ? $rules['wcbfc_too_many_failed_oats_attempt_try'] : '' );
            break;
        case 'maximum_allowed_no_order':
            $details[__( 'Max orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = ( isset( $rules['wcbfc_maximum_allowed_number_of_orders_between_time'] ) ? $rules['wcbfc_maximum_allowed_number_of_orders_between_time'] : '' );
            break;
        case 'unknown_origin':
            $details[__( 'Origin', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )] = __( 'Unknown', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            break;
    }
    return array_filter( $details, static function ( $value ) {
        return null !== $value && '' !== $value;
    } );
}

/**
 * Structured details for all matched score rules.
 *
 * @param WC_Order $order Order object.
 * @param array    $score Matched score keys and weights.
 * @return array
 */
function wcbfc_build_fraud_score_details(  $order, $score  ) {
    $details = array();
    if ( !is_array( $score ) ) {
        return $details;
    }
    foreach ( array_keys( $score ) as $rule_key ) {
        $details[$rule_key] = wcbfc_get_fraud_score_rule_details( $order, $rule_key );
    }
    return $details;
}

/**
 * One Click Setup presets for the Rules page.
 *
 * @return array
 */
function wcbfc_get_one_click_presets() {
    $off_rules = array(
        'wcbfc_first_order_status'                    => '0',
        'wcbfc_first_order_weight'                    => '0',
        'wcbfc_first_order_custom'                    => '0',
        'wcbfc_first_order_custom_weight'             => '0',
        'wcbfc_bca_order'                             => '0',
        'wcbfc_bca_order_weight'                      => '0',
        'wcbfc_billing_phone_number_order'            => '0',
        'wcbfc_billing_phone_number_order_weight'     => '0',
        'wcbfc_billing_shipping_geo_match'            => '0',
        'wcbfc_billing_shipping_geo_match_weight'     => '0',
        'wcbfc_proxy_order'                           => '0',
        'wcbfc_proxy_order_weight'                    => '0',
        'wcbfc_ip_multiple_check'                     => '0',
        'wcbfc_ip_multiple_weight'                    => '0',
        'wcbfc_international_order'                   => '0',
        'wcbfc_international_order_weight'            => '0',
        'wcbfc_unknown_origin'                        => '0',
        'wcbfc_unknown_origin_weight'                 => '0',
        'wcbfc_unsafe_countries'                      => '0',
        'wcbfc_unsafe_countries_weight'               => '0',
        'wcbfc_suspecius_email'                       => '0',
        'wcbfc_suspecious_email_weight'               => '0',
        'wcbfc_blocked_user_email_check'              => '0',
        'wcbfc_blocked_user_email_weight'             => '0',
        'wcbfc_order_avg_amount_check'                => '0',
        'wcbfc_order_avg_amount_weight'               => '0',
        'wcbfc_order_amount_check'                    => '0',
        'wcbfc_max_order_attempt_weight'              => '0',
        'wcbfc_too_many_oa_check'                     => '0',
        'wcbfc_too_many_oa_attempt_weight'            => '0',
        'wcbfc_too_many_o_failed_a_check'             => '0',
        'wcbfc_too_many_o_failed_a_check_weight'      => '0',
        'wcbfc_no_of_allow_order_between_time'        => '0',
        'wcbfc_no_of_allow_order_between_time_weight' => '0',
    );
    $presets = array(
        'relaxed'  => array(
            'title'            => __( 'Relaxed', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description'      => __( 'Blocks obvious spam and bots only.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'             => 'dashicons-yes-alt',
            'theme'            => 'green',
            'recommended'      => false,
            'protection'       => 35,
            'protection_label' => __( 'Light Protection', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'bullets'          => array(array(
                'yes'  => true,
                'text' => __( 'Lower false positives', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Basic rule checking', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Good for new stores', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            )),
            'extras'           => array(
                'wcblu_automatic_blacklist'          => '0',
                'wcblu_automatic_blacklist_ip'       => '0',
                'wcbfc_fraud_check_before_pay'       => '0',
                'wcbfc_update_order_status_on_score' => '0',
                'wcblu_place_order_type'             => '1',
                'wcbfc_settings_low_risk_threshold'  => '40',
                'wcbfc_settings_high_risk_threshold' => '85',
            ),
            'rules'            => array_merge( $off_rules, array(
                'wcbfc_first_order_status'         => '1',
                'wcbfc_first_order_weight'         => '5',
                'wcbfc_suspecius_email'            => '1',
                'wcbfc_suspecious_email_weight'    => '10',
                'wcbfc_blocked_user_email_check'   => '1',
                'wcbfc_blocked_user_email_weight'  => '15',
                'wcbfc_proxy_order'                => '1',
                'wcbfc_proxy_order_weight'         => '15',
                'wcbfc_ip_multiple_check'          => '1',
                'wcbfc_ip_multiple_weight'         => '10',
                'wcbfc_too_many_oa_check'          => '1',
                'wcbfc_too_many_oa_attempt_weight' => '10',
            ) ),
        ),
        'balanced' => array(
            'title'            => __( 'Balanced', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description'      => __( 'Recommended for most stores.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'             => 'dashicons-leftright',
            'theme'            => 'blue',
            'recommended'      => true,
            'protection'       => 75,
            'protection_label' => __( 'Balanced Protection', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'bullets'          => array(array(
                'yes'  => true,
                'text' => __( 'Balanced protection', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Medium rule weights', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Good accuracy', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            )),
            'extras'           => array(
                'wcblu_automatic_blacklist'          => '1',
                'wcblu_automatic_blacklist_ip'       => '0',
                'wcbfc_fraud_check_before_pay'       => '1',
                'wcbfc_update_order_status_on_score' => '1',
                'wcblu_place_order_type'             => '1',
                'wcbfc_settings_low_risk_threshold'  => '25',
                'wcbfc_settings_high_risk_threshold' => '75',
            ),
            'rules'            => array_merge( $off_rules, array(
                'wcbfc_first_order_status'                => '1',
                'wcbfc_first_order_weight'                => '5',
                'wcbfc_first_order_custom'                => '1',
                'wcbfc_first_order_custom_weight'         => '15',
                'wcbfc_bca_order'                         => '1',
                'wcbfc_bca_order_weight'                  => '15',
                'wcbfc_billing_phone_number_order'        => '1',
                'wcbfc_billing_phone_number_order_weight' => '10',
                'wcbfc_international_order'               => '1',
                'wcbfc_international_order_weight'        => '15',
                'wcbfc_suspecius_email'                   => '1',
                'wcbfc_suspecious_email_weight'           => '15',
                'wcbfc_blocked_user_email_check'          => '1',
                'wcbfc_blocked_user_email_weight'         => '20',
                'wcbfc_unsafe_countries'                  => '1',
                'wcbfc_unsafe_countries_weight'           => '20',
                'wcbfc_proxy_order'                       => '1',
                'wcbfc_proxy_order_weight'                => '20',
                'wcbfc_ip_multiple_check'                 => '1',
                'wcbfc_ip_multiple_weight'                => '15',
                'wcbfc_order_amount_check'                => '1',
                'wcbfc_max_order_attempt_weight'          => '15',
                'wcbfc_too_many_oa_check'                 => '1',
                'wcbfc_too_many_oa_attempt_weight'        => '15',
            ) ),
        ),
        'strict'   => array(
            'title'            => __( 'Strict', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description'      => __( 'Strong fraud prevention.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'             => 'dashicons-shield',
            'theme'            => 'orange',
            'recommended'      => false,
            'protection'       => 85,
            'protection_label' => __( 'Strong Protection', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'bullets'          => array(array(
                'yes'  => true,
                'text' => __( 'Higher rule weights', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Strict rule matching', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Blocks more fraud', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            )),
            'extras'           => array(
                'wcblu_automatic_blacklist'          => '1',
                'wcblu_automatic_blacklist_ip'       => '1',
                'wcbfc_fraud_check_before_pay'       => '1',
                'wcbfc_update_order_status_on_score' => '1',
                'wcblu_place_order_type'             => '1',
                'wcbfc_settings_low_risk_threshold'  => '20',
                'wcbfc_settings_high_risk_threshold' => '60',
            ),
            'rules'            => array_merge( $off_rules, array(
                'wcbfc_first_order_status'                => '1',
                'wcbfc_first_order_weight'                => '10',
                'wcbfc_first_order_custom'                => '1',
                'wcbfc_first_order_custom_weight'         => '20',
                'wcbfc_bca_order'                         => '1',
                'wcbfc_bca_order_weight'                  => '20',
                'wcbfc_billing_phone_number_order'        => '1',
                'wcbfc_billing_phone_number_order_weight' => '15',
                'wcbfc_international_order'               => '1',
                'wcbfc_international_order_weight'        => '20',
                'wcbfc_unknown_origin'                    => '1',
                'wcbfc_unknown_origin_weight'             => '20',
                'wcbfc_suspecius_email'                   => '1',
                'wcbfc_suspecious_email_weight'           => '20',
                'wcbfc_blocked_user_email_check'          => '1',
                'wcbfc_blocked_user_email_weight'         => '30',
                'wcbfc_unsafe_countries'                  => '1',
                'wcbfc_unsafe_countries_weight'           => '25',
                'wcbfc_proxy_order'                       => '1',
                'wcbfc_proxy_order_weight'                => '30',
                'wcbfc_ip_multiple_check'                 => '1',
                'wcbfc_ip_multiple_weight'                => '20',
                'wcbfc_order_avg_amount_check'            => '1',
                'wcbfc_order_avg_amount_weight'           => '20',
                'wcbfc_order_amount_check'                => '1',
                'wcbfc_max_order_attempt_weight'          => '20',
                'wcbfc_too_many_oa_check'                 => '1',
                'wcbfc_too_many_oa_attempt_weight'        => '20',
                'wcbfc_too_many_o_failed_a_check'         => '1',
                'wcbfc_too_many_o_failed_a_check_weight'  => '25',
            ) ),
        ),
        'maximum'  => array(
            'title'            => __( 'Maximum', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'description'      => __( 'Highest protection with aggressive blocking.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'icon'             => 'dashicons-shield-alt',
            'theme'            => 'red',
            'recommended'      => false,
            'protection'       => 95,
            'protection_label' => __( 'Maximum Protection', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            'bullets'          => array(array(
                'yes'  => true,
                'text' => __( 'Maximum rule weights', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Very strict matching', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            ), array(
                'yes'  => true,
                'text' => __( 'Highest fraud blocking', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            )),
            'extras'           => array(
                'wcblu_automatic_blacklist'          => '1',
                'wcblu_automatic_blacklist_ip'       => '1',
                'wcbfc_fraud_check_before_pay'       => '1',
                'wcbfc_update_order_status_on_score' => '1',
                'wcblu_place_order_type'             => '1',
                'wcbfc_settings_low_risk_threshold'  => '20',
                'wcbfc_settings_high_risk_threshold' => '55',
            ),
            'rules'            => array_merge( $off_rules, array(
                'wcbfc_first_order_status'                    => '1',
                'wcbfc_first_order_weight'                    => '15',
                'wcbfc_first_order_custom'                    => '1',
                'wcbfc_first_order_custom_weight'             => '25',
                'wcbfc_bca_order'                             => '1',
                'wcbfc_bca_order_weight'                      => '25',
                'wcbfc_billing_phone_number_order'            => '1',
                'wcbfc_billing_phone_number_order_weight'     => '20',
                'wcbfc_billing_shipping_geo_match'            => '1',
                'wcbfc_billing_shipping_geo_match_weight'     => '25',
                'wcbfc_international_order'                   => '1',
                'wcbfc_international_order_weight'            => '25',
                'wcbfc_unknown_origin'                        => '1',
                'wcbfc_unknown_origin_weight'                 => '25',
                'wcbfc_suspecius_email'                       => '1',
                'wcbfc_suspecious_email_weight'               => '25',
                'wcbfc_blocked_user_email_check'              => '1',
                'wcbfc_blocked_user_email_weight'             => '40',
                'wcbfc_unsafe_countries'                      => '1',
                'wcbfc_unsafe_countries_weight'               => '35',
                'wcbfc_proxy_order'                           => '1',
                'wcbfc_proxy_order_weight'                    => '40',
                'wcbfc_ip_multiple_check'                     => '1',
                'wcbfc_ip_multiple_weight'                    => '25',
                'wcbfc_order_avg_amount_check'                => '1',
                'wcbfc_order_avg_amount_weight'               => '25',
                'wcbfc_order_amount_check'                    => '1',
                'wcbfc_max_order_attempt_weight'              => '25',
                'wcbfc_too_many_oa_check'                     => '1',
                'wcbfc_too_many_oa_attempt_weight'            => '25',
                'wcbfc_too_many_o_failed_a_check'             => '1',
                'wcbfc_too_many_o_failed_a_check_weight'      => '35',
                'wcbfc_no_of_allow_order_between_time'        => '1',
                'wcbfc_no_of_allow_order_between_time_weight' => '30',
            ) ),
        ),
    );
    foreach ( $presets as $key => $preset ) {
        $presets[$key]['preview'] = wcbfc_get_one_click_preset_preview( $preset );
        $presets[$key]['apply_label'] = sprintf( 
            /* translators: %s: preset title */
            __( 'Apply %s Setup', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            $preset['title']
         );
    }
    return $presets;
}

/**
 * Preview numbers derived from a preset.
 *
 * @param array $preset Preset array.
 * @return array
 */
function wcbfc_get_one_click_preset_preview(  $preset  ) {
    $rules = ( isset( $preset['rules'] ) ? $preset['rules'] : array() );
    $extras = ( isset( $preset['extras'] ) ? $preset['extras'] : array() );
    $enabled = 0;
    $weights = array();
    $weight_map = array(
        'wcbfc_first_order_status'             => 'wcbfc_first_order_weight',
        'wcbfc_first_order_custom'             => 'wcbfc_first_order_custom_weight',
        'wcbfc_bca_order'                      => 'wcbfc_bca_order_weight',
        'wcbfc_billing_phone_number_order'     => 'wcbfc_billing_phone_number_order_weight',
        'wcbfc_billing_shipping_geo_match'     => 'wcbfc_billing_shipping_geo_match_weight',
        'wcbfc_proxy_order'                    => 'wcbfc_proxy_order_weight',
        'wcbfc_ip_multiple_check'              => 'wcbfc_ip_multiple_weight',
        'wcbfc_international_order'            => 'wcbfc_international_order_weight',
        'wcbfc_unknown_origin'                 => 'wcbfc_unknown_origin_weight',
        'wcbfc_unsafe_countries'               => 'wcbfc_unsafe_countries_weight',
        'wcbfc_suspecius_email'                => 'wcbfc_suspecious_email_weight',
        'wcbfc_blocked_user_email_check'       => 'wcbfc_blocked_user_email_weight',
        'wcbfc_order_avg_amount_check'         => 'wcbfc_order_avg_amount_weight',
        'wcbfc_order_amount_check'             => 'wcbfc_max_order_attempt_weight',
        'wcbfc_too_many_oa_check'              => 'wcbfc_too_many_oa_attempt_weight',
        'wcbfc_too_many_o_failed_a_check'      => 'wcbfc_too_many_o_failed_a_check_weight',
        'wcbfc_no_of_allow_order_between_time' => 'wcbfc_no_of_allow_order_between_time_weight',
    );
    foreach ( $weight_map as $status_key => $weight_key ) {
        if ( !empty( $rules[$status_key] ) && '1' === (string) $rules[$status_key] ) {
            ++$enabled;
            if ( isset( $rules[$weight_key] ) && (int) $rules[$weight_key] > 0 ) {
                $weights[] = (int) $rules[$weight_key];
            }
        }
    }
    $min = ( !empty( $weights ) ? min( $weights ) : 0 );
    $max = ( !empty( $weights ) ? max( $weights ) : 0 );
    return array(
        'enabled'          => $enabled,
        'weight_range'     => $min . ' – ' . $max,
        'auto_blacklist'   => !empty( $extras['wcblu_automatic_blacklist'] ) && '1' === (string) $extras['wcblu_automatic_blacklist'],
        'auto_ip'          => !empty( $extras['wcblu_automatic_blacklist_ip'] ) && '1' === (string) $extras['wcblu_automatic_blacklist_ip'],
        'before_pay'       => !empty( $extras['wcbfc_fraud_check_before_pay'] ) && '1' === (string) $extras['wcbfc_fraud_check_before_pay'],
        'update_status'    => !empty( $extras['wcbfc_update_order_status_on_score'] ) && '1' === (string) $extras['wcbfc_update_order_status_on_score'],
        'place_order'      => !empty( $extras['wcblu_place_order_type'] ) && '1' === (string) $extras['wcblu_place_order_type'],
        'medium_risk'      => ( isset( $extras['wcbfc_settings_low_risk_threshold'] ) ? (int) $extras['wcbfc_settings_low_risk_threshold'] : 25 ),
        'high_risk'        => ( isset( $extras['wcbfc_settings_high_risk_threshold'] ) ? (int) $extras['wcbfc_settings_high_risk_threshold'] : 75 ),
        'protection'       => ( isset( $preset['protection'] ) ? (int) $preset['protection'] : 0 ),
        'protection_label' => ( isset( $preset['protection_label'] ) ? $preset['protection_label'] : '' ),
    );
}

/**
 * Merge a One Click Setup preset into saved options.
 *
 * @param string $preset_key Preset key.
 * @return true|WP_Error
 */
function wcbfc_apply_one_click_preset(  $preset_key  ) {
    $presets = wcbfc_get_one_click_presets();
    if ( !isset( $presets[$preset_key] ) ) {
        return new WP_Error('invalid_preset', __( 'Invalid setup option.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ));
    }
    $preset = $presets[$preset_key];
    $rules = json_decode( get_option( 'wcblu_rules_option', '' ), true );
    $rules = ( is_array( $rules ) ? $rules : array() );
    foreach ( $preset['rules'] as $key => $value ) {
        $rules[$key] = $value;
    }
    update_option( 'wcblu_rules_option', wp_json_encode( $rules ) );
    $blacklist = json_decode( get_option( 'wcblu_option', '' ), true );
    $blacklist = ( is_array( $blacklist ) ? $blacklist : array() );
    $blacklist['wcblu_automatic_blacklist'] = $preset['extras']['wcblu_automatic_blacklist'];
    $blacklist['wcblu_automatic_blacklist_ip'] = $preset['extras']['wcblu_automatic_blacklist_ip'];
    $blacklist['wcblu_place_order_type'] = '1';
    update_option( 'wcblu_option', wp_json_encode( $blacklist ) );
    $general = json_decode( get_option( 'wcblu_general_option', '' ), true );
    $general = ( is_array( $general ) ? $general : array() );
    $general['wcbfc_fraud_check_before_pay'] = $preset['extras']['wcbfc_fraud_check_before_pay'];
    $general['wcbfc_update_order_status_on_score'] = $preset['extras']['wcbfc_update_order_status_on_score'];
    $general['wcbfc_settings_low_risk_threshold'] = ( isset( $preset['extras']['wcbfc_settings_low_risk_threshold'] ) ? (string) (int) $preset['extras']['wcbfc_settings_low_risk_threshold'] : '25' );
    $general['wcbfc_settings_high_risk_threshold'] = ( isset( $preset['extras']['wcbfc_settings_high_risk_threshold'] ) ? (string) (int) $preset['extras']['wcbfc_settings_high_risk_threshold'] : '75' );
    $general['wcbfc_one_click_preset'] = $preset_key;
    update_option( 'wcblu_general_option', wp_json_encode( $general ) );
    return true;
}

/**
 * Whether the One Click Setup wizard should show on the Rules page.
 *
 * @return bool
 */
function wcbfc_should_show_one_click_setup() {
    $general = json_decode( get_option( 'wcblu_general_option', '' ), true );
    $general = ( is_array( $general ) ? $general : array() );
    return isset( $general['wcbfc_fraud_check_status'] ) && 'on' === $general['wcbfc_fraud_check_status'];
}

/**
 * @param $getpluginoptionarray
 *
 * @return false|string
 * Function to return HTML of setting page for free users
 */
function wcblu_get_setting_html_for_free_user(  $getpluginoptionarray  ) {
    ob_start();
    $getregistertype = ( !empty( $getpluginoptionarray['wcblu_register_type'] ) ? $getpluginoptionarray['wcblu_register_type'] : '0' );
    $getplaceordertype = ( !empty( $getpluginoptionarray['wcblu_place_order_type'] ) ? $getpluginoptionarray['wcblu_place_order_type'] : '0' );
    $fetchSelectedIpAddress = ( !empty( $getpluginoptionarray['wcblu_block_ip'] ) ? $getpluginoptionarray['wcblu_block_ip'] : '' );
    $fetchSelecetedState = ( !empty( $getpluginoptionarray['wcblu_block_state'] ) ? $getpluginoptionarray['wcblu_block_state'] : '' );
    $fetchSelecetedDomain = ( !empty( $getpluginoptionarray['wcblu_block_domain'] ) ? $getpluginoptionarray['wcblu_block_domain'] : '' );
    $fetchSelecetedZip = ( !empty( $getpluginoptionarray['wcblu_block_zip'] ) ? $getpluginoptionarray['wcblu_block_zip'] : '' );
    $fetchSelectedEmail = ( !empty( $getpluginoptionarray['wcblu_block_email'] ) ? $getpluginoptionarray['wcblu_block_email'] : '' );
    $wbaf_address = ( !empty( $getpluginoptionarray['wcblu_block_address'] ) ? $getpluginoptionarray['wcblu_block_address'] : '' );
    $fetchSelectedPhone = ( !empty( $getpluginoptionarray['wcblu_block_phone'] ) ? $getpluginoptionarray['wcblu_block_phone'] : '' );
    $fetchAllSectionChk = ( !empty( $getpluginoptionarray['wcblu_all_chk_selection'] ) ? $getpluginoptionarray['wcblu_all_chk_selection'] : '' );
    /**
     * get messages
     */
    $getemailmessage = ( !empty( $getpluginoptionarray['wcblu_email_msg'] ) ? $getpluginoptionarray['wcblu_email_msg'] : esc_html__( 'This email address has been blocked, please try other email address or Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getstatemessage = ( !empty( $getpluginoptionarray['wcblu_state_msg'] ) ? $getpluginoptionarray['wcblu_state_msg'] : esc_html__( 'Sorry :( We are not shipping this products in this state.  Kindly contact admin. ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getzipmessage = ( !empty( $getpluginoptionarray['wcblu_zpcode_msg'] ) ? $getpluginoptionarray['wcblu_zpcode_msg'] : esc_html__( 'Sorry :( We are not shipping this products in this location. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getipmessage = ( !empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? $getpluginoptionarray['wcblu_ip_msg'] : esc_html__( 'This IP address has been blocked due to some reason, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getdomainmessage = ( !empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? $getpluginoptionarray['wcblu_domain_msg'] : esc_html__( 'Sorry :( This domain has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getaddressmessage = ( !empty( $getpluginoptionarray['wcblu_address_msg'] ) ? $getpluginoptionarray['wcblu_address_msg'] : esc_html__( 'Sorry :( This Address has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getphonemessage = ( !empty( $getpluginoptionarray['wcblu_phone_msg'] ) ? $getpluginoptionarray['wcblu_phone_msg'] : esc_html__( 'Sorry :( This phone number has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getcountrymessage = ( !empty( $getpluginoptionarray['wcblu_country_msg'] ) ? $getpluginoptionarray['wcblu_country_msg'] : esc_html__( 'Sorry :( We are not shipping this products in this country.  Kindly contact admin. ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getzonemessage = ( !empty( $getpluginoptionarray['wcblu_zone_msg'] ) ? $getpluginoptionarray['wcblu_zone_msg'] : esc_html__( 'This zone has been blocked due to some reason, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    ?>

	<div class='heading_div'>
		<div class='heading_section'>
			<h2><?php 
    esc_html_e( 'Basic configuration settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></h2>
		</div>
		<button type="submit" name="wcblu_submit" class="button button-primary wcblu_submit" value="<?php 
    echo esc_attr( 'Save Changes' );
    ?>"><?php 
    esc_html_e( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
	</div>
	<table class="table-outer res-cl">
		<tbody>
		<tr>
			<th scope="row" class="titledesc">
				<label for=""><?php 
    esc_html_e( 'Blocking Trigger Stage', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
					<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<div><?php 
    echo esc_html__( 'For Registration : Registration Time defines you want to block user while user going to register on your website.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
							<div><?php 
    echo esc_html__( 'For Place order : Place Order defines you want to block user while user placing order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
						</div>
					</div>
				</label>
			</th>
			<td>
				<p><?php 
    echo wp_kses_post( esc_html__( 'Type defines on which stage you want to block user.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    ?></p>
				<?php 
    if ( empty( $getregistertype ) && '' === $getregistertype ) {
        ?>
					<input checked type="checkbox" id="wc_user_register_type"
					       name="wc_user_register_type" value="">
					<label for="wc_user_register_type"><?php 
        esc_html_e( 'Registration', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					<?php 
    } else {
        ?>
					<input <?php 
        if ( !empty( $getregistertype ) && '1' === $getregistertype ) {
            ?> checked <?php 
        }
        ?>
						type="checkbox" id="wc_user_register_type"
						name="wc_user_register_type" value="<?php 
        if ( !empty( $getregistertype ) && '1' === $getregistertype ) {
            echo "1";
        } else {
            echo "0";
        }
        ?>">
					<label for="wc_user_register_type"><?php 
        esc_html_e( 'Registration', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
				<?php 
    }
    ?>
				<p><?php 
    echo sprintf( wp_kses_post( '%1$sNote: For Registration only email id and ip address blocked.%2$s' ), '<strong>', '</strong>' );
    ?></p>
				<?php 
    if ( empty( $getplaceordertype ) && '' === $getplaceordertype ) {
        ?>
					<input checked type="checkbox" id="wc_user_place_order_type"
					       name="wc_user_place_order_type" value="">
					<label for="wc_user_place_order_type"><?php 
        esc_html_e( 'Place order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					
					<?php 
    } else {
        ?>
					<input <?php 
        if ( !empty( $getplaceordertype ) && '1' === $getplaceordertype ) {
            ?> checked <?php 
        }
        ?>
						type="checkbox"
						id="wc_user_place_order_type"
						name="wc_user_place_order_type"
						value="<?php 
        if ( !empty( $getplaceordertype ) && '1' === $getplaceordertype ) {
            echo "1";
        } else {
            echo "0";
        }
        ?>">
					<label for="wc_user_place_order_type"><?php 
        esc_html_e( 'Place order', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
				<?php 
    }
    ?>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc">
					<label for=""><?php 
        esc_html_e( 'Address Type to Monitor', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
        echo esc_html__( 'Choose which address to block: Billing, Shipping, or Both.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
						</div>
					</div>
					<div class="wcblu-pro-label"></div>
				</label>
				</th>
				<td>
					<p>
						<?php 
        echo wp_kses_post( esc_html__( 'Address type defines on which type of address you want to block user.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
        ?>
					</p>				
					<p>
						<input type="radio" id="wc_user_both_type" name="wc_user_address_type" value="<?php 
        esc_attr( 'purchase-premium' );
        ?>" disabled />
						<label for="wc_user_both_type"><?php 
        esc_html_e( 'Both address type', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					</p>
					<p>
						<input type="radio" id="wc_user_billing_type" name="wc_user_address_type" value="<?php 
        esc_attr( 'purchase-premium' );
        ?>" checked disabled />
						<label for="wc_user_billing_type"><?php 
        esc_html_e( 'Billing address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					</p>
					<p>
						<input type="radio" id="wc_user_shipping_type" name="wc_user_address_type" value="<?php 
        esc_attr( 'purchase-premium' );
        ?>" disabled />
						<label for="wc_user_shipping_type"><?php 
        esc_html_e( 'Shipping address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					</p>
					<p class="primium_message"><?php 
        echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
        ?></p>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<th scope="row" class="titledesc"><label
					for="email"><?php 
    esc_html_e( 'Blocked Email Addresses', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
					<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple email to block user <a target="_blank" href="https://docs.thedotstore.com/article/518-how-to-automatic-blacklist-emails-based-on-fraud-score">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
				</label>
			</th>
			<td>
				<div class="">
					<p class="wcblu-switch-row">
						<label class="wcblu-switch">
							<input type="checkbox" id="wcblu_enable_block_email" name="wcblu_enable_block_email" value="1" checked disabled>
							<span class="wcblu-switch-slider"></span>
						</label>
						<label for="wcblu_enable_block_email"><?php 
    esc_html_e( 'Block orders from email addresses on the list below.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
						<span class="new-feature"><?php 
    echo esc_html_e( '- [New]', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
						<span class="wcblu-pro-label"></span>
					</p>
					<p class="wcblu-switch-row">
						<label class="wcblu-switch">
							<input type="checkbox" id="wcblu_automatic_blacklist" name="wcblu_automatic_blacklist" value="" disabled>
							<span class="wcblu-switch-slider"></span>
						</label>
						<label for="wcblu_automatic_blacklist"><?php 
    esc_html_e( 'Automatic Blacklisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
						<span class="new-feature"><?php 
    echo esc_html_e( '- [New]', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
						<span class="wcblu-pro-label"></span>
					</p>
					<div>
						<p><?php 
    esc_html_e( 'Add email addresses of orders reported with a high risk of fraud to blacklist automatically', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
					</div>
					<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access Automatic Blacklisting feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
				</div>
				<select id="email"
				        data-placeholder="<?php 
    esc_attr_e( 'Add emails separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
				        name="email[]" multiple="true"
				        class="chosen-select-email category-select chosen-rtl">
					<?php 
    $getpluginoption = get_option( 'wcblu_option' );
    $getpluginoptionarray = json_decode( $getpluginoption, true );
    $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_email'] ) ? $getpluginoptionarray['wcblu_block_email'] : '' );
    $optionsBlockEmail = ( !empty( $fetchCurrentBrowser ) ? $fetchCurrentBrowser : array() );
    if ( !empty( $fetchSelectedEmail ) ) {
        if ( is_array( $fetchSelectedEmail ) ) {
            foreach ( $fetchSelectedEmail as $email ) {
                ?>
								<option
								<?php 
                if ( in_array( $email, $optionsBlockEmail, true ) ) {
                    echo 'Selected';
                }
                ?>
								value="<?php 
                echo esc_attr( $email );
                ?>"><?php 
                esc_html_e( $email, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></option>
								<?php 
            }
        }
    }
    ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="first_name"><?php 
    esc_html_e( 'Blocked First Names', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple first name to block users <a target="_blank" href="https://docs.thedotstore.com/article/188-how-to-block-user-by-first-and-last-name">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
					<div class="wcblu-pro-label"></div>
				</label>
			</th>
			<td>
				<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="last_name"><?php 
    esc_html_e( 'Blocked Last Names', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple last name to block users <a target="_blank" href="https://docs.thedotstore.com/article/188-how-to-block-user-by-first-and-last-name">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
					<div class="wcblu-pro-label"></div>
				</label>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
				<tr>
				
					<th scope="row" class="titledesc"><label
							for="wcblu_address"><?php 
        esc_html_e( 'Blocked Street Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
						<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
        echo wp_kses( __( 'Add multiple address to block users <a target="_blank" href="https://docs.thedotstore.com/article/894-how-to-block-user-by-address">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
            'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
        ) );
        ?>
							</div>
						</div>
						</label>
					</th>
					<td>
						<select id="wcblu_address"
								data-placeholder="<?php 
        esc_attr_e( 'Add Address to block users.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>"
								name="wcblu_address[]" multiple="true"
								class="chosen-select-last-name category-select chosen-rtl"
								style="width: 62%">
							<option value=""></option>
							<?php 
        if ( !empty( $wbaf_address ) && '' !== $wbaf_address ) {
            if ( is_array( $wbaf_address ) ) {
                foreach ( $wbaf_address as $values ) {
                    ?>
										<option selected value="<?php 
                    echo esc_attr( $values );
                    ?>"><?php 
                    esc_html_e( $values, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                    ?></option>
										<?php 
                }
            }
        }
        ?>
						</select>
						<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
							<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<th scope="row" class="titledesc"><label
					for="ip_address"><?php 
    esc_html_e( 'Blocked IP Addresses', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple IP address to block users <a target="_blank" href="https://docs.thedotstore.com/article/152-block-fraud-customers-by-ip-geo-location">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
				</label>
			</th>
			<td>
				<div>
					<p class="wcblu-switch-row">
						<label class="wcblu-switch">
							<input type="checkbox" id="wcblu_enable_block_ip" name="wcblu_enable_block_ip" value="1" checked disabled>
							<span class="wcblu-switch-slider"></span>
						</label>
						<label for="wcblu_enable_block_ip"><?php 
    esc_html_e( 'Block orders from IP addresses on the list below.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
						<span class="new-feature"><?php 
    echo esc_html_e( '- [New]', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
						<span class="wcblu-pro-label"></span>
					</p>
					<p class="wcblu-switch-row">
						<label class="wcblu-switch">
							<input type="checkbox" id="wcblu_automatic_blacklist_ip" name="wcblu_automatic_blacklist_ip" value="" disabled>
							<span class="wcblu-switch-slider"></span>
						</label>
						<label for="wcblu_automatic_blacklist_ip"><?php 
    esc_html_e( 'Automatically block IP addresses identified as high risk.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
						<span class="new-feature"><?php 
    echo esc_html_e( '- [New]', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></span>
						<span class="wcblu-pro-label"></span>
					</p>
				</div>
				<select id="ip_address"
				        data-placeholder="<?php 
    esc_attr_e( 'Add IP address separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
				        name="ip-basic[]" multiple="true"
				        class="chosen-select-ip category-select chosen-rtl">
					<option value=""></option>
					<?php 
    if ( !empty( $fetchSelectedIpAddress ) && '' !== $fetchSelectedIpAddress ) {
        if ( is_array( $fetchSelectedIpAddress ) ) {
            foreach ( $fetchSelectedIpAddress as $values ) {
                ?>
								<option selected value="<?php 
                echo esc_attr( $values );
                ?>"><?php 
                esc_html_e( $values, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></option>
								<?php 
            }
        }
    }
    ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="domain"><?php 
    esc_html_e( 'Blocked Domains', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
    echo wp_kses( __( 'Add multiple domain to block users e.g gmail.com <a target="_blank" href="https://docs.thedotstore.com/article/153-block-fraud-customers-by-domain">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
					</div>
				</div>
				</label>
			</th>
			<td>
				<select id="domain"
				        data-placeholder="<?php 
    esc_attr_e( 'Add domain to block users separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
				        name="domain[]" multiple="true"
				        class="chosen-select-domain category-select chosen-rtl">
					<option value=""></option>
					<?php 
    if ( !empty( $fetchSelecetedDomain ) && '' !== $fetchSelecetedDomain ) {
        if ( is_array( $fetchSelecetedDomain ) ) {
            foreach ( $fetchSelecetedDomain as $values ) {
                ?>
								<option selected value="<?php 
                echo esc_attr( $values );
                ?>"><?php 
                esc_html_e( $values, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></option>
								<?php 
            }
        }
    }
    ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="last_name"><?php 
    esc_html_e( 'Blocked Domain Extensions', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple domain extension to block users e.g .com <a target="_blank" href="https://docs.thedotstore.com/article/1073-block-fraud-customers-by-domain-extension">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
					<div class="wcblu-pro-label"></div>
				</label>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="last_name"><?php 
    esc_html_e( 'Blocked Web Browsers', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcblu-pro-label"></div>
					<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple browsers to block users <a target="_blank" href="https://docs.thedotstore.com/article/232-how-to-block-user-by-the-browser">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
				</label>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc"><label
						for="state"><?php 
        esc_html_e( 'Blocked States', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
        echo wp_kses( __( 'Add multiple states to block users <a target="_blank" href="https://docs.thedotstore.com/article/154-block-fraud-customers-by-state">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
            'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
        ) );
        ?>
							</div>
						</div>
					</label>
				</th>
				<td>
					<select id="state"
							data-placeholder="<?php 
        esc_attr_e( 'Add states to block user separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>"
							name="state[]" multiple="true"
							class="chosen-select-state category-select chosen-rtl">
						<option value=""></option>
						
						<?php 
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_state'] ) ? $getpluginoptionarray['wcblu_block_state'] : '' );
        $optionsBlockState = ( !empty( $fetchCurrentBrowser ) ? $fetchCurrentBrowser : array() );
        if ( !empty( $fetchSelecetedState ) ) {
            if ( is_array( $fetchSelecetedState ) ) {
                foreach ( $fetchSelecetedState as $state ) {
                    ?>
									<option 
									<?php 
                    if ( in_array( $state, $optionsBlockState, true ) ) {
                        echo 'selected';
                    }
                    ?>
									value="<?php 
                    echo esc_attr( $state );
                    ?>"><?php 
                    esc_html_e( $state, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                    ?></option>
									<?php 
                }
            }
        }
        ?>

					</select>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="country"><?php 
        esc_html_e( 'Blocked Countries', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
        echo wp_kses( __( 'Add multiple countries to block users <a target="_blank" href="https://docs.thedotstore.com/article/673-block-fraud-customers-by-country">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
            'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
        ) );
        ?>
							</div>
						</div>
					</label>
				</th>
				<td>
					<?php 
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
            $countries_obj = new WC_Countries();
            $final_country_merged_array_result = $countries_obj->__get( 'countries' );
        } else {
            $final_country_merged_array_result = array();
        }
        ?>
					<select id="country"
							data-placeholder="<?php 
        esc_attr_e( 'Add countries to block user separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>"
							name="country[]" multiple="true"
							class="chosen-select-country category-select chosen-rtl" autocomplete="new-password">
						
						<?php 
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_country'] ) ? $getpluginoptionarray['wcblu_block_country'] : '' );
        $optionsBlockCountry = ( !empty( $fetchCurrentBrowser ) ? $fetchCurrentBrowser : array() );
        if ( !empty( $final_country_merged_array_result ) ) {
            if ( is_array( $final_country_merged_array_result ) ) {
                foreach ( $final_country_merged_array_result as $country_code => $country ) {
                    ?>
									<option 
									<?php 
                    if ( in_array( $country_code, $optionsBlockCountry, true ) ) {
                        echo 'selected';
                    }
                    ?>
									value="<?php 
                    echo esc_attr( $country_code );
                    ?>"><?php 
                    esc_html_e( $country, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                    ?></option>
									<?php 
                }
            }
        }
        ?>

					</select>
					<div class="main_excel_div">
						<div class="all_chk">
							<input type="checkbox" name="all_chk_selection[country]" id="all_chk_selection" <?php 
        if ( $fetchAllSectionChk && array_key_exists( 'country', $fetchAllSectionChk ) ) {
            if ( $fetchAllSectionChk['country'] === 'on' ) {
                echo 'checked=checked';
            }
        }
        ?>/><?php 
        esc_html_e( 'Select All', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
						</div>
					</div>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="zip"><?php 
        esc_html_e( 'Blocked Zip/Postal Codes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
        echo wp_kses( __( 'Add multiple zip code to block users <a target="_blank" href="https://docs.thedotstore.com/article/155-block-fraud-customers-by-zipcode">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
            'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
        ) );
        ?>
							</div>
						</div>
					</label>
				</th>
				<td>
					<select id="zip"
							data-placeholder="<?php 
        esc_attr_e( 'Add Zip code separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>"
							name="zip[]" multiple="true"
							class="chosen-select-zip category-select chosen-rtl">
						<?php 
        $getpluginoption = get_option( 'wcblu_option' );
        $getpluginoptionarray = json_decode( $getpluginoption, true );
        $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_zip'] ) ? $getpluginoptionarray['wcblu_block_zip'] : '' );
        $optionsBlockZip = ( !empty( $fetchCurrentBrowser ) ? $fetchCurrentBrowser : array() );
        if ( !empty( $fetchSelecetedZip ) ) {
            if ( is_array( $fetchSelecetedZip ) ) {
                foreach ( $fetchSelecetedZip as $zipcode ) {
                    ?>
									<option 
									<?php 
                    if ( in_array( $zipcode, $optionsBlockZip, true ) ) {
                        echo 'selected';
                    }
                    ?>
									value="<?php 
                    echo esc_attr( $zipcode );
                    ?>"><?php 
                    esc_html_e( $zipcode, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                    ?></option>
									<?php 
                }
            }
        }
        ?>

					</select>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<th scope="row" class="titledesc"><label
					for="phone"><?php 
    esc_html_e( 'Blocked Phone Numbers', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
    esc_html_e( 'Add multiple phone number to block users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
								<?php 
    echo wp_kses( __( 'For wildcard number blocking add (*) after digits. <a target="_blank" href="https://docs.thedotstore.com/article/740-block-fraud-customer-by-phone-number">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
							</div>
						</div>
				</label>
			</th>
			<td>
				<select id="phone"
						data-placeholder="<?php 
    esc_attr_e( 'Add phone numbers separated by comma', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
						name="phone[]" multiple="true"
						class="chosen-select-phone category-select chosen-rtl"
						style="width: 62%">
					<option value=""></option>
					<?php 
    if ( !empty( $fetchSelectedPhone ) && '' !== $fetchSelectedPhone ) {
        if ( is_array( $fetchSelectedPhone ) ) {
            foreach ( $fetchSelectedPhone as $values ) {
                ?>
								<option selected value="<?php 
                echo esc_attr( $values );
                ?>"><?php 
                esc_html_e( $values, 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></option>
								<?php 
            }
        }
    }
    ?>
				</select>
				<?php 
    if ( class_exists( 'Easy_Digital_Downloads' ) ) {
        ?> 
					<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
        esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
				<?php 
    }
    ?>
			</td>
		</tr>
		<tr>
			<?php 
    if ( class_exists( 'WooCommerce' ) ) {
        $delivery_zones = WC_Shipping_Zones::get_zones();
    }
    ?>
			<th scope="row" class="titledesc"><label
					for="shippingZone"><?php 
    esc_html_e( 'Blocked Shipping Zones', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
							<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
								<?php 
    echo wp_kses( __( 'Add multiple shipping zones to block users <a target="_blank" href="https://docs.thedotstore.com/article/156-block-fraud-customers-by-shipping-zone">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
							</div>
						</div>
				</label>
			</th>
			<td>
				<select id="shipping_zone"
						data-placeholder="<?php 
    esc_attr_e( 'Add Shipping Zone', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>"
						name="zone[]" multiple="true"
						class="chosen-select-zone category-select chosen-rtl"
						style="width: 62%">
					<option value=""></option>
					<?php 
    $getpluginoption = get_option( 'wcblu_option' );
    $getpluginoptionarray = json_decode( $getpluginoption, true );
    $fetchCurrentBrowser = ( !empty( $getpluginoptionarray['wcblu_block_zone'] ) ? $getpluginoptionarray['wcblu_block_zone'] : '' );
    $optionsBlockZone = ( !empty( $fetchCurrentBrowser ) ? $fetchCurrentBrowser : array() );
    if ( !empty( $delivery_zones ) && '' !== $delivery_zones ) {
        if ( is_array( $delivery_zones ) ) {
            foreach ( (array) $delivery_zones as $the_zone ) {
                ?>
								<option 
								<?php 
                if ( in_array( $the_zone['zone_name'], $optionsBlockZone, true ) ) {
                    echo 'selected';
                }
                ?>
								value="<?php 
                echo esc_attr( $the_zone['zone_name'] );
                ?>"><?php 
                esc_html_e( $the_zone['zone_name'], 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
                ?></option>
								<?php 
            }
        }
    }
    ?>
				</select>
				<?php 
    if ( class_exists( 'Easy_Digital_Downloads' ) ) {
        ?> 
					<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
        esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></p>
				<?php 
    }
    ?>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="userRole"><?php 
    esc_html_e( 'Blocked User Roles', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    echo wp_kses( __( 'Add multiple user roles to block users <a target="_blank" href="https://docs.thedotstore.com/article/157-block-fraud-customers-by-user-role">Learn More.</a>', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
        'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
    ) );
    ?>
						</div>
					</div>
					<div class="wcblu-pro-label"></div>
				</label>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		</tbody>
	</table>

	<div class='heading_section_btm'>
		<h2><?php 
    esc_html_e( 'In this section you can add your custom messages OR our message will be printed as by default.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></h2>
	</div>
	<table class="form-table table-outer res-cl">
		<tbody>
		<tr>
			<th scope="row" class="titledesc"><label
					for="wc_email_msg_sett"><?php 
    esc_html_e( 'Email error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist email found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
						</div>
					</div>
				</label>
			</th>
			<td><textarea id="wc_email_msg_sett" class="set_message_box" style="width: 100%"
			              name="wc_email_msg_sett"><?php 
    echo wp_kses_post( $getemailmessage );
    ?></textarea>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_address_msg_sett"><?php 
        esc_html_e( 'Address error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
						<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
							<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist address found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
						</div>
					</div>
					</label>
				</th>
				<td><textarea id="wc_address_msg_sett" colspan="15" class="set_message_box" style="width: 100%" rows=""
							name="wc_address_msg_sett"><?php 
        echo wp_kses_post( $getaddressmessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<th scope="row" class="titledesc"><label
					for="wc_ip_msg_sett"><?php 
    esc_html_e( 'IP error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist IP found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
					</div>
				</div>
				</label>
			</th>
			<td><textarea id="wc_ip_msg_sett" class="set_message_box" style="width: 100%"
			              name="wc_ip_msg_sett"><?php 
    echo wp_kses_post( $getipmessage );
    ?></textarea>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="wc_domain_msg_sett"><?php 
    esc_html_e( 'Domain error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
				<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist domain found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
					</div>
				</div>
				</label>
			</th>
			<td><textarea id="wc_domain_msg_sett" colspan="15" class="set_message_box" style="width: 100%" rows=""
			              name="wc_domain_msg_sett"><?php 
    echo wp_kses_post( $getdomainmessage );
    ?></textarea>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_state_msg_sett"><?php 
        esc_html_e( 'State error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist state found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					</div>
				</div>
					</label>
				</th>
				<td><textarea id="wc_state_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_state_msg_sett"><?php 
        echo wp_kses_post( $getstatemessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_country_msg_sett"><?php 
        esc_html_e( 'Country error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist country found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
						<?php 
        esc_html_e( 'You can use "{country}" tag in notice which will replace with selected block country name on error', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					</div>
				</div>
					</label>
				</th>
				<td><textarea id="wc_country_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_country_msg_sett"><?php 
        echo wp_kses_post( $getcountrymessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_zpcode_msg_sett"><?php 
        esc_html_e( 'Zip code error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist zipcode found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					</div>
				</div>
					</label>
				</th>
				<td><textarea id="wc_zpcode_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_zpcode_msg_sett"><?php 
        echo wp_kses_post( $getzipmessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_phone_msg_sett"><?php 
        esc_html_e( 'Phone number error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist phone number found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					</div>
				</div>
					</label>
				</th>
				<td><textarea id="wc_phone_msg_sett" colspan="15" class="set_message_box" style="width: 100%" rows=""
							name="wc_phone_msg_sett"><?php 
        echo wp_kses_post( $getphonemessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_zone_msg_sett"><?php 
        esc_html_e( 'Zone error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					<div class="wcbfc-tooltip-rules">
					<div class="wcbfc-tooltiptext wcbfc-tooltip-bottom">
						<?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist zone found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?>
					</div>
				</div>
					</label>
				</th>
				<td><textarea id="wc_zone_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_zone_msg_sett"><?php 
        echo wp_kses_post( $getzonemessage );
        ?></textarea>
					<?php 
        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            ?> 
						<p class="wcbfc-pl-compatiblity-notice-bs"><div class="dashicons dashicons-warning" style="color:#d0a823;"></div><?php 
            esc_html_e( ' This feature will only works with woocommerce orders.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
            ?></p>
					<?php 
        }
        ?>
				</td>
			</tr>
		<?php 
    }
    ?>
		</tbody>
	</table>


	<p>
		<button type="submit" name="wcblu_submit" class="button button-primary" value="<?php 
    echo esc_attr( 'Save Changes' );
    ?>"><?php 
    esc_html_e( 'Save Changes', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
		<button type="button" name="wcblu_submit" id="wcblu_reset_settings" class="button button-secondary" value="<?php 
    echo esc_attr( 'Reset all settings' );
    ?>"><?php 
    esc_html_e( 'Reset all settings', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></button>
	</p>

	<?php 
    return ob_get_clean();
}

/**
 * @param $unblock_user_id
 *
 * @return false|string
 * Function to return unblock user
 */
function wcblu_permanent_delete_data(  $unblock_user_id  ) {
    wp_delete_post( $unblock_user_id );
    ob_start();
    ?>
	<div class="updated notice is-dismissible">
		<p>
			<?php 
    esc_html_e( 'User Deleted Permanantly', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?>
		</p>
	</div>
	<?php 
    return ob_get_clean();
}
