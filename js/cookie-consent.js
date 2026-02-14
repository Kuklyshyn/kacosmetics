/**
 * Cookie Consent Banner
 * GDPR compliant cookie consent for Slovak/EU legislation
 */
(function() {
    'use strict';

    const COOKIE_NAME = 'kac_cookie_consent';
    const COOKIE_EXPIRY_DAYS = 365;

    // Cookie categories
    const COOKIE_CATEGORIES = {
        necessary: true, // Always enabled
        analytics: false,
        marketing: false
    };

    /**
     * Get cookie value
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    /**
     * Set cookie
     */
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    /**
     * Check if consent has been given
     */
    function hasConsent() {
        return getCookie(COOKIE_NAME) !== null;
    }

    /**
     * Get consent preferences
     */
    function getConsentPreferences() {
        const consent = getCookie(COOKIE_NAME);
        if (consent) {
            try {
                return JSON.parse(decodeURIComponent(consent));
            } catch (e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Save consent preferences
     */
    function saveConsent(preferences) {
        setCookie(COOKIE_NAME, encodeURIComponent(JSON.stringify(preferences)), COOKIE_EXPIRY_DAYS);

        // Dispatch custom event for other scripts to listen to
        window.dispatchEvent(new CustomEvent('cookieConsentUpdated', {
            detail: preferences
        }));

        // Apply consent preferences
        applyConsentPreferences(preferences);
    }

    /**
     * Apply consent preferences (enable/disable tracking scripts)
     */
    function applyConsentPreferences(preferences) {
        // Analytics (e.g., Google Analytics)
        if (preferences.analytics) {
            enableAnalytics();
        } else {
            disableAnalytics();
        }

        // Marketing (e.g., Facebook Pixel, Google Ads)
        if (preferences.marketing) {
            enableMarketing();
        } else {
            disableMarketing();
        }
    }

    /**
     * Enable analytics tracking
     */
    function enableAnalytics() {
        // Google Analytics - grant consent
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }

    /**
     * Disable analytics tracking
     */
    function disableAnalytics() {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    }

    /**
     * Enable marketing tracking
     */
    function enableMarketing() {
        // Facebook Pixel
        if (typeof fbq === 'function') {
            fbq('consent', 'grant');
        }

        // Google Ads
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        }
    }

    /**
     * Disable marketing tracking
     */
    function disableMarketing() {
        if (typeof fbq === 'function') {
            fbq('consent', 'revoke');
        }

        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied'
            });
        }
    }

    /**
     * Show cookie banner
     */
    function showBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Hide cookie banner
     */
    function hideBanner() {
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    /**
     * Accept all cookies
     */
    function acceptAll() {
        const preferences = {
            necessary: true,
            analytics: true,
            marketing: true,
            timestamp: Date.now()
        };
        saveConsent(preferences);
        hideBanner();
    }

    /**
     * Reject all optional cookies
     */
    function rejectAll() {
        const preferences = {
            necessary: true,
            analytics: false,
            marketing: false,
            timestamp: Date.now()
        };
        saveConsent(preferences);
        hideBanner();
    }

    /**
     * Save custom settings
     */
    function saveSettings() {
        const analyticsCheckbox = document.getElementById('cookie-analytics');
        const marketingCheckbox = document.getElementById('cookie-marketing');

        const preferences = {
            necessary: true,
            analytics: analyticsCheckbox ? analyticsCheckbox.checked : false,
            marketing: marketingCheckbox ? marketingCheckbox.checked : false,
            timestamp: Date.now()
        };
        saveConsent(preferences);
        hideBanner();
    }

    /**
     * Toggle settings panel
     */
    function toggleSettingsPanel() {
        const panel = document.getElementById('cookie-settings-panel');
        if (panel) {
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
    }

    /**
     * Initialize cookie consent
     */
    function init() {
        // Check if consent already given
        if (hasConsent()) {
            const preferences = getConsentPreferences();
            if (preferences) {
                applyConsentPreferences(preferences);
            }
            return;
        }

        // Show banner if no consent
        showBanner();

        // Event listeners
        const acceptBtn = document.getElementById('cookie-accept-all');
        const rejectBtn = document.getElementById('cookie-reject-all');
        const settingsBtn = document.getElementById('cookie-settings-toggle');
        const saveBtn = document.getElementById('cookie-save-settings');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', acceptAll);
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', rejectAll);
        }

        if (settingsBtn) {
            settingsBtn.addEventListener('click', toggleSettingsPanel);
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', saveSettings);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose functions globally for manual control
    window.CookieConsent = {
        show: showBanner,
        hide: hideBanner,
        acceptAll: acceptAll,
        rejectAll: rejectAll,
        getPreferences: getConsentPreferences,
        hasConsent: hasConsent
    };

})();
