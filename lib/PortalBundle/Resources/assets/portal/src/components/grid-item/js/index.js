import {UiComponent} from "../../ui-component/js";

class GridItem extends UiComponent {
    constructor() {
        super();

        this.linkUrl = null;
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
     * Trigger page redirect when clicking on current grid item
     *
     * @returns {void}
     */
    initEventListeners() {
        this.addEventListener('click', () => {
            window.location.href = this.linkUrl;
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.linkUrl = this.getAttribute('link-url');

        this.initEventListeners();
    }
}

customElements.define('app-grid-item', GridItem);