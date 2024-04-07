import {FilterOption} from "../../filter-option/js";

export class FilterOptionInput extends FilterOption {
    constructor() {
        super();

        this.form = null;
        this.searchField = null;
        this.searchFieldActiveClass = 'filter-option__search-field--active';
        this.resetButton = null;
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        super.initEventListeners();

        this.searchField.addEventListener('keyup', () => {
            this.onSearchFieldChange();
        });

        this.form.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') {
                return;
            }

            this.onSearchFieldSubmit();
        });

        this.searchField.addEventListener(
            "blur",
            this.onSearchFieldSubmit.bind(this)
        )

        this.resetButton.addEventListener('click', () => {
            this.searchField.classList.remove(this.searchFieldActiveClass);
            this.searchField.value = '';

            this.appFacets.removeFilter(this.filterCode);
        });
    }

    initialize() {
        super.initialize();

        this.form = this.querySelector('[data-role="filter_option_search_form"]');
        this.searchField = this.querySelector('[data-role="filter_option_search_field"]');
        this.resetButton = document.createElement('button');
        this.resetButton.type = 'button';
        this.resetButton.classList.add('filter-option__reset-button');

        this.form.append(this.resetButton);

        if (this.appFacets.getFilter(this.filterCode)) {
            this.searchField.value = this.appFacets.getFilter(this.filterCode);

            this.classList.add(this.activeClass);
            this.searchField.classList.add(this.searchFieldActiveClass);
        }
    }

    onSearchFieldChange() {
        let method = this.searchField.value ? 'add' : 'remove';

        this.searchField.classList[method](this.searchFieldActiveClass);
    }

    onSearchFieldSubmit() {
        if (this.searchField.value) {
            this.appFacets.setFilter(this.filterCode, this.searchField.value);
        } else {
            this.appFacets.removeFilter(this.filterCode);
        }
    }
}

customElements.define('app-filter-option-input', FilterOptionInput);
