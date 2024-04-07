import {UiComponent} from '../../ui-component/js';

class LoginForm extends UiComponent {
    constructor() {
        super();

        this.passwordFieldControl = null;
        this.passwordVisibilityToggleButton = document.createElement('button');

        this.passwordVisibilityToggleButton.type = 'button';
        this.passwordVisibilityToggleButton.classList.add('password-visibility-toggle-button');
    }

    /**
     * Callback to execute immediately after the HTML element is inserted in the DOM
     *
     * @returns {void}
     */
    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    /**
     * Initialize logic to switch visibility of password characters (change input element type) on button click
     *
     * @returns {void}
     */
    initEventListeners() {
        this.passwordVisibilityToggleButton.addEventListener('click', () => {
            let inputField = this.passwordFieldControl.querySelector('input');

            if (inputField.type === 'password') {
                inputField.type = 'text';
            } else if (inputField.type === 'text') {
                inputField.type = 'password';
            }
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.passwordFieldControl = this.querySelector('[data-role="password_field_control"]');

        this.passwordFieldControl.append(this.passwordVisibilityToggleButton);

        this.initEventListeners();
    }
}

customElements.define('app-login-form', LoginForm);
