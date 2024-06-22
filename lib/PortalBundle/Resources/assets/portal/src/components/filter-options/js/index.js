import {UiComponent} from "../../ui-component/js";

class FilterOptions extends UiComponent {
    constructor() {
        super();

        this.clearAllLinkText = 'Clear';
        this.filterOptionsTitleWrapper = null;
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
        this.clearAllLink.addEventListener('click', () => {
            this.appFacets.clearRegistry();
        });

        this.addEventListener('scroll', () => {
            window.filterOptionsScrollTop = this.scrollTop;
        });

        document.addEventListener('afterSetUrlParamsAsFilters', () => {
            let filtersAreApplied = false;

            const url = new URL(window.location.href);

            url.searchParams.forEach((value, key) => {
                if (key.indexOf('filters') !== -1) {
                    filtersAreApplied = true;
                }
            });

            this.clearAllLink.style.display = filtersAreApplied ? 'block' : 'none';
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.clearAllLink = document.createElement('a');
        this.clearAllLinkText = this.getAttribute('clear-all-link-text');
        this.filterOptionsTitleWrapper = this.querySelector('[data-role="filter_options_title_wrapper"]');

        this.clearAllLink.classList.add('filter-options__clear-all');
        this.clearAllLink.innerText = this.clearAllLinkText;
        this.clearAllLink.href = 'Javascript:void(0)';
        this.clearAllLink.style.display = 'none';

        if (this.filterOptionsTitleWrapper) {
            this.filterOptionsTitleWrapper.append(this.clearAllLink);
        }

        let filtersAreApplied = false;

        this.appFacets.filtersRegistry.forEach((filter) => {
            if (this.appFacets.getFilters().hasOwnProperty(filter)) {
                filtersAreApplied = true;
            }
        });

        this.clearAllLink.style.display = filtersAreApplied ? 'block' : 'none';

        if (window.filterOptionsScrollTop && window.filterOptionsScrollTop > 0) {
            window.setTimeout(() => {
                this.scrollTop = window.filterOptionsScrollTop;
            });
        }

        this.initEventListeners();
    }
}

customElements.define('app-filter-options', FilterOptions);
