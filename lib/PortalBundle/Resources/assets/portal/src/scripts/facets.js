/**
 * This class groups all filters applied by the user in UI.
 * These filters are retrieved by single components when requesting content via AJAX.
 */
export class AppFacets {
    constructor(eventDispatcher) {
        this.eventDispatcher = eventDispatcher ?? document;
        this.filters = {};
        this.filtersRegistry = [];
        this.paginationData = {
            nextPage: null,
            pages: null
        };
        this.protectedKeywords = [
            'page',
            'sort_by',
            'sort_direction',
            'query',
            'size'
        ];

        /**
         * Only allow autofill of filters object when operating in global filter scopes (AKA Asset/SKU library page)
         */
        if (this.eventDispatcher === document) {
            setTimeout(() => {
                this.setUrlParamsAsFilters();
            }, 250);
        }
    }

    /**
     * Autofill currently applied filters with values from URL querystring parameters
     *
     * @returns {void}
     */
    setUrlParamsAsFilters() {
        let urlPath = window.location.href,
            url = new URL(urlPath);

        if (parseInt(url.searchParams['size']) > 0) {
            let urlParamsObject = this.convertUrlParamsToObject(urlPath);

            this.filters = urlParamsObject.hasOwnProperty('filters') ? urlParamsObject['filters'] : this.filters;

            url.searchParams.forEach((value, key) => {
                if (key.indexOf('filters') !== -1) {
                    return;
                }

                this.filters[key] = value;
            });
        }

        this.eventDispatcher.addEventListener('facetsUpdated', () => {
            let newUrl = this.buildRequestUrl(window.location.href.split('?')[0]);

            window.history.pushState({path: newUrl.href}, null, newUrl.href);
        });

        document.dispatchEvent(new Event('afterSetUrlParamsAsFilters'));
    }

    /**
     * Extract nested Url querystring parameters and render object containing all querystring parameters in given Url
     *
     * @param {string} url
     * @returns {object}
     */
    convertUrlParamsToObject(url) {
        url = url.substring(url.indexOf('?') + 1);

        let re = /([^&=]+)=?([^&]*)/g,
            decodeRE = /\+/g,
            decode = function (str) {
                return decodeURIComponent(str.replace(decodeRE, " "));
            },
            params = {},
            e;

        while (e = re.exec(url)) {
            let k = decode(e[1]), v = decode(e[2]);

            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            } else params[k] = v;
        }

        let assign = function (obj, keyPath, value) {
            let lastKeyIndex = keyPath.length - 1;

            for (let i = 0; i < lastKeyIndex; ++i) {
                let key = keyPath[i];
                if (!(key in obj))
                    obj[key] = {}
                obj = obj[key];
            }

            obj[keyPath[lastKeyIndex]] = value;
        }

        for (let prop in params) {
            let structure = prop.split('[');

            if (structure.length > 1) {
                let levels = [];

                structure.forEach(function (item, i) {
                    let key = item.replace(/[?[\]\\ ]/g, '');
                    levels.push(key);
                });

                assign(params, levels, params[prop]);
                delete(params[prop]);
            }
        }

        return params;
    }

    /**
     * Generate URL with querystring parameters based on given URL string and current filters
     *
     * @param {string} requestUrl
     * @returns {module:url.URL}
     */
    buildRequestUrl(requestUrl) {
        let url = new URL(requestUrl),
            filters = this.getFilters();

        Object.keys(filters).forEach((key) => {
            let urlParam = this.protectedKeywords.indexOf(key) !== -1 ? key : `filters[${key}]`;

            if (typeof filters[key] === 'object') {
                for (let prop in filters[key]) {
                    if (typeof filters[key][prop] !== 'object') {
                        url.searchParams.set(`${urlParam}[${prop}]`, filters[key][prop])
                    }
                }
            } else {
                url.searchParams.set(urlParam, filters[key]);
            }
        });

        return url;
    }

    /**
     * Retrieve filter by filter code (if it exists) in current instance
     *
     * @param {string} code
     * @returns {string|array|object|null}
     */
    getFilter(code) {
        if (this.filters.hasOwnProperty(code)) {
            return this.filters[code];
        }

        return null;
    }

    /**
     * Get all filters in current instance
     *
     * @returns {object}
     */
    getFilters() {
        return this.filters;
    }

    /**
     * Clear all filters applied but NOT special ones like search queries, pagination number and sort order
     *
     * @returns {void}
     */
    clearRegistry() {
        this.filtersRegistry.forEach((entry) => {
            delete this.filters[entry];
        });

        this.dispatchFacetsUpdatedEvent()
    }

    /**
     * Clear all filters applied but NOT special ones like search queries, pagination number and sort order without
     * firing a "facetsUpdated" event
     *
     * @returns {void}
     */
    clearRegistryWithoutEventDispatch() {
        this.filtersRegistry.forEach((entry) => {
            delete this.filters[entry];
        });
    }

    /**
     * Dispatch event to notify components that filters in current instance have been updated
     *
     * @returns {void}
     */
    dispatchFacetsUpdatedEvent() {
        this.eventDispatcher.dispatchEvent(new Event('facetsUpdated'));
    }

    /**
     * Dispatch event to notify components that pagination filters have been updated.
     * This allows to listen for changes in pagination without having components (like lists and grids) resetting
     * immediately the page number which is the expected behaviour when the "facetsUpdated" event is fired
     *
     * @returns {void}
     */
    dispatchPaginationUpdatedEvent() {
        this.eventDispatcher.dispatchEvent(new Event('paginationUpdated'));
    }

    /**
     * Dispatch event to update UI of pagination elements in the current context with pagination data from latest
     * AJAX response (next page and total pages count)
     */
    dispatchReloadPaginationEvent() {
        this.eventDispatcher.dispatchEvent(new Event('reloadPagination'));
    }

    /**
     * This method allows to register a filter code in a special list of filters that need to be reset when user
     * clicks on the "Clear" button in filter options sidebar (this is not needed for filters like search queries or
     * sort order
     *
     * @param {string} code
     */
    registerFilter(code) {
        this.filtersRegistry.push(code);
    }

    /**
     * Remove filter by code (if it exists) in current instance
     *
     * @param {string} code
     */
    removeFilter(code) {
        if (!code in this.filters) {
            return;
        }

        delete this.filters[code];

        this.dispatchFacetsUpdatedEvent()
    }

    /**
     * Remove filter by code (if it exists) in current instance without firing the "facetsUpdated" event
     *
     * @param {string} code
     */
    removeFilterWithoutEventDispatch(code) {
        if (!code in this.filters) {
            return;
        }

        delete this.filters[code];
    }

    /**
     * Set filter by code on given value in current instance
     *
     * @param {string} code
     * @param {string|array|object} value
     */
    setFilter(code, value) {
        this.filters[code] = value;

        this.dispatchFacetsUpdatedEvent()
    }

    /**
     * Set filter by code on given value in current instance without firing the "facetsUpdated" event
     *
     * @param {string} code
     * @param {string|array|object} value
     */
    setFilterWithoutEventDispatch(code, value) {
        this.filters[code] = value;
    }
}

window.appFacets = new AppFacets();
