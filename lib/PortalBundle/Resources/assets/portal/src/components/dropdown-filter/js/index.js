import {UiComponent} from "../../ui-component/js";

class DropdownFilter extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'dropdown-filter__active';
        this.dropdownFilterSelector = null;
        this.filterCode = null;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.filterCode = this.getAttribute('filter-code');
        this.dropdownFilterSelector = this.querySelector('[data-role="dropdown_filter_selector"]');

        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        document.body.addEventListener('click', (e) => {
            if (this.classList.contains(this.activeClass) && e.target.matches('*:not(.dropdown-filter):not(.dropdown-filter *)')) {
                this.classList.remove(this.activeClass);
            }
        });

        this.dropdownFilterSelector.addEventListener('click', () => {
            this.toggleActiveClass();
        });

        this.querySelectorAll('[data-role="dropdown_filter_list_item"]').forEach((listItem) => {
            listItem.addEventListener('click', () => {
                this.classList.remove(this.activeClass);
                this.dropdownFilterSelector.innerText = listItem.innerText.trim();

                this.appFacets.setFilter(this.filterCode, listItem.getAttribute('data-code'));
            });
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

customElements.define('app-dropdown-filter', DropdownFilter);