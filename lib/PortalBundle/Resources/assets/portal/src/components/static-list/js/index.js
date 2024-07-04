import {List} from '../../list/js';

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
        if (!this.appFacets.getFilter('size')) {
            let defaultPageSize = (
                window.hasOwnProperty('staticListPageSize') && window.staticListPageSize
            ) ? window.staticListPageSize : 12;

            this.appFacets.setFilterWithoutEventDispatch('size', defaultPageSize);

            let newUrl = new URL(this.appFacets.buildRequestUrl(window.location.href));

            window.history.pushState({path: newUrl.href}, null, newUrl.href);
        }

        setTimeout(() => {
            this.list = this.querySelector('[data-role="list"]');
            this.listHeader = document.querySelector('[data-role="list_header"]');
            this.url = this.getAttribute('url') ?? '';
            this.canLoadNextPageOnStart = this.getAttribute('can-load-next-page-on-start') === 'true' || false;

            if (!this.canLoadNextPageOnStart) {
                this.preventScrollEventDispatch = true;
            }

            if (this.getAttribute('load-on-scroll')) {
                this.loadOnScroll = this.getAttribute('load-on-scroll') === 'true';
            }

            if (this.listHeader) {
                this.initHeader();
            }

            if (this.canLoadNextPageOnStart) {
                let currentPage = this.appFacets.getFilter('page') ? parseInt(this.appFacets.getFilter('page')) : 1;

                this.appFacets.setFilterWithoutEventDispatch('page', currentPage + 1);
            }

            this.list.prepend(this.listHeader);

            this.setActiveStateOnListHeaderSorterButtons();
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

        if (this.loadOnScroll) {
            this.addEventListener('scroll', (e) => {
                if (this.preventScrollEventDispatch) {
                    return;
                }

                let maxScrollPosition = this.scrollHeight - this.clientHeight;

                if (this.scrollTop === maxScrollPosition) {
                    let scrollLoader = null;

                    this.preventScrollEventDispatch = true;

                    if (!this.querySelectorAll('.scroll-loader').length) {
                        let scrollLoader = document.createElement('div');

                        scrollLoader.classList.add('scroll-loader');
                        scrollLoader.innerHTML = 'Loading...';

                        this.appendChild(scrollLoader);
                    }

                    document.dispatchEvent(new Event('showSearchResultsLoader'));
                    this.loadItems()
                        .then((jsonData) => {
                            if (scrollLoader) {
                                scrollLoader.remove();
                            }

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

customElements.define('app-static-list', StaticList);
