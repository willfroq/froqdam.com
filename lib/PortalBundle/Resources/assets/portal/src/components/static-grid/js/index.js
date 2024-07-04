import {Grid} from '../../grid/js';

export class StaticGrid extends Grid {
    constructor() {
        super();

        this.canLoadNextPageOnStart = true;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        if (!this.appFacets.getFilter('size')) {
            let defaultPageSize = (
                window.hasOwnProperty('staticListPageSize') && window.staticListPageSize
            ) ? window.staticListPageSize : 12;

            this.appFacets.setFilterWithoutEventDispatch('size', defaultPageSize);

            let newUrl = new URL(this.appFacets.buildRequestUrl(window.location.href));

            window.history.pushState({path: newUrl.href}, null, newUrl.href);
        }

        setTimeout(() => {
            this.grid = this.querySelector('[data-role="grid"]');
            this.url = this.getAttribute('url') ?? '';
            this.canLoadNextPageOnStart = this.getAttribute('can-load-next-page-on-start') === 'true' || false;

            if (!this.canLoadNextPageOnStart) {
                this.preventScrollEventDispatch = true;
            }

            if (this.canLoadNextPageOnStart) {
                let currentPage = this.appFacets.getFilter('page') ? parseInt(this.appFacets.getFilter('page')) : 1;

                this.appFacets.setFilterWithoutEventDispatch('page', currentPage + 1);
            }

            this.initEventListeners();
        });
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.appFacets.eventDispatcher.addEventListener('afterSetUrlParamsAsFilters', () => {
            if (!this.appFacets.getFilter('page') && this.canLoadNextPageOnStart) {
                this.appFacets.setFilterWithoutEventDispatch('page', 2)
            }
        });

        if (this.loadOnScroll) {
            this.addEventListener('scroll', (e) => {
                if (this.preventScrollEventDispatch) {
                    return;
                }

                let maxScrollPosition = this.scrollHeight - this.clientHeight;

                if (this.scrollTop === maxScrollPosition) {
                    document.dispatchEvent(new Event('showSearchResultsLoader'));

                    this.loadItems()
                        .then((jsonData) => {
                            this.processJsonData(jsonData);
                            document.dispatchEvent(new Event('hideSearchResultsLoader'));
                        })
                        .catch(() => {
                            document.dispatchEvent(new Event('hideSearchResultsLoader'));
                        })
                }
            });
        }
    }
}

customElements.define('app-static-grid', StaticGrid);