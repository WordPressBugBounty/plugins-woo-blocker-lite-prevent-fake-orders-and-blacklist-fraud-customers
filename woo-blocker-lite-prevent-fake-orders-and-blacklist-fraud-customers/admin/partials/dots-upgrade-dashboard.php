<?php
/**
 * Handles free plugin user dashboard
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );

// Get product details from Freemius via API
$annual_plugin_price = '';
$monthly_plugin_price = '';
$plugin_details = array(
    'product_id' => 45398,
);

$api_url = add_query_arg(wp_rand(), '', WB_STORE_URL . 'wp-json/dotstore-product-fs-data/v2/dotstore-product-fs-data');
$final_api_url = add_query_arg($plugin_details, $api_url);

if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
    $api_response = vip_safe_wp_remote_get( $final_api_url, 3, 1, 20 );
} else {
    $api_response = wp_remote_get( $final_api_url ); // phpcs:ignore
}

if ( ( !is_wp_error($api_response)) && (200 === wp_remote_retrieve_response_code( $api_response ) ) ) {
	$api_response_body = wp_remote_retrieve_body($api_response);
	$plugin_pricing = json_decode( $api_response_body, true );

	if ( isset( $plugin_pricing ) && ! empty( $plugin_pricing ) ) {
		$first_element = reset( $plugin_pricing );
        if ( ! empty( $first_element['price_data'] ) ) {
            $first_price = reset( $first_element['price_data'] )['annual_price'];
        } else {
            $first_price = "0";
        }

        if( "0" !== $first_price ){
        	$annual_plugin_price = $first_price;
        	$monthly_plugin_price = round( intval( $first_price  ) / 12 );
        }
	}
}

// Set plugin key features content
$plugin_key_features = array(
    array(
        'title' => esc_html__( 'Block Users with Multiple Activities', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Enables the blocking of suspicious users during registration and checkout, with customizable criteria including domains, names, emails, IP addresses, etc.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-one-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'The feature effectively prevents fake orders, helping to build customer trust and confidence in the platform\'s security and reliability.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )
        ),
        'popup_examples' => array(
            esc_html__( 'It will show the risk icon on the order listing page.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            esc_html__( 'As per the screenshot, the blocked user will get a blocked message and fraud score with an applied rule, as shown in the widget.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        )
    ),
    array(
        'title' => esc_html__( 'Blacklist Bulk Fraud User Email', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Upload a list of blacklisted email addresses in bulk, helping you proactively block and prevent access for users associated with those emails.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-two-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Upload Bulk email address in blacklisted email Field using the upload blacklist email feature.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Use an xlsx format file to add multiple email addresses.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            esc_html__( 'A demo file is also available for reference.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )
        )
    ),
    array(
        'title' => esc_html__( 'Automated Fraud Blacklisting', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Blocks suspicious users from accessing your store based on their assigned fraud scores, providing an effective way to prevent fraudulent activities.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-three-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Blocks users based on their fraud scores, preventing potentially suspicious or malicious individuals from accessing the platform.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'As shown screenshot, enable auto fraud, set rules and fraud weight, and enable automatic blacklisting.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            esc_html__( 'It will automatically block users based on fraud score.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )
        )
    ),
    array(
        'title' => esc_html__( 'Configurable Risk Threshold Levels', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Customize and set risk threshold levels, allowing them to adjust the sensitivity of fraud detection according to their specific needs and preferences.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-four-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Allows users to adjust and fine-tune the sensitivity of fraud detection.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'The gray icon represents whitelisted orders, the yellow icon represents medium risks, and the red icon represents high-risk detections.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        )
    ),
    array(
        'title' => esc_html__( 'Customizable Fraud Rules & Scoring', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Own fraud prevention rules and scoring criteria, enabling a personalized approach to detecting and blocking suspicious activities in the store.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-five-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Modify fraud prevention rules and scoring criteria, tailoring the system to match specific security needs.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Set your rule weight, as shown in the screenshot.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            esc_html__( 'So if the first three rules apply, add a 10 + 20 + 20 = 50 fraud score.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )
        )
    ),
    array(
        'title' => esc_html__( 'Easy to Whitelist Exclusive Emails', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Allows users to create a select list of approved email addresses, ensuring that only whitelisted users can place orders on the store.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-six-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Enable trusted users to place orders without unnecessary restrictions or additional verifications.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'You can add multiple email addresses here.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
            esc_html__( 'Add one email address per line.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' )
        )
    ),
    array(
        'title' => esc_html__( 'Browse Blacklisted User Details', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'description' => esc_html__( 'Allows users to view comprehensive information about users who have been blacklisted, offering insights into the number of attempts.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        'popup_image' => esc_url( WB_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-seven-img.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Check all blacklisted users in the Blocked User List tab. All the blocked users will be listed with the title of their email.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Please check more details, such as displayed screenshots, an address, the number of attempts, the location where the User was Banned, etc.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ),
        )
    )
);
?>
	<div class="wcblu-section-left">
		<div class="dotstore-upgrade-dashboard">
			<div class="premium-benefits-section">
				<h2><?php esc_html_e( 'Upgrade to Unlock Premium Features', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h2>
				<p><?php esc_html_e( 'Upgrade to the premium version to access advanced features, enhance security measures, and protect your online transactions!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
			</div>
			<div class="premium-plugin-details">
				<div class="premium-key-fetures">
					<h3><?php esc_html_e( 'Discover Our Top Key Features', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
					<ul>
						<?php 
						if ( isset( $plugin_key_features ) && ! empty( $plugin_key_features ) ) {
							foreach( $plugin_key_features as $key_feature ) {
								?>
								<li>
									<h4><?php echo esc_html( $key_feature['title'] ); ?><span class="premium-feature-popup"></span></h4>
									<p><?php echo esc_html( $key_feature['description'] ); ?></p>
									<div class="feature-explanation-popup-main">
										<div class="feature-explanation-popup-outer">
											<div class="feature-explanation-popup-inner">
												<div class="feature-explanation-popup">
													<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'); ?>"></span>
													<div class="popup-body-content">
														<div class="feature-content">
															<h4><?php echo esc_html( $key_feature['title'] ); ?></h4>
															<?php 
															if ( isset( $key_feature['popup_content'] ) && ! empty( $key_feature['popup_content'] ) ) {
																foreach( $key_feature['popup_content'] as $feature_content ) {
																	?>
																	<p><?php echo esc_html( $feature_content ); ?></p>
																	<?php
																}
															}
															?>
															<ul>
																<?php 
																if ( isset( $key_feature['popup_examples'] ) && ! empty( $key_feature['popup_examples'] ) ) {
																	foreach( $key_feature['popup_examples'] as $feature_example ) {
																		?>
																		<li><?php echo esc_html( $feature_example ); ?></li>
																		<?php
																	}
																}
																?>
															</ul>
														</div>
														<div class="feature-image">
															<img src="<?php echo esc_url( $key_feature['popup_image'] ); ?>" alt="<?php echo esc_attr( $key_feature['title'] ); ?>">
														</div>
													</div>
												</div>		
											</div>
										</div>
									</div>
								</li>
								<?php
							}
						}
						?>
					</ul>
				</div>
				<div class="premium-plugin-buy">
					<div class="premium-buy-price-box">
						<div class="price-box-top">
							<div class="pricing-icon">
								<img src="<?php echo esc_url( WB_PLUGIN_URL . 'admin/images/premium-upgrade-img/pricing-1.svg' ); ?>" alt="<?php esc_attr_e( 'Personal Plan', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?>">
							</div>
							<h4><?php esc_html_e( 'Personal', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h4>
						</div>
						<div class="price-box-middle">
							<?php
							if ( ! empty( $annual_plugin_price ) ) {
								?>
								<div class="monthly-price-wrap"><?php echo esc_html( '$' . $monthly_plugin_price ); ?><span class="seprater">/</span><span><?php esc_html_e( 'month', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></span></div>
								<div class="yearly-price-wrap"><?php echo sprintf( esc_html__( 'Pay $%s today. Renews in 12 months.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), esc_html( $annual_plugin_price ) ); ?></div>
								<?php	
							}
							?>
							<span class="for-site"><?php esc_html_e( '1 site', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></span>
							<p class="price-desc"><?php esc_html_e( 'Great for website owners with a single WooCommerce Store', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
						</div>
						<div class="price-box-bottom">
							<a href="javascript:void(0);" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></a>
							<p class="trusted-by"><?php esc_html_e( 'Trusted by 100,000+ store owners and WP experts!', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
						</div>
					</div>
					<div class="premium-satisfaction-guarantee premium-satisfaction-guarantee-2">
						<div class="money-back-img">
							<img src="<?php echo esc_url(WB_PLUGIN_URL . 'admin/images/premium-upgrade-img/14-Days-Money-Back-Guarantee.png'); ?>" alt="<?php esc_attr_e('14-Day money-back guarantee', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'); ?>">
						</div>
						<div class="money-back-content">
							<h2><?php esc_html_e( '14-Day Satisfaction Guarantee', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h2>
							<p><?php esc_html_e( 'You are fully protected by our 100% Satisfaction Guarantee. If over the next 14 days you are unhappy with our plugin or have an issue that we are unable to resolve, we\'ll happily consider offering a 100% refund of your money.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
						</div>
					</div>
					<div class="plugin-customer-review">
						<h3><?php esc_html_e( 'Perfect for preventing fake orders', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
						<p>
							<?php echo wp_kses( __( 'We have started using this plugin on recommendation to prevent fake orders and we are <strong>very satisfied with the results</strong>, we will continue to <strong>use it for a long time</strong>.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ), array(
					                'strong' => array(),
					            ) ); 
				            ?>
			            </p>
						<div class="review-customer">
							<div class="customer-img">
								<img src="<?php echo esc_url(WB_PLUGIN_URL . 'admin/images/premium-upgrade-img/customer-profile-img.png'); ?>" alt="<?php esc_attr_e('Customer Profile Image', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'); ?>">
							</div>
							<div class="customer-name">
								<span><?php esc_html_e( 'Peter Howells', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></span>
								<div class="customer-rating-bottom">
									<div class="customer-ratings">
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
									</div>
									<div class="verified-customer">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Verified Customer', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-pro-faqs">
				<h2><?php esc_html_e( 'FAQs', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h2>
				<div class="upgrade-faqs-main">
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'Do you offer support for the plugin? What’s it like?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('Yes! You can read our %s or submit a %s. We are very responsive and strive to do our best to help you.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'),
								    '<a href="' . esc_url('https://docs.thedotstore.com/collection/146-fraud-preventions') . '" target="_blank">' . esc_html__('knowledge base', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers') . '</a>',
								    '<a href="' . esc_url('https://www.thedotstore.com/support-ticket/') . '" target="_blank">' . esc_html__('support ticket', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What payment methods do you accept?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'You can pay with your credit card using Stripe checkout. Or your PayPal account.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What’s your refund policy?', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'We have a 14-day money-back guarantee.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'I have more questions…', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('No problem, we’re happy to help! Please reach out at %s.', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers'),
								    '<a href="' . esc_url('mailto:hello@thedotstore.com') . '" target="_blank">' . esc_html('hello@thedotstore.com') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-premium-btn">
				<a href="javascript:void(0);" target="_blank" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers' ); ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="crown" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-crown fa-w-20 fa-3x" width="22" height="20"><path fill="#000" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z" class=""></path></svg></a>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
<?php 
