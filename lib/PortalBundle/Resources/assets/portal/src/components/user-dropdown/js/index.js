import {UiComponent} from '../../ui-component/js';

class UserDropdown extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'user-dropdown__active';
        this.userDropdownSelector = null;
        this.fullname = null;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.filterCode = this.getAttribute('filter-code');
        this.userDropdownSelector = this.querySelector('[data-role="user_dropdown_selector"]');
        this.fullname = this.getAttribute('fullname') ?? '';

        if (this.fullname) {
            let initials = '',
                fullNameParts = this.fullname.split(' ');

            for (let i = 0; i < fullNameParts.length; i++) {
                initials += fullNameParts[i].charAt(0).toUpperCase();
            }

            this.querySelector('[data-role="user_dropdown_full_name"]').textContent = this.fullname;
            this.querySelector('[data-role="user_dropdown_initials"]').textContent = initials;
        }

        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        document.body.addEventListener('click', (e) => {
            if (this.classList.contains(this.activeClass) && e.target.matches('*:not(.user-dropdown):not(.user-dropdown *)')) {
                this.classList.remove(this.activeClass);
            }
        });

        this.userDropdownSelector.addEventListener('click', () => {
            this.toggleActiveClass();
        });
    }

    /**
     * Add or remove active class based on current state
     *
     * @returns {void}
     */
    toggleActiveClass() {
        let method = !this.classList.contains(this.activeClass) ? 'add' : 'remove';

        this.classList[method](this.activeClass);
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
}

customElements.define('app-user-dropdown', UserDropdown);
