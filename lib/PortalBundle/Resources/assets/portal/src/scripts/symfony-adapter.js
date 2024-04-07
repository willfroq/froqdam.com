/**
 * This utility provides any specific fix needed to integrate FroQ UI in the Symfony frontend
 */
window.onload = () => {
    setTimeout(() => {
        document.querySelectorAll('form').forEach((formEl) => {
            if (formEl.matches('.login-form form')) {
                return;
            }

            formEl.addEventListener('submit', (evt) => {
                evt.preventDefault();
            })
        });
    }, 500);
}