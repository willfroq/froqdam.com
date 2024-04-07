import {UiComponent} from "../../ui-component/js";

class Tab extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'tab--active';
        this.url = null;
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
        this.addEventListener('tabActivated', () => {
            this.classList.add(this.activeClass);
        });

        this.addEventListener('click', () => {
            this.parentElement.dispatchEvent(
                new CustomEvent('tabClick', {
                    detail: {
                        url: this.url
                    }
                })
            );

            this.toggleActiveClass();
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.url = this.getAttribute('url');

        this.initEventListeners();
    }

    /**
     * Add or remove active class based on current state
     *
     * @returns {void}
     */
    toggleActiveClass() {
        this.parentElement.querySelectorAll('[data-role="tab"]').forEach((tab) => {
            tab.classList.remove(this.activeClass);
        });

        let method = !this.classList.contains(this.activeClass) ? 'add' : 'remove';

        this.classList[method](this.activeClass);
    }
}

customElements.define('app-tab', Tab);