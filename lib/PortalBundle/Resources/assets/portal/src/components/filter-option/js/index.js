import {UiComponent} from "../../ui-component/js";

export class FilterOption extends UiComponent {
    constructor() {
        super();

        this.filterCode = null;
        this.activeClass = 'filter-option--expanded';
        this.filterOptionContent = null;
        this.filterOptionTrigger = null;
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
        this.filterOptionTrigger.addEventListener('click', () => {
           this.toggleActiveClass();
        });
    }

    initialize() {
        this.filterCode = this.getAttribute('filter-code');
        this.filterOptionTrigger = this.querySelector('[data-role="filter_option_title"]');
        this.filterOptionContent = this.querySelector('[data-role="filter_option_content"]');

        if (this.filterCode) {
            this.appFacets.registerFilter(this.filterCode);
        }
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.initialize();
        this.initEventListeners();
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
}
