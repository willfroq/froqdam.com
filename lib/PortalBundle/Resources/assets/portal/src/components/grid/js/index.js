import {UiComponent} from "../../ui-component/js";

export class Grid extends UiComponent {
    constructor() {
        super();

        this.url = null;
        this.grid = null;
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
        this.url = this.getAttribute('url') ?? '';
    }

    /**
     * Send AJAX request to load grid items based on current filters and return promise for JSON response
     *
     * @returns {Promise<any>}
     */
    async loadItems() {
        this.validateUrl();
        let response = await fetch(this.appFacets.buildRequestUrl(this.url).toString());
        return response.json();
    }

    /**
     * Validate the URL for the AJAX endpoint
     *
     * @throws {Error} If the URL is not defined
     */
    validateUrl() {
        if (!this.url) {
            throw new Error('AJAX End-point for grid component was not defined.');
        }
    }

    /**
     * Build the request URL using appFacets
     *
     * @returns {URL} The built URL
     */
    buildRequestUrl() {
        return this.appFacets.buildRequestUrl(this.url);
    }

    /**
     * Add/replace content in grid based on given JSON data if an HTML field is present
     *
     * @param {Object} jsonData
     * @param {Boolean} replaceContent
     */
    processJsonData(jsonData, replaceContent = false) {
        if (!jsonData) return;

        this.updateGridContent(jsonData.html, replaceContent);
        this.updatePaginationData(jsonData);

        const params = new URLSearchParams(this.buildRequestUrl().search);
        if (this.isNumeric(params.get('page'))) {
            this.appFacets.dispatchReloadPaginationEvent();
        }
    }

    /**
     * Update the grid content
     *
     * @param {string} html - The HTML content to update
     * @param {Boolean} replaceContent - Flag to indicate whether to replace or append the content
     */
    updateGridContent(html, replaceContent) {
        if (html) {
            if (replaceContent) {
                this.grid.innerHTML = html;
            } else {
                this.grid.innerHTML += html;
            }
        }
    }

    /**
     * Update pagination data based on JSON data
     *
     * @param {Object} jsonData - The JSON data containing pagination info
     */
    updatePaginationData(jsonData) {
        if ('next_page' in jsonData) {
            this.appFacets.paginationData.nextPage = jsonData.next_page;
            this.appFacets.setFilterWithoutEventDispatch('page', jsonData.next_page);
        }

        if ('pages' in jsonData) {
            this.appFacets.paginationData.pages = jsonData.pages;
        }
    }
}

customElements.define('app-grid', Grid);