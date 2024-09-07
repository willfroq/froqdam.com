import { UiComponent } from "../../ui-component/js";
import {debounce} from "lodash"

class SearchBar extends UiComponent {
    constructor() {
        super();

        this.activeClass = "search-active";
        this.searchForm = null;
        this.searchInput = null;
        this.currentSearchQuery = "";
        this.resetButton = null;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.searchForm = this.querySelector('[data-role="search_form"]');
        this.searchInput = this.querySelector(
            'input[data-role="search_input"]'
        );
        this.resetButton = document.createElement("button");
        this.resetButton.classList.add("search-bar__reset-button");
        this.resetButton.type = "button";

        // Check if there are search parameters in the URL
        const urlSearchParams = new URLSearchParams(window.location.search);
        const queryParam = urlSearchParams.get("query");

        if (queryParam) {
            this.searchInput.value = queryParam;
            this.currentSearchQuery = this.searchInput.value;
            this.classList.add(this.activeClass);
        }

        // Move the button next to the input
        this.searchForm.parentNode.insertBefore(
            this.resetButton,
            this.searchForm.nextSibling
        );

        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.searchForm.addEventListener("keydown", debounce((e) => {
            if (e.key === "Enter") {
                if (
                    !this.searchInput.value &&
                    this.appFacets.getFilter("query")
                ) {
                    this.appFacets.removeFilter("query");
                    return;
                }

                if (this.searchInput.value.length < 3) {
                    return;
                }

                this.triggerLoadResults()
            }
        }, 500));

        this.searchInput.addEventListener(
            "blur",
            this.handleBlur.bind(this)
        );
        this.searchInput.addEventListener(
            "keyup",
            this.toggleActiveClass.bind(this)
        );
        this.searchInput.addEventListener(
            "change",
            this.toggleActiveClass.bind(this)
        );
        this.resetButton.addEventListener(
            "click",
            this.resetToolbar.bind(this)
        );
    }

    /**
     * Handle blur event to conditionally trigger the load results
     *
     * @returns {void}
     */
    handleBlur() {
        if (this.searchInput.value.trim() !== "") {
            this.triggerLoadResults();
        }
    }

    /**
     * Remove any search query applied and reset UI to initial state
     *
     * @returns {void}
     */
    resetToolbar() {
        this.searchInput.value = "";
        this.currentSearchQuery = "";
        this.classList.remove(this.activeClass);
        this.appFacets.removeFilter("query");
    }

    /**
     * Apply query filter to trigger UI update (components like grid and list by default update their results every
     * time a new filter is applied)
     *
     * @returns {void}
     */
    triggerLoadResults() {
        this.appFacets.setFilter("query", this.currentSearchQuery);
    }

    /**
     * Add or remove active class based on current state
     *
     * @returns {void}
     */
    toggleActiveClass() {
        if (this.searchInput.value === this.currentSearchQuery) {
            return;
        }

        let method = this.searchInput.value ? "add" : "remove";

        this.classList[method](this.activeClass);
        this.currentSearchQuery = this.searchInput.value;
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
}

customElements.define("app-search-bar", SearchBar);
