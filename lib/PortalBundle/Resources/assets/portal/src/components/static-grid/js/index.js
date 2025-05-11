import {Grid} from '../../grid/js';
import {setLoadingState} from '../../../hook/setLoadingState';

export class StaticGrid extends Grid {
    constructor() {
        super();

        this.hasMoreContent = false;
        this.loading = false;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.setDefaultPageSize();

        this.grid = this.querySelector('[data-role="grid"]');
        this.url = this.getAttribute('url') ?? '';
        this.hasMoreContent = this.getAttribute('next-page');

        if (this.hasMoreContent) {
            this.appFacets.setFilterWithoutEventDispatch('page', this.hasMoreContent);
        }

        this.initializeIntersectionObserver();
    }

    /**
     * Get the current page from the app facets filter
     *
     * @returns {number} The current page number
     */
    getCurrentPage() {
        const pageFilter = this.appFacets.getFilter('page');
        return pageFilter ? parseInt(pageFilter, 10) : 1;
    }

    /**
     * Sets the default page size filter and updates the URL accordingly
     *
     * @returns {void}
     */
    setDefaultPageSize() {
        if (this.appFacets.getFilter('size')) {
            return;
        }

        const defaultPageSize = this.determineDefaultPageSize();
        this.appFacets.setFilterWithoutEventDispatch('size', defaultPageSize);

        const newUrl = this.buildNewUrl();
        this.updateBrowserHistory(newUrl);
    }

    determineDefaultPageSize() {
        if (window.hasOwnProperty('staticListPageSize') && window.staticListPageSize) {
            return window.staticListPageSize;
        }
        return 24;
    }

    buildNewUrl() {
        return new URL(this.appFacets.buildRequestUrl(window.location.href));
    }

    updateBrowserHistory(newUrl) {
        window.history.pushState({ path: newUrl.href }, null, newUrl.href);
    }

    /**
     * Initialize IntersectionObserver event listeners on element and children
     *
     * @returns {void}
     */
    initializeIntersectionObserver() {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 1.0
        };

        this.intersectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && this.hasMoreContent && !this.loading) {
                    this.loadAndProcessItems()
                }
            });
        }, observerOptions);

        this.connectObserver();
    }

    /**
     * Function to load items and process the JSON data, with event dispatches to show and hide a search results loader.
     *
     * @returns {void}
     */
    loadAndProcessItems() {
        this.buttonLoader(true);

        this.loadItems()
            .then((jsonData) => {
                const { next_page } = jsonData

                this.hasMoreContent = next_page

                this.processJsonData(jsonData);
                this.removeObserver()
                this.popLoading(false)

                if (next_page){
                    this.connectObserver();
                }
            })
            .catch((error) => {
                console.error(`Error: ${error}`)
            })
            .finally(() => {
                this.buttonLoader(false);
            })
    }

    popLoading(show) {
        const viewMoreElement = document.querySelector('#view-more');

        const loadingIcon = viewMoreElement.querySelector('#loading-icon');
        if (loadingIcon) {
            return
        }

        if(!show) {
            loadingIcon.style.display = 'none';
            return
        }
        loadingIcon.style.display = 'block';

    }

    connectObserver() {
        const defaultSize = 7;
        const viewMoreElement = document.querySelector('#view-more');
        const gridItems = this.grid.querySelectorAll('app-grid-item');

        if(!gridItems) {
            return;
        }

        if (!viewMoreElement) {
            return;
        }

        this.target = gridItems[gridItems.length - (gridItems.length > defaultSize ? defaultSize : 1)];

        this.intersectionObserver.observe(this.target);

        viewMoreElement.addEventListener('click', () => {
            this.loadAndProcessItems();
        })
    }

    removeObserver() {
        if (this.observer) {
            this.observer.unobserve(this.target);
            this.observer.disconnect();
        }
    }

    buttonLoader(loading) {
        setLoadingState.call(this, loading);
    }
}

customElements.define('app-static-grid', StaticGrid);