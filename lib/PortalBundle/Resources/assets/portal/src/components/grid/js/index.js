import {UiComponent} from "../../ui-component/js";

class Grid extends UiComponent {
    constructor() {
        super();

        this.url = null;
        this.grid = null;
        this.gridColumns = null;
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
        this.grid = this.querySelector('[data-role="grid"]');
        this.gridColumns = this.getAttribute('grid-columns');
        this.url = this.getAttribute('url') ?? '';

        if (this.gridColumns) {
            let gridTemplateColumns = '';

            for (let i = 0; i < this.gridColumns; i++) {
                gridTemplateColumns += '1fr ';
            }

            this.grid.style.gridTemplateColumns = gridTemplateColumns;
        }

        if (this.getAttribute('load-on-scroll')) {
            this.loadOnScroll = this.getAttribute('load-on-scroll') === 'true';
        }

        this.loadItems().then((jsonData) => {
            this.processJsonData(jsonData);
        });

        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        this.appFacets.eventDispatcher.addEventListener('paginationUpdated', () => {
            this.loadItems()
                .then((jsonData) => {
                    this.processJsonData(jsonData, true);
                })
        });

        this.appFacets.eventDispatcher.addEventListener('facetsUpdated', () => {
            this.appFacets.removeFilterWithoutEventDispatch('page');

            this.loadItems().then((jsonData) => {
                this.processJsonData(jsonData, true);
            });
        });

        if (this.loadOnScroll) {
            this.addEventListener('scroll', (e) => {
                if (this.preventScrollEventDispatch) {
                    return;
                }

                let maxScrollPosition = this.scrollHeight - this.clientHeight;

                if (this.scrollTop === maxScrollPosition) {
                    let scrollLoader = null;

                    if (!this.querySelectorAll('.scroll-loader').length) {
                        let scrollLoader = document.createElement('div');

                        scrollLoader.classList.add('scroll-loader');
                        scrollLoader.innerHTML = 'Loading...';

                        this.appendChild(scrollLoader);
                    }

                    this.loadItems()
                        .then((jsonData) => {
                            if (scrollLoader) {
                                scrollLoader.remove();
                            }

                            this.processJsonData(jsonData);
                        })
                }
            });
        }
    }

    /**
     * Send AJAX request to load grid items based on current filters and return promise for JSON response
     *
     * @returns {Promise<any>}
     */
    async loadItems() {
        if (!this.url) {
            throw new Error('AJAX End-point for grid component was not defined.')
        }

        let response = await fetch(this.appFacets.buildRequestUrl(this.url).toString());

        return response.json();
    }

    /**
     * Add/replace content in grid based on given json data if an HTML field is present
     *
     * @param {Object} jsonData
     * @param {Boolean} replaceContent
     */
    processJsonData(jsonData, replaceContent = false) {
        this.preventScrollEventDispatch = true;

        if (jsonData && 'html' in jsonData) {
            if (replaceContent) {
                this.grid.innerHTML = jsonData['html'];
            } else {
                this.grid.innerHTML += jsonData['html'];
            }
        }

        if (jsonData && 'next_page' in jsonData) {
            this.appFacets.paginationData.nextPage = jsonData['next_page'];
            this.appFacets.setFilterWithoutEventDispatch('page', jsonData['next_page']);
        }

        if (jsonData && 'pages' in jsonData) {
            this.appFacets.paginationData.pages = jsonData['pages'];
        }

        setTimeout(() => {
            this.preventScrollEventDispatch = false;
            this.querySelectorAll('.scroll-loader').forEach((scrollLoader) => {
                scrollLoader.remove();
            });
        }, 250);

        this.appFacets.dispatchReloadPaginationEvent();
    }
}

customElements.define('app-grid', Grid);