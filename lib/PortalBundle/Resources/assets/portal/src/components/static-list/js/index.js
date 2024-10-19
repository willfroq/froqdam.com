import { List } from '../../list/js';
import { setLoadingState } from '../../../hook/setLoadingState';

export class StaticList extends List {
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
        this.setDefaultPageSize();

        setTimeout(() => {
            this.list = this.querySelector('[data-role="list"]');
            this.listHeader = document.querySelector('[data-role="list_header"]');
            this.url = this.getAttribute('url') ?? '';
            this.hasMoreContent = this.getAttribute('next-page');

            if (this.hasMoreContent) {
                this.appFacets.setFilterWithoutEventDispatch('page', this.hasMoreContent);
            }

            this.initializeHeaderSortObserver();
            this.initializeIntersectionObserver();
        });
    }

    initializeHeaderSortObserver() {
        if (this.listHeader) {
            this.initHeader();
        }

        this.setActiveStateOnListHeaderSorterButtons();
        this.handleListHeaderSorterClick()
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
     * Designed to handle click events specifically for elements that are intended to act as list header sorters
     *
     * @returns {void}
     */
    handleListHeaderSorterClick() {
        this.addEventListener('click', (e) => {
            let target = e.target;

            if (target.getAttribute('data-role') === 'list_header_sorter') {
                let sortByCode = target.getAttribute('data-sort-by'),
                    sortingDirection = target.getAttribute('data-sorting-direction');

                this.appFacets.setFilterWithoutEventDispatch('sort_by', sortByCode);
                this.appFacets.setFilterWithoutEventDispatch('sort_direction', sortingDirection);
                this.appFacets.dispatchFacetsUpdatedEvent();
            }
        });
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
                if (!next_page){
                    this.removeObserver()
                }

                this.processJsonData(jsonData);
            })
            .catch((error) => {
                console.error(`Error: ${error}`)
            })
            .finally(() => {
                this.buttonLoader(false);
            })
    }

    connectObserver() {
        const viewMoreElement = document.getElementById('view-more');

        if (!viewMoreElement) {
            return;
        }

        this.intersectionObserver.observe(viewMoreElement);
        viewMoreElement.addEventListener('click', () => {
            this.loadAndProcessItems();
        })
    }

    removeObserver() {
        const viewMoreElement = document.getElementById('view-more');

        if (!viewMoreElement) {
            return;
        }

        viewMoreElement.style.display = 'none';

        if (this.observer) {
            this.observer.unobserve(viewMoreElement);
            this.observer.disconnect();
        }
    }

    buttonLoader(loading) {
        setLoadingState.call(this, loading);
    }
}

customElements.define('app-static-list', StaticList);
