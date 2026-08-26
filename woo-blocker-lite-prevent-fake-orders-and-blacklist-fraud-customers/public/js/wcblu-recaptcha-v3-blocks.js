/**
 * Google reCAPTCHA v3 for WooCommerce Checkout Blocks.
 * Executes an invisible token and stores it in checkout extension data
 * for Store API server-side verification.
 */
(function () {
	'use strict';

	var cfg = window.wcbluRecaptchaV3Blocks || {};
	var siteKey = cfg.siteKey || '';
	var action = cfg.action || 'wcbfc_validate_v3_recaptcha';
	var namespace = cfg.namespace || 'wcblu-recaptcha-v3';
	var refreshMs = parseInt(cfg.refreshMs, 10) || 90000;
	var refreshTimer = null;

	if (!siteKey) {
		return;
	}

	function canUseCheckoutStore() {
		return (
			window.wp &&
			wp.data &&
			typeof wp.data.dispatch === 'function' &&
			wp.data.dispatch('wc/store/checkout') &&
			typeof wp.data.dispatch('wc/store/checkout').setExtensionData === 'function'
		);
	}

	function setToken(token) {
		if (!token || !canUseCheckoutStore()) {
			return;
		}
		wp.data.dispatch('wc/store/checkout').setExtensionData(namespace, {
			token: token
		});
	}

	function executeToken() {
		if (typeof window.grecaptcha === 'undefined' || typeof window.grecaptcha.execute !== 'function') {
			return;
		}

		window.grecaptcha.ready(function () {
			window.grecaptcha
				.execute(siteKey, { action: action })
				.then(function (token) {
					setToken(token);
				})
				.catch(function () {
					// Keep last good token if a refresh fails.
				});
		});
	}

	function startRefresh() {
		if (refreshTimer) {
			clearInterval(refreshTimer);
		}
		refreshTimer = setInterval(executeToken, refreshMs);
	}

	function boot() {
		if (!canUseCheckoutStore()) {
			return false;
		}
		executeToken();
		startRefresh();
		return true;
	}

	function waitForStore(attemptsLeft) {
		if (boot() || attemptsLeft <= 0) {
			return;
		}
		window.setTimeout(function () {
			waitForStore(attemptsLeft - 1);
		}, 250);
	}

	function waitForGrecaptcha(attemptsLeft) {
		if (typeof window.grecaptcha !== 'undefined' && typeof window.grecaptcha.execute === 'function') {
			waitForStore(40);
			return;
		}
		if (attemptsLeft <= 0) {
			return;
		}
		window.setTimeout(function () {
			waitForGrecaptcha(attemptsLeft - 1);
		}, 250);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			waitForGrecaptcha(40);
		});
	} else {
		waitForGrecaptcha(40);
	}
})();
