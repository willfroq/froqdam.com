import {UiComponent} from "../../ui-component/js";

class LayoutViewToggle extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'layout-view-toggle__button--active';
        this.buttonLoadingClass = 'button-loading'
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
            let buttonClassList = button.classList;

            button.addEventListener('click', () => {
                if (buttonClassList.contains(this.activeClass) || buttonClassList.contains(this.buttonLoadingClass)) {
                    return;
                }

                this.classList.add('loading');
                buttonClassList.add(this.buttonLoadingClass);

                if (buttonRole === 'list_view_toggle') {
                    this.updateViewType('list');
                    return
                }

                this.updateViewType('grid');
            });

            if (this.layoutType === 'list' && buttonRole === 'list_view_toggle') {
                buttonClassList.add(this.activeClass);
                return
            }

            if (this.layoutType === 'grid' && buttonRole === 'grid_view_toggle') {
                buttonClassList.add(this.activeClass);
                return
            }

            buttonClassList.remove(this.activeClass);
        });
    }

    updateViewType(type) {
        this.appFacets.setFilter('type', type)
    }
}

customElements.define('app-layout-view-toggle', LayoutViewToggle);
