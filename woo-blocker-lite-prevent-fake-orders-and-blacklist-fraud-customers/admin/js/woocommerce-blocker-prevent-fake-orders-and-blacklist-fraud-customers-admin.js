( function ($) {
	'use strict';
	$(window).load(function () {
		var arrDomainList, localize_json_output;
		localize_json_output = $.parseJSON($('input[name="localize_json_output"]').val());
		$('body').on('click', '#wcblu_reset_settings', function () {
			ajaxindicatorstart('Please wait..!!');
			jQuery.ajax({
				url: adminajax.ajaxurl,
				type: 'post',
				data: {
					action: 'wcblu_reset_settings',
					nonce:  adminajax.nonce,
				},
				success: function () {
					location.reload(true);
				},
			});
		});

		$('body').on('click', '.wcblu_export_settings', function(e){
			e.preventDefault();
			var action = $('input[name="wcblu_export_action"]').val();
			ajaxindicatorstart('Please wait..!!');
			jQuery.ajax({
				url: adminajax.ajaxurl,
				type: 'post',
				data: {
					action: action,
					nonce:  adminajax.nonce,
				},
				success: function (responce) {

					jQuery('.responce_message').remove();
					if (!responce || responce.success === false) {
						var errorText = responce && responce.data && responce.data.message ? responce.data.message : 'Export failed.';
						$('<span/>', { class: 'responce_message error', text: errorText }).insertAfter('.wcblu_export_settings');
						ajaxindicatorstop();
						return;
					}

					var message = $('<span/>', { class: 'responce_message success', text: responce.data.message }).insertAfter('.wcblu_export_settings');

					var exportData = responce.data.data || '';
					var fileName = responce.data.filename || 'export_settings.json';
					var exportBlob = new Blob([exportData], { type: 'application/json' });
					var exportUrl = window.URL.createObjectURL(exportBlob);
					var dynamic_download = $('<a />', { href: exportUrl, download: fileName });
					$('body').append(dynamic_download);
					dynamic_download[0].click();
					window.URL.revokeObjectURL(exportUrl);
					dynamic_download.remove();

					setTimeout(function(){
						message.remove();
					}, 5000);
					
					ajaxindicatorstop();
				},
			});
		});

		$('body').on('click', '.wcblu_import_setting', function(e){
			e.preventDefault();
			var action = $('input[name="wcblu_import_action"]').val();
			var json_file = $('input[name="import_file"]');
			jQuery('.responce_message').remove();

			if( json_file.val() === ''){
				$('<span/>', { class: 'responce_message error', text: adminajax.importError.exmptyFile }).insertAfter('.wcblu_import_setting');
				return false;
			}
			var ext = json_file.val().split('.').pop().toLowerCase();
			if($.inArray(ext, ['json']) === -1) {
				json_file.val('');
				$('<span/>', { class: 'responce_message error', text: adminajax.importError.invalidFile }).insertAfter('.wcblu_import_setting');
				return false;
			}
			var dataImport = new FormData();
			dataImport.append('import_file', json_file[0].files[0]);
			dataImport.append('nonce', adminajax.nonce);
			dataImport.append('action', action);
			ajaxindicatorstart('Please wait..!!');
			jQuery.ajax({
				url: adminajax.ajaxurl,
				type: 'post',
				processData: false,
				contentType: false,
				cache: false,
				data: dataImport,
				success: function (responce) {

					jQuery('.responce_message').remove();
					json_file.val('');
					var message = '';
					var messageText = responce && responce.message ? responce.message : ( responce && responce.data && responce.data.message ? responce.data.message : '' );
					if(responce.success){
						message = $('<span/>', { class: 'responce_message success', text: messageText }).insertAfter('.wcblu_import_setting');
					} else {
						message = $('<span/>', { class: 'responce_message error', text: messageText }).insertAfter('.wcblu_import_setting');
					}

					setTimeout(function(){
						message.remove();
					}, 5000);
					
					ajaxindicatorstop();
				},
			});
		});
		
		$('body').on('click', '#wc_enb_ext_bl', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		
		$('body').on('click', '#wc_user_register_type', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		
		$('body').on('click', '#wc_user_place_order_type', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});

		$('body').on('click', '#wcbfc_fraud_check_before_pay', function () {
			var f_name = $(this).attr('id'); 
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$('[data-label="'+ f_name +'"]').show();
			} else {
				$(this).attr('value', '0');
				$('[data-label="'+ f_name +'"]').hide();
			}
		});

		$('body').on('click', '#wcbfc_update_order_status_on_score', function () {
			var f_name = $(this).attr('id'); 
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$('[data-label="'+ f_name +'"]').show();
			} else {
				$(this).attr('value', '0');
				$('[data-label="'+ f_name +'"]').hide();
			}
		});

		$('body').on('click', '#wcbfc_email_notification', function () {
			var f_name = $(this).attr('id'); 
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$('[data-label="'+ f_name +'"]').show();
			} else {
				$(this).attr('value', '0');
				$('[data-label="'+ f_name +'"]').hide();
			}
		});

		$('body').on('click', '#wcbfc_first_order_status', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_first_order_custom', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_bca_order', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_proxy_order', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_international_order', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_suspecius_email', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_unsafe_countries', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_unsafe_countries_ip', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcblu_automatic_blacklist', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_enable_whitelist_payment_method', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$(this).parent().next().next('.wcblu_rule_field').show();
			} else {
				$(this).attr('value', '0');
				$(this).parent().next().next('.wcblu_rule_field').hide();
			}
		});
		$('body').on('click', '#wcbfc_enable_whitelist_user_roles', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$(this).parent().next().next('.wcblu_rule_field').show();
			} else {
				$(this).attr('value', '0');
				$(this).parent().next().next('.wcblu_rule_field').hide();
			}
		});
		$('body').on('click', '#wcbfc_enable_whitelist_ips', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$(this).parent().next().next('.wcblu_rule_field').show();
			} else {
				$(this).attr('value', '0');
				$(this).parent().next().next('.wcblu_rule_field').hide();
			}
		});
		$('body').on('change', '#wcbfc_recaptcha_version', function () {
			var version = $(this).val();
			$('.wcblu_versions_key').hide();
			if( 'wcblu_v2_keys' === version ){
				$('.wcblu_v2_keys').show();
			}else if( 'wcblu_v3_keys' === version ){
				$('.wcblu_v3_keys').show();
			} else {
				$('.wcblu_versions_key'). hide();
			}
		});
		$('body').on('click', '#wcbfc_cod_block_status', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$(this).parent().next('.wcblu_captcha_settings').show();
			} else {
				$(this).attr('value', '0');
				$(this).parent().next('.wcblu_captcha_settings').hide();
			}
		});
		$('body').on('click', '#wcbfc_cbf_status', function () {
			const $this = $(this);
			const isChecked = $this.is(':checked');

			$this.val(isChecked ? '1' : '0');
			$this.parent().next('.wcblu_checkout_payment_settings').toggle(isChecked);
		});
		$('body').on('click', '#wcbfc_recaptcha_status', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
				$(this).parent().next('.wcblu_captcha_settings').show();
			} else {
				$(this).attr('value', '0');
				$(this).parent().next('.wcblu_captcha_settings').hide();
			}
		});
		$('body').on('click', '#wcbfc_billing_phone_number_order', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_billing_shipping_geo_match', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_ip_multiple_check', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_order_avg_amount_check', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_order_amount_check', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_too_many_oa_check', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_too_many_o_failed_a_check', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '#wcbfc_no_of_allow_order_between_time', function () {
			if ($(this).is(':checked')) {
				$(this).attr('value', '1');
			} else {
				$(this).attr('value', '0');
			}
		});
		$('body').on('click', '.wcbfc-description-tooltip-icon', function () {
			$(this).parents('td').find('.wcbfc-tooltip-sh').toggle();
		});
		$('body').on('click', '.dotstore-sidebar-section .content_box .et-star-rating label', function(e){
			e.stopImmediatePropagation();
			var rurl = jQuery('#wcblu-review-url').val();
			window.open( rurl, '_blank' );
		});

		$('#email').on('select2:select', function () {
			// var data = e.params.data;
			// var res = ValidateEmail(data.text);
			// if( 'no' === res  ){ return; }
			
		});

		$( '.woocommerce_blocker_tab_description' ).click( function( event ) {
			event.preventDefault();
			$( this ).next( 'p.description' ).toggle();
		} );

		$( '.woocommerce_blocker_tab_description.file-fields' ).click( function( event ) {
			event.preventDefault();
			$( this ).next( 'div.description' ).toggle();
		} );

		/** Upgrade to pro modal */
		$(document).on('click', '#dotsstoremain .wcblu-pro-label, .wcblu-upgrade-pro-to-unlock', function(){
			$('body').addClass('wcblu-modal-visible');
		});

		$(document).on('click', '#dotsstoremain .modal-close-btn', function(){
			$('body').removeClass('wcblu-modal-visible');
		});

		$('#ip_address').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
			createTag: function(term) {
				var value = term.term;
				var valid = ValidateIPaddress(value);
				if ('' !== valid && 'yes' === valid ) {
					return {
					  id: value,
					  text: value
					};
				}
				return null;            
			}
		});
		$('#state').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		$('#country').select2();
		$('#domain').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		
		$('body').on('keyup', '#domain_chosen ul.chosen-choices li.search-field input', function (evt) {
			var c = evt.keyCode;
			var domain;
			
			if (188 === c || 13 === c || 59 === c || 186 === c) {
				if (13 === c) {
					domain = $(this).val();
				}
				if (186 === c) {
					domain = $(this).val().replace(';', '');
				}
				if (59 === c) {
					domain = $(this).val().replace(';', '');
				}
				if (188 === c) {
					domain = $(this).val().replace(',', '');
				}
				if ('' !== domain && -1 === $.inArray(domain, arrDomainList)) {
					
					arrDomainList.push(domain);
					$('<option/>', { value: domain, text: domain }).appendTo('#domain');
					$('#domain option[value=\'' + domain + '\']').prop('selected', true);
					$('#domain').trigger('chosen:updated');
				} else {
					$('#domain').trigger('chosen:updated');
				}
			}
		});
		
		$('body').on('blur', '#domain_chosen ul.chosen-choices li.search-field input', function () {
			var domain = $(this).val().replace(',', '');
			if ('' !== domain && -1 === $.inArray(domain, arrDomainList)) {
				arrDomainList.push(domain);
				$('<option/>', { value: domain, text: domain }).appendTo('#domain');
				$('#domain option[value=\'' + domain + '\']').prop('selected', true);
				$('#domain').trigger('chosen:updated');
			} else {
				$('#domain').trigger('chosen:updated');
			}
		});
		
		
		$('#domainext').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		
		$('#phone').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		
		$('#first_name').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
			
		});

		$('#last_name').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		$('#wcblu_address').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: ['\n'],
		});

		$('#user_agent').select2();
		
		$('#email').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
			createTag: function(term) {
				var value = term.term;
				if(validateEmail(value)) {
					return {
					  id: value,
					  text: value
					};
				}
				return null;            
			}
		});
		function validateEmail(email) {
			var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}
		$('.chosen-ducl-select-countries').select2();
		$('#wcblu_whitelist_payment_method').select2();
		$('#wcblu_whitelist_user_roles').select2();
		$('#wcbfc_suspecius_email_list').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		$('#zip').select2({
			tags: true,
			selectOnClose: false,
			tokenSeparators: [','],
		});
		$('#shipping_zone').select2();
		$('#userrole').select2();
        
		$('#file').change(function () {
			var ext = $('#file').val().split('.').pop().toLowerCase();
			if (-1 === $.inArray(ext, ['json'])) {
				wcbluShowNotification('fa fa-warning', 'Error', 'invalid extension!', 'error');
				wcbluHideNotification();
				$('#file').val('');
			}
		});
		$('#file_state').change(function () {
			var ext = $('#file_state').val().split('.').pop().toLowerCase();
			if (-1 === $.inArray(ext, ['json'])) {
				wcbluShowNotification('fa fa-warning', 'Error', 'invalid extension!', 'error');
				wcbluHideNotification();
				$('#file_state').val('');
			}
		});
		$('#file_zipcode').change(function () {
			var ext = $('#file_zipcode').val().split('.').pop().toLowerCase();
			if (-1 === $.inArray(ext, ['json'])) {
				wcbluShowNotification('fa fa-warning', 'Error', 'invalid extension!', 'error');
				wcbluHideNotification();
				$('#file_zipcode').val('');
			}
		});
		
		$(document).on('click', '#wcblu_import_setting', function () {
			var _uploadedFileValue = $('input[name="import_file"]').val();
			var _uploadedFileExt = _uploadedFileValue.split('.').pop().toLowerCase();
			if ('' === _uploadedFileExt) {
				wcbluShowNotification('fa fa-warning', 'Error', 'Please add file for upload.', 'error');
				wcbluHideNotification();
				return false;
			} else if (-1 === $.inArray(_uploadedFileExt, ['json'])) {
				wcbluShowNotification('fa fa-warning', 'Error', 'Please upload only .json file.', 'error');
				wcbluHideNotification();
				return false;
			}
		});
		
		$(document).on('click', '#all_chk_selection', function () {
			var matches = /\[(.*?)\]/g.exec($(this).attr('name'));
			if ($(this).prop('checked') === true) {
				$('#' + matches[1]).select2('destroy');
				$('#' + matches[1]).find('option').attr('selected',true);
				$('#' + matches[1]).select2();
			} else {
				$('#' + matches[1]).select2('destroy');
				$('#' + matches[1]).find('option').attr('selected',false);
				$('#' + matches[1]).select2();
			}
		});
		
		dotStoreNeededJs();

		$('#wcblu_settings_whitelist').on('focusout',function(){
			var whitelistemail = $('#wcblu_settings_whitelist').val();
			$.ajax({
				url: adminajax.ajaxurl,
				type : 'POST',  
				data: {
					'action':'wcblu_check_blacklist_whitelist',
					'whitelist':whitelistemail,
					'nonce':  adminajax.nonce,
				},
				success:function(result) {
					
					console.log(result);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		});

		$('.wcblu-main-table #message .notice-dismiss').on('click',function(){
			window.history.replaceState(null, null, window.location.href.split('&')[0]);
		});		
	});
	
	function dotStoreNeededJs () {
		$('#toplevel_page_woocommerce_blacklist_users').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
		$('#toplevel_page_woocommerce_blacklist_users > a').addClass('wp-has-current-submenu current').removeClass('wp-not-current-submenu');
		$('#toplevel_page_dots_store').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
		$('#toplevel_page_dots_store > a').addClass('wp-has-current-submenu current').removeClass('wp-not-current-submenu');
		$('li#menu-posts').removeClass('wp-not-current-submenu wp-has-current-submenu wp-menu-open current');
		$('li.mine').css('display', 'none');
		$('li.publish').css('display', 'none');

		$('a[href="admin.php?page=woocommerce_blacklist_users"]').parents().addClass('current wp-has-current-submenu');
        $('a[href="admin.php?page=woocommerce_blacklist_users"]').addClass('current');
	}
	
	/**
	 * Function defined to show the notification.
	 *
	 * @param iconClass
	 * @param headerText
	 * @param message
	 * @param successOrError
	 */
	function wcbluShowNotification (iconClass, headerText, message, successOrError) {
		
		$('.notification_popup .notification_icon i').removeClass().addClass(iconClass);
		$('.notification_popup .notification_message h3').text(headerText);
		$('.notification_popup .notification_message p').text(message);
		$('.notification_popup').removeClass('is-success is-error');
		if ('error' === successOrError) {
			$('.notification_popup').addClass('active is-error');
		} else if ('success' === successOrError) {
			$('.notification_popup').addClass('active is-success');
			
		}
		
	}
	
	/**
	 * Function to return hide error notification
	 */
	function wcbluHideNotification () {
		setTimeout(function () {
			$('.notification_popup').removeClass('active');
		}, 3000);
	}
	
	function ValidateIPaddress (ipaddress) {
		
		var ipFormat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
		
		//check that all the ip in range or without is match or not
		$.each(ipaddress.split('-'), function () {
			if (!( ipaddress.match(ipFormat) )) {
				return 'no';
			}
		});
		
		//If no any un matched IP found the return yes for IP address is valid
		return 'yes';
	}
		
	function ajaxindicatorstart (text) {
		var firstDiv,
			firstInnerDiv,
			subDiv;
		if ('resultLoading' !== $('body').find('#resultLoading').attr('id')) {
			firstDiv = $('<div/>', { id: 'resultLoading', style: 'display:none' }).appendTo('body');
			firstInnerDiv = $('<div/>').appendTo(firstDiv);
			$('<img/>', { src: adminajax.ajax_icon }).appendTo(firstInnerDiv);
			subDiv = $('<div/>').appendTo(firstInnerDiv);
			$('<span/>', { id: '', text: text }).appendTo(subDiv);
			$('<div/>', { class: 'bg' }).appendTo(firstDiv);
		} else {
			$('#ajax-quote').text(text);
		}
		
		$('#resultLoading').css({
			'width': '100%',
			'height': '100%',
			'position': 'fixed',
			'z-index': '10000000',
			'top': '0',
			'left': '0',
			'right': '0',
			'bottom': '0',
			'margin': 'auto',
		});
		$('#resultLoading .bg').css({
			'background': '#000000',
			'opacity': '0.7',
			'width': '100%',
			'height': '100%',
			'position': 'absolute',
			'top': '0',
		});
		$('#resultLoading>div:first').css({
			'width': '250px',
			'height': '75px',
			'text-align': 'center',
			'position': 'fixed',
			'top': '0',
			'left': '0',
			'right': '0',
			'bottom': '0',
			'margin': 'auto',
			'font-size': '16px',
			'z-index': '10',
			'color': '#ffffff',
		});
		$('#resultLoading .bg').height('100%');
		$('#resultLoading').fadeIn(300);
		$('body').css('cursor', 'wait');
		
	}
	
	function ajaxindicatorstop () {
		$('#resultLoading .bg').height('100%');
		$('#resultLoading').fadeOut(300);
		$('body').css('cursor', 'default');
	}

	// Set cookies
	function setCookie(name, value, minutes) {
		var expires = '';
		if (minutes) {
			var date = new Date();
			date.setTime(date.getTime() + (minutes * 60 * 1000));
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + (value || '') + expires + '; path=/';
	}

	// Get cookies
    function getCookie(name) {
        let nameEQ = name + '=';
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    /** Script for Freemius upgrade popup */
    function upgradeToProFreemius( couponCode ) {
        let handler;
        handler = FS.Checkout.configure({
            plugin_id: '3493',
            plan_id: '5573',
            public_key:'pk_9edf804dccd14eabfd00ff503acaf',
            image: 'https://www.thedotstore.com/wp-content/uploads/sites/1417/2023/10/WooCommerce-Fraud-Prevention-Banner-1-New.png',
            coupon: couponCode,
        });
        handler.open({
            name: 'WooCommerce Fraud Prevention Plugin',
            subtitle: 'You’re a step closer to our Pro features',
            licenses: jQuery('input[name="licence"]:checked').val(),
            purchaseCompleted: function( response ) {
                console.log (response);
            },
            success: function (response) {
                console.log (response);
            }
        });
    }

	/** Dynamic Promotional Bar START */
	$(document).on('click', '.dpbpop-close', function () {
		var popupName 		= $(this).attr('data-popup-name');
		setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
		$('.' + popupName).hide();
	});

	$(document).on('click', '.dpb-popup .dpb-popup-meta a', function () {
		var promotional_id         = $(this).parent().find('.dpbpop-close').attr('data-bar-id');
		
		//Create a new Student object using the values from the textfields
		var apiData = {
			'bar_id' : promotional_id
		};

		$.ajax({
			type: 'POST',
			url: adminajax.dpb_api_url + 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php',
			data: JSON.stringify(apiData),// now data come in this function
			dataType: 'json',
			cors: true,
			contentType:'application/json',
			
			success: function (data) {
				console.log(data);
			},
			error: function () {
			}
		 });
	});
	/** Dynamic Promotional Bar END */

	 /** Upgrade Dashboard Script START */
    // Dashboard features popup script
    $(document).on('click', '.dotstore-upgrade-dashboard .premium-key-fetures .premium-feature-popup', function (event) {
        let $trigger = $('.feature-explanation-popup, .feature-explanation-popup *');
        if(!$trigger.is(event.target) && $trigger.has(event.target).length === 0){
            $('.feature-explanation-popup-main').not($(this).find('.feature-explanation-popup-main')).hide();
            $(this).parents('li').find('.feature-explanation-popup-main').show();
            $('body').addClass('feature-explanation-popup-visible');
        }
    });
    $(document).on('click', '.dotstore-upgrade-dashboard .popup-close-btn', function () {
        $(this).parents('.feature-explanation-popup-main').hide();
        $('body').removeClass('feature-explanation-popup-visible');
    });
    /** Upgrade Dashboard Script End */

	$(document).ready(function () {
		/** Plugin Setup Wizard Script START */
		// Hide & show wizard steps based on the url params 
		var urlParams = new URLSearchParams(window.location.search);
		if (urlParams.has('require_license')) {
			$('.ds-plugin-setup-wizard-main .tab-panel').hide();
			$( '.ds-plugin-setup-wizard-main #step5' ).show();
		} else {
			setTimeout(function () {
				$( '.ds-plugin-setup-wizard-main #step1' ).show();
			}, 500);
		}
		
		// Plugin setup wizard steps script
		$(document).on('click', '.ds-plugin-setup-wizard-main .tab-panel .btn-primary:not(.ds-wizard-complete)', function () {
			var curruntStep = jQuery(this).closest('.tab-panel').attr('id');
			var nextStep = 'step' + ( parseInt( curruntStep.slice(4,5) ) + 1 ); // Masteringjs.io

			if( 'step5' !== curruntStep ) {
				// Youtube videos stop on next step
                $('iframe[src*="https://www.youtube.com/embed/"]').each(function(){
                   $(this).attr('src', $(this).attr('src'));
                   return false;
                });
                
				jQuery( '#' + curruntStep ).hide();
				jQuery( '#' + nextStep ).show();   
			}
		});

		// Get allow for marketing or not
		if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
			$('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
		} else {
			$('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
		}

		// Get allow for marketing or not on change	    
		$(document).on( 'change', '.ds-plugin-setup-wizard-main .ds_count_me_in', function() {
			if ( this.checked ) {
				$('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
			} else {
				$('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
			}
		});

		// Complete setup wizard
		$(document).on( 'click', '.ds-plugin-setup-wizard-main .tab-panel .ds-wizard-complete', function() {
			if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
				$( '.fs-actions button'  ).trigger('click');
			} else {
				$('.fs-actions #skip_activation')[0].click();
			}
		});

		// Send setup wizard data on Ajax callback
		$(document).on( 'click', '.ds-plugin-setup-wizard-main .fs-actions button', function() {
			var wizardData = {
				'action': 'wcblu_plugin_setup_wizard_submit',
				'survey_list': $('.ds-plugin-setup-wizard-main .ds-wizard-where-hear-select').val(),
				'nonce':  adminajax.nonce,
			};

			$.ajax({
				url: adminajax.ajaxurl,
				data: wizardData,
				success: function ( success ) {
					console.log(success);
				}
			});
		});
		/** Plugin Setup Wizard Script End */

		/** Script for Freemius upgrade popup */
        $(document).on('click', '.dots-header .dots-upgrade-btn, .dotstore-upgrade-dashboard .upgrade-now', function(e){
            e.preventDefault();
            upgradeToProFreemius( '' );
        });
        $(document).on('click', '.upgrade-to-pro-modal-main .upgrade-now', function(e){
            e.preventDefault();
            $('body').removeClass('wcblu-modal-visible');
            let couponCode = $('.upgrade-to-pro-discount-code').val();
            upgradeToProFreemius( couponCode );
        });

        // Script for Beacon configuration
        var helpBeaconCookie = getCookie( 'wcblu-help-beacon-hide' );
        if ( ! helpBeaconCookie ) {
            Beacon('init', 'afe1c188-3c3b-4c5f-9dbd-87329301c920');
            Beacon('config', {
                display: {
                    style: 'icon',
                    iconImage: 'message',
                    zIndex: '99999'
                }
            });

            // Add plugin articles IDs to display in beacon
            Beacon('suggest', ['5dfca7aa04286364bc93181f', '5d934ed12c7d3a7e9ae1da53', '5d934f642c7d3a7e9ae1da65', '5df0dbd204286364bc92bb9b', '5d9352a12c7d3a7e9ae1dac6']);

            // Add custom close icon form beacon
            setTimeout(function() {
                if ( $( '.hsds-beacon .BeaconFabButtonFrame' ).length > 0 ) {
                    let newElement = document.createElement('span');
                    newElement.classList.add('dashicons', 'dashicons-no-alt', 'dots-beacon-close');
                    let container = document.getElementsByClassName('BeaconFabButtonFrame');
                    container[0].appendChild( newElement );
                }
            }, 3000);

            // Hide beacon
            $(document).on('click', '.dots-beacon-close', function(){
                Beacon('destroy');
                setCookie( 'wcblu-help-beacon-hide' , 'true', 24 * 60 );
            });
        }
		jQuery('.wcblu_rule_field .wcbfc_rules_weights').on('change', function() {
			var value = jQuery(this).val();
			jQuery(this).parent().parent().next().find('.wcbfc-progressBar').val(value);
			updateTooltip();
		});
		// Initialize tooltip
		updateTooltip();

		const progressBars = document.getElementsByClassName('wcbfc-progressBar');

		for (let i = 0; i < progressBars.length; i++) {
			progressBars[i].addEventListener('input', updateTooltip);
		}
		
		$('.general-rules .wcbfc-control-settings input[type="checkbox"]').change(function() {
			var $checkbox = $(this);
			var $inputNumber = $(this).parent().next().find('input[type="number"]');
			$checkbox.change(wcbfc_toggleInput( $checkbox, $inputNumber));
		});
		
		$('.general-rules .wcbfc-control-settings .wcbfc_rules_weights').each(function() {
			if( parseInt($(this).val()) === 0 ) {
				$(this).prop('disabled', true);
				$(this).parent().prev().find('input[type="checkbox"]').prop('checked', false);
			}
		});
		$('#wcblu_plugin_general_settings .wcbfc-control-settings select').on('change', function() {
			var value = jQuery(this).val();
			jQuery(this).parent().next().find('.wcbfc-progressBar').val(value);
			updateTooltip();
		});
		$('#wcblu_plugin_general_settings #wcbfc_settings_low_risk_threshold').on('change', function() {
			$('#wcbfc_settings_high_risk_threshold').attr('min', $(this).val());
			$(this).parent().parent().next().find('.wcbfc-progressBar.medium').val($(this).val());
			updateTooltip();
		});
		$('#wcblu_plugin_general_settings #wcbfc_settings_high_risk_threshold').on('change', function() {
			$('#wcbfc_settings_low_risk_threshold').attr('max', $(this).val());
			$(this).parent().parent().next().find('.wcbfc-progressBar.high').val($(this).val());
			updateTooltip();
		});
		jQuery('#wcblu_plugin_general_settings .wcbfc-tooltip-rules').hover(function(event) {
			jQuery(this).css('z-index', event.type === 'mouseenter' ? '9' : '');
		});
		
		$('body').on('click', '#wcbfc_email_notification,#wcbfc_update_order_status_on_score,#wcbfc_fraud_check_before_pay', function () {
			var f_name = jQuery('input[name="wcbfc_fraud_check_status"]').is(':checked');
			if( false === f_name && true === jQuery(this).is(':checked') ){
				alert('Please Enable Automatic Fraud Check');
			}
		});
	});
} )(jQuery);

// Function to toggle the input field
function wcbfc_toggleInput($checkbox, $inputNumber) {
	if ($checkbox.is(':checked')) {
		$inputNumber.prop('disabled', false);
	} else {
		$inputNumber.prop('disabled', true);
		$inputNumber.trigger('change');
	}
}


function updateTooltip() {
	const progressBars = document.getElementsByClassName('wcbfc-progressBar');
	const tooltips = document.getElementsByClassName('wcbfc-tooltip');
	for (let i = 0; i < progressBars.length; i++) {
		const progressBar = progressBars[i];
		const tooltip = tooltips[i];
		const max = progressBar.max;
		const value = progressBar.value;
		const percentage = (value / max) * 100;
		const reducedPercentage = percentage - (percentage * 0.10);
	
		tooltip.textContent = value + '';
		tooltip.style.left = reducedPercentage + '%';
	}
}
