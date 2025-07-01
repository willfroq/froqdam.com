import './bootstrap.js'

/**
 * Session expiration handler for Turbo requests
 * Redirects to login page when session expires during AJAX requests
 */
document.addEventListener("turbo:before-fetch-response", (event) => {
    const response = event.detail.fetchResponse.response;

    const isRedirectToLogin =
        response.status === 401 ||
        response.status === 403 ||
        (response.redirected && response.url.includes('/portal/auth/login'));

    if (isRedirectToLogin) {
        event.preventDefault();

        window.location.href = '/portal/auth/login';
    }
});