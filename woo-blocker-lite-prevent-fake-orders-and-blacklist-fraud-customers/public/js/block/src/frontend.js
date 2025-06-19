import metadata from './block.json';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout'; // Import the ValidatedTextInput

const { registerCheckoutBlock } = wc.blocksCheckout;
const Block = ({ checkoutExtensionData }) => {
    const [captchaCheck, setCaptchaCheck] = useState('');
    const { setExtensionData } = checkoutExtensionData;
    const [recaptchaRendered, setRecaptchaRendered] = useState(false);

    useEffect(() => {
        const captchaKey = wcbfc_captcha_ajax.wcbfc_captcha_key; // Make sure this is defined
        const captchaEnable = wcbfc_captcha_ajax.wcbfc_recaptcha_status; // Make sure this is defined
        
        if (!captchaKey || captchaEnable !== '1') {
            console.warn('reCAPTCHA is disabled or site key is missing.');
            return; // Exit if the key is not present
        }

        const loadRecaptcha = () => {
            if (typeof window.grecaptcha === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.onload = renderRecaptcha;
                document.body.appendChild(script);
            } else {
                renderRecaptcha();
            }
        };

        const renderRecaptcha = () => {
            if (!recaptchaRendered) {
                window.grecaptcha.render('recaptcha-container', {
                    sitekey: wcbfc_captcha_ajax.wcbfc_captcha_key,
                    callback: (response) => {
                        setCaptchaCheck(response);
                    },
                    'expired-callback': () => {
                        setCaptchaCheck('');
                    },
                });
                setRecaptchaRendered(true);
            }
        };

        loadRecaptcha();
    }, [captchaCheck, recaptchaRendered]);

    useEffect(() => {
        setExtensionData('checkout-captcha-block', 'checkout_captcha', captchaCheck);
    }, [captchaCheck]);

    const handleInputChange = (value) => {
        setCaptchaCheck(value);
    };

    return (
        <div>
            <div id="recaptcha-container" className="g-recaptcha" style={{ marginBottom: '-15px', marginTop: '15px' }} ></div>
            <ValidatedTextInput
                id="captcha_check"
                type="text"
                required={true}
                value={captchaCheck ? '1' : ''} // Set value based on CAPTCHA status
                onChange={handleInputChange}
                className="hidden-captcha-input" // Optional class for styling
                disabled={false} // Can be adjusted based on your requirements
                style={{ display: 'none' }} // Hide the input visually
                errorMessage={__('Please complete the reCAPTCHA.', 'captcha-block')}
            />
        </div>
    );
};

const options = {
    metadata,
    component: Block
};

registerCheckoutBlock(options);
