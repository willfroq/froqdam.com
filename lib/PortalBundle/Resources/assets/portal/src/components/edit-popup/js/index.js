import {UiComponent} from "../../ui-component/js";

class EditPopup extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'edit-popup--active';
        this.activeBodyClass = 'has-active-edit-popup';
        this.form = null;
        this.formHeader = null;
        this.formInitialState = null;
        this.formUrl = null;
        this.submitUrl = null;
        this.submitButton = document.createElement('submit');

        this.submitButton.innerText = 'Apply';
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

    destroyPopup() {
        document.body.classList.remove(this.activeBodyClass);
        this.classList.remove(this.activeClass);
        this.remove();
    }

    getFormValues() {
        let values = {};

        for (let pair of new FormData(this.form).entries()) {
            values[pair[0]] = pair[1];
        }

        return values;
    }

    initForm() {
        this.loadForm().then((response) => {
            response.json().then((jsonData) => {
                if ('html' in jsonData) {
                    this.form.innerHTML = jsonData['html'];
                }

                this.formInitialState = this.getFormValues();
                this.formFooter = document.createElement('div');
                this.formHeader = this.form.querySelector('[data-role="edit_popup_form_header"]');
                this.closePopupButton = document.createElement('button');

                this.closePopupButton.type = 'button';
                this.closePopupButton.setAttribute('data-role', 'edit_popup_close_button')
                this.closePopupButton.classList.add('edit-popup__close-popup-button');

                this.formFooter.classList.add('edit-popup-form__footer');
                this.formHeader.append(this.closePopupButton);
                this.form.innerHTML += '' +
                    '<div class="edit-popup-form__footer">' +
                        '<div class="edit-popup-form__footer-changes-text" data-role="edit_popup_form_changes_text">' +
                            '0 Change(s) have been made' +
                        '</div>' +
                        '<div class="edit-popup-form__footer-actions">' +
                            '<button data-role="edit_popup_close_button">Cancel</button>' +
                            '<button data-role="edit_popup_submit_button" disabled>Apply</button>' +
                        '</div>' +
                    '</div>';

                this.classList.add(this.activeClass);
                this.initEventListeners();
            });
        });
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        this.addEventListener('click', (e) => {
            if (e.target.matches('[data-role="edit_popup_close_button"]')) {
                this.destroyPopup();
            }
        });

        this.form.addEventListener('change', () => {
            this.checkChanges();
        });

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();

            fetch(this.submitUrl, {
                method: 'POST'
            }).then(() => {
                this.destroyPopup();
            });
        });
    }

    async loadForm() {
        return await fetch(this.formUrl);
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.form = document.createElement('form');
        this.formUrl = this.getAttribute('form-url');
        this.submitUrl = this.getAttribute('submit-url');
        this.append(this.form);

        document.body.classList.add(this.activeBodyClass);
        this.initForm();
    }

    checkChanges() {
        let formCurrentState = this.getFormValues(),
            changes = 0;

        if (formCurrentState !== this.formInitialState) {
            for (let key in formCurrentState) {
                if (formCurrentState[key] !== this.formInitialState[key]) {
                    changes++;
                }
            }
        }

        this.querySelector('[data-role="edit_popup_form_changes_text"]').innerText = `${changes} Change(s) have been made`;
        this.querySelector('[data-role="edit_popup_submit_button"]').disabled = changes === 0;
    }
}

customElements.define('app-edit-popup', EditPopup);