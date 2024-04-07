import {UiComponent} from "../../ui-component/js";

export class List extends UiComponent {
    constructor() {
        super();

        this.activeListHeaderSorterClass = 'list-header__sorter--active';
        this.url = null;
        this.list = null;
        this.preventScrollEventDispatch = false;
        this.loadOnScroll = true;
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
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.list = this.querySelector('[data-role="list"]');
        this.listHeader = document.querySelector('[data-role="list_header"]');
        this.url = this.getAttribute('url') ?? '';

        if (this.getAttribute('load-on-scroll')) {
            this.loadOnScroll = this.getAttribute('load-on-scroll') === 'true';
        }

        if (this.listHeader) {
            this.initHeader();
        }

        this.list.appendChild(this.listHeader);

        document.dispatchEvent(new Event('showSearchResultsLoader'));

        this.loadItems()
            .then((jsonData) => {
                this.processJsonData(jsonData);
                document.dispatchEvent(new Event('hideSearchResultsLoader'));
            })
            .catch(() => {
                document.dispatchEvent(new Event('hideSearchResultsLoader'));
            })

        this.initEventListeners();
    }

    /**
     * Generate HTML element for column sort button based on given filter code and sorting direction (asc/desc)
     *
     * @param {string} sortByCode
     * @param {string} sortingDirection
     * @returns {HTMLButtonElement}
     */
    getColumnSortButtonHTMLElement(sortByCode, sortingDirection) {
        let button = document.createElement('button');

        button.type = 'button';
        button.classList.add('list-header__sorter');
        button.classList.add(sortingDirection === 'asc' ? 'list-header__sorter-asc' : 'list-header__sorter-desc');
        button.setAttribute('data-role', 'list_header_sorter');
        button.setAttribute('data-sort-by', sortByCode);
        button.setAttribute('data-sorting-direction', sortingDirection);

        return button;
    }

    /**
     * Initialize columns sort buttons based on if current column is set to be sortable in data attribute
     *
     * @returns {void}
     */
    initHeader() {
        let sortableColumns = this.listHeader.querySelectorAll('[data-sortable="true"]');

        sortableColumns.forEach((sortableColumn) => {
            let actions = document.createElement('div');

            actions.classList.add('list-header__actions');
            actions.append(this.getColumnSortButtonHTMLElement(
                sortableColumn.getAttribute('data-sort-by'), 'asc')
            );
            actions.append(this.getColumnSortButtonHTMLElement(
                sortableColumn.getAttribute('data-sort-by'), 'desc')
            );

            sortableColumn.append(actions);
        });

        this.listHeader.classList.add('list-header');
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.appFacets.eventDispatcher.addEventListener('paginationUpdated', () => {
            document.dispatchEvent(new Event('showSearchResultsLoader'));
            this.loadItems()
                .then((jsonData) => {
                    this.processJsonData(jsonData, true);
                    document.dispatchEvent(new Event('hideSearchResultsLoader'));
                })
                .catch(() => {
                    document.dispatchEvent(new Event('hideSearchResultsLoader'));
                })
        });

        this.appFacets.eventDispatcher.addEventListener('facetsUpdated', () => {
            this.appFacets.removeFilterWithoutEventDispatch('page');

            document.dispatchEvent(new Event('showSearchResultsLoader'));
            this.loadItems()
                .then((jsonData) => {
                    if (jsonData && 'html' in jsonData) {
                        this.processJsonData(jsonData, true);
                        document.dispatchEvent(new Event('hideSearchResultsLoader'));
                    }
                })
                .catch(() => {
                    document.dispatchEvent(new Event('hideSearchResultsLoader'));
                });

            this.setActiveStateOnListHeaderSorterButtons();
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
                }
            });
        }
    }

    /**
     * Send AJAX request to load list items based on current filters and return promise for JSON response
     *
     * @returns {Promise<any>}
     */
    async loadItems() {
        if (!this.url) {
            throw new Error('AJAX End-point for list component was not defined.')
        }

        let response = await fetch(this.appFacets.buildRequestUrl(this.url).toString(), {
            redirect: 'manual',
        });

        if (response.status === 0) {
            window.location.href = '/portal/auth/login';
            return;
        }

        return response.json();
    }

    /**
     * Add/replace content in list based on given json data if an HTML field is present
     *
     * @param {Object} jsonData
     * @param {Boolean} replaceContent
     */
    processJsonData(jsonData, replaceContent = false) {
        this.appFacets.paginationData.nextPage = 'next_page' in jsonData ? jsonData['next_page'] : null;
        this.appFacets.paginationData.pages = 'pages' in jsonData ? jsonData['pages'] : null;

        if (replaceContent) {
            this.list.querySelectorAll('.list-item').forEach((listItem) => {
                listItem.remove();
            });
        }

        if (jsonData && 'html' in jsonData) {
            this.list.innerHTML += jsonData['html'];
        }

        if (jsonData && 'next_page' in jsonData) {
            this.appFacets.paginationData.nextPage = jsonData['next_page'];
            this.appFacets.setFilterWithoutEventDispatch('page', jsonData['next_page']);
        }

        if (jsonData && 'pages' in jsonData) {
            this.appFacets.paginationData.pages = jsonData['pages'];
        }

        setTimeout(() => {
            this.preventScrollEventDispatch = jsonData.hasOwnProperty('next_page') && !jsonData['next_page'];
            this.querySelectorAll('.scroll-loader').forEach((scrollLoader) => {
                scrollLoader.remove();
            });
        }, 250);

        this.appFacets.dispatchReloadPaginationEvent();
    }

    /**
     * Set active CSS classes on sort buttons in columns based on current filters
     *
     * @returns {void}
     */
    setActiveStateOnListHeaderSorterButtons() {
        this.querySelectorAll('[data-role="list_header_sorter"]').forEach((listHeaderSorter) => {
            let sortByCode = listHeaderSorter.getAttribute('data-sort-by'),
                sortDirection = listHeaderSorter.getAttribute('data-sorting-direction'),
                method = this.appFacets.getFilter('sort_by') === sortByCode &&
                this.appFacets.getFilter('sort_direction') === sortDirection ? 'add' : 'remove';

            listHeaderSorter.classList[method](this.activeListHeaderSorterClass);
        });
    }
}

customElements.define('app-list', List);
