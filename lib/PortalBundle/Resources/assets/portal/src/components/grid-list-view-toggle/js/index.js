import {UiComponent} from "../../ui-component/js";

class LayoutViewToggle extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'layout-view-toggle__button--active';
        this.buttons = [];
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

    render() {
        this.buttons = this.querySelectorAll('button');
        this.layoutType = this.getAttribute('itemsLayout');

        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.buttons.forEach((button) => {
            let buttonRole = button.getAttribute('data-role');

            button.addEventListener('click', () => {
                if (buttonRole === 'list_view_toggle') {
                    this.updateViewType('list');
                    return
                }

                this.updateViewType('grid');
            });

            if (this.layoutType === 'list' && buttonRole === 'list_view_toggle') {
                button.classList.add(this.activeClass);
                return
            }

            if (this.layoutType === 'grid' && buttonRole === 'grid_view_toggle') {
                button.classList.add(this.activeClass);
                return
            }

            button.classList.remove(this.activeClass);
        });
    }

    updateViewType(type) {
        this.appFacets.setFilter('type', type)
    }
}

customElements.define('app-layout-view-toggle', LayoutViewToggle);
