<?php

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
    /**
     * get messages
     */
    $getemailmessage = ( !empty( $getpluginoptionarray['wcblu_email_msg'] ) ? $getpluginoptionarray['wcblu_email_msg'] : esc_html__( 'This email address has been blocked, please try other email address or Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getstatemessage = ( !empty( $getpluginoptionarray['wcblu_state_msg'] ) ? $getpluginoptionarray['wcblu_state_msg'] : esc_html__( 'Sorry :( We are not shipping this products in this state.  Kindly contact admin. ', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getzipmessage = ( !empty( $getpluginoptionarray['wcblu_zpcode_msg'] ) ? $getpluginoptionarray['wcblu_zpcode_msg'] : esc_html__( 'Sorry :( We are not shipping this products in this location. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getipmessage = ( !empty( $getpluginoptionarray['wcblu_ip_msg'] ) ? $getpluginoptionarray['wcblu_ip_msg'] : esc_html__( 'This IP address has been blocked due to some reason, Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
    $getdomainmessage = ( !empty( $getpluginoptionarray['wcblu_domain_msg'] ) ? $getpluginoptionarray['wcblu_domain_msg'] : esc_html__( 'Sorry :( This domain has been blocked due to some reason. Kindly contact admin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ) );
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
			<th scope="row" class="titledesc"><label
					for=""><?php 
    esc_html_e( 'Type', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
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
				<p><?php 
    echo wp_kses_post( sprintf( '%1$s For Registration : %2$s Registration Time defines you want to block user while user going to register on your website.', '<b>', '</b>' ) );
    ?></p>
				<p><?php 
    echo wp_kses_post( sprintf( '%1$s For Place order : %2$s Place Order defines you want to block user while user placing order', '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc">
					<label for=""><?php 
        esc_html_e( 'Address Type', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
					<div class="wcblu-pro-label"></div>
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
    esc_html_e( 'Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
			</th>
			<td>
				<div class="">
					<input type="checkbox" id="wcblu_automatic_blacklist" name="wcblu_automatic_blacklist" value="" disabled>
					<label for="wcblu_automatic_blacklist"><?php 
    esc_html_e( 'Automatic Blacklisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="new-feature"><?php 
    echo esc_html_e( '- [New]', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></div>
					<div class="wcblu-pro-label"></div>
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
				<p><?php 
    esc_html_e( 'Add multiple email to block users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="first_name"><?php 
    esc_html_e( 'First name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
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
    esc_html_e( 'Last name', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
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
    esc_html_e( 'Address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="ip_address"><?php 
    esc_html_e( 'IP address', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
			</th>
			<td>
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
				<p><?php 
    esc_html_e( 'Add multiple IP address to block users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="domain"><?php 
    esc_html_e( 'Domain', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
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
				<p><?php 
    esc_html_e( 'Add multiple domain to block users e.g gmail.com', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="last_name"><?php 
    esc_html_e( 'Domain Extension', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
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
    esc_html_e( 'User Browser', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
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
        esc_html_e( 'State', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
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
					<p><?php 
        esc_html_e( 'Add multiple state to block users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<tr>
				<th scope="row" class="titledesc"><label
						for="country"><?php 
        esc_html_e( 'Country', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
						<div class="wcblu-pro-label"></div>
				</th>
				<td>
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
			<tr>
				<th scope="row" class="titledesc"><label
						for="zip"><?php 
        esc_html_e( 'Zip code', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
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
					<p><?php 
        esc_html_e( 'Add multiple zipcode to block users', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
					for="phone"><?php 
    esc_html_e( 'Phone number', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="shippingZone"><?php 
    esc_html_e( 'Shipping Zone', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
			</th>
			<td>
			<p class="primium_message"><?php 
    echo wp_kses_post( sprintf( esc_html__( '%1$s Note : %2$s You can access this feature in our premium plugin.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), '<b>', '</b>' ) );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="userRole"><?php 
    esc_html_e( 'User Role', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
					<div class="wcblu-pro-label"></div>
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
    ?></label>
			</th>
			<td><textarea id="wc_email_msg_sett" class="set_message_box" style="width: 100%"
			              name="wc_email_msg_sett"><?php 
    echo wp_kses_post( $getemailmessage );
    ?></textarea>
				<p><?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist email found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="wc_ip_msg_sett"><?php 
    esc_html_e( 'IP error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
			</th>
			<td><textarea id="wc_ip_msg_sett" class="set_message_box" style="width: 100%"
			              name="wc_ip_msg_sett"><?php 
    echo wp_kses_post( $getipmessage );
    ?></textarea>
				<p><?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist IP found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc"><label
					for="wc_domain_msg_sett"><?php 
    esc_html_e( 'Domain error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></label>
			</th>
			<td><textarea id="wc_domain_msg_sett" colspan="15" class="set_message_box" style="width: 100%" rows=""
			              name="wc_domain_msg_sett"><?php 
    echo wp_kses_post( $getdomainmessage );
    ?></textarea>
				<p><?php 
    esc_html_e( 'Enter the error message you want to show user when blacklist domain found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
    ?></p>
			</td>
		</tr>
		<?php 
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
        ?>
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_state_msg_sett"><?php 
        esc_html_e( 'State error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
				</th>
				<td><textarea id="wc_state_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_state_msg_sett"><?php 
        echo wp_kses_post( $getstatemessage );
        ?></textarea>
					<p><?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist state found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_country_msg_sett"><?php 
        esc_html_e( 'Country error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
						<div class="wcblu-pro-label"></div>
				</th>
				<td>
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
			<tr>
				<th scope="row" class="titledesc"><label
						for="wc_zpcode_msg_sett"><?php 
        esc_html_e( 'Zip code error message', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
        ?></label>
				</th>
				<td><textarea id="wc_zpcode_msg_sett" class="set_message_box" style="width: 100%"
							name="wc_zpcode_msg_sett"><?php 
        echo wp_kses_post( $getzipmessage );
        ?></textarea>
					<p><?php 
        esc_html_e( 'Enter the error message you want to show user when blacklist zipcode found', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' );
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
