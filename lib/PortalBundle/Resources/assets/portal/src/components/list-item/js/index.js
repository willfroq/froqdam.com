import {UiComponent} from "../../ui-component/js";

class ListItem extends UiComponent {
    constructor() {
        super();

        this.activeDropdownClass = 'list-item__actions-dropdown--active';
        this.actionsButton = null;
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
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        document.body.addEventListener('click', (e) => {
            if (
                this.classList.contains(this.activeDropdownClass) &&
                e.target.matches(':not(.list-item__actions-button):not(.list-item__actions-dropdown):not(.list-item__actions-dropdown *)')
            ) {
                this.classList.remove(this.activeDropdownClass);
            }
        });

        this.addEventListener('click', (e) => {
            if (
                e.target.matches('.list-item__actions-button') ||
                e.target.matches('.list-item__actions-button *') ||
                e.target.matches('.list-item__actions-dropdown *')
            ) {
                return;
            }

            this.querySelector('[data-role="list_item_goto_link"]').click();
        });

        if (!this.actionsButton) {
            return;
        }

        this.actionsButton.parentElement.addEventListener('click', (e) => {
            this.toggleActiveDropdown();
        });
    }

    /**
     * Remove any list item dropdown added to the main body element
     *
     * @returns {void}
     */
    removeActiveDropdowns() {
        document.querySelectorAll('.list-item').forEach((listItem) => {
            listItem.classList.remove(this.activeDropdownClass);
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.actionsButton = this.querySelector('[data-role="list_item_actions_button"]');
        this.actionsDropdown = this.querySelector('[data-role="list_item_actions_dropdown"]');
        window.setTimeout(this.initEventListeners.bind(this), 500);
    }

    /**
     * Create/destroy list item dropdown based on current state
     *
     * @returns {void}
     */
    toggleActiveDropdown() {
        this.removeActiveDropdowns();
        this.classList.add(this.activeDropdownClass);
    }
}

customElements.define('app-list-item', ListItem);
