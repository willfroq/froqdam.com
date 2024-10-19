import {FilterOptionInput} from "../../filter-option-input/js";

export class FilterOptionMultiSelect extends FilterOptionInput {
    constructor() {
        super();

        this.form = null;
        this.checkboxesListActiveClass = 'filter-option-multi-select__checkboxes-list--expanded';
        this.url = null;
        this.checkboxesList = null;
        this.checkboxesListCollapseText = '';
        this.checkboxesListExpanded = false;
        this.checkboxesListExpandText = '';
        this.checkboxesListToggle = null;
    }

    initialize() {
        this.filterCode = this.getAttribute('filter-code');
        this.filterOptionTrigger = this.querySelector('[data-role="filter_option_title"]');
        this.filterOptionContent = this.querySelector('[data-role="filter_option_content"]');
        this.url = this.getAttribute('url');
        this.checkboxesList = this.querySelector('[data-role="filter_option_checkboxes_list"]');
        this.checkboxesListCollapseText = this.getAttribute('checkboxes-list-collapse-text');
        this.checkboxesListExpandText = this.getAttribute('checkboxes-list-expand-text');
        this.form = this.querySelector('[data-role="filter_option_search_form"]');
        this.searchField = this.querySelector('[data-role="filter_option_search_field"]');
        this.resetButton = document.createElement('button');
        this.resetButton.type = 'button';
        this.resetButton.classList.add('filter-option__reset-button');

        this.appFacets.registerFilter(this.filterCode);
        this.form.append(this.resetButton);
        this.initCheckboxesDropdown();
        this.initFilters();
    }

    initCheckboxesDropdown() {
        let checkboxesCount = this.checkboxesList.children.length;

        if (checkboxesCount > 4) {
            this.checkboxesListToggle = document.createElement('a');
            this.checkboxesListToggle.innerText = `${this.checkboxesListExpandText} (${checkboxesCount})`;
            this.checkboxesListToggle.classList.add('filter-option-multi-select__checkboxes-list-toggle');

            this.filterOptionContent.append(this.checkboxesListToggle);

            this.checkboxesListToggle.addEventListener('click', () => {
                this.checkboxesListExpanded = !this.checkboxesListExpanded;

                this.checkboxesListToggle.innerText = this.checkboxesListExpanded ? this.checkboxesListCollapseText : `${this.checkboxesListExpandText} (${checkboxesCount})`;
                this.checkboxesList.classList[this.checkboxesListExpanded ? 'add' : 'remove'](this.checkboxesListActiveClass);
            });
        }
    }

    initFilters() {
        let filterCheckboxes = this.querySelectorAll('[data-role="filter_option_multi_select_checkbox"]');

        filterCheckboxes.forEach((filterCheckbox) => {
            let hasActiveCheckboxes = false,
                filterValues = this.appFacets.getFilter(this.filterCode);

            if (filterValues && !Array.isArray(filterValues)) {
                filterValues = Object.values(filterValues)
            }

            if (filterValues && filterValues.length) {
                filterValues.forEach((filterValue) => {
                    if (filterCheckbox.value === filterValue) {
                        let filterCheckboxParent = filterCheckbox.parentElement;

                        filterCheckboxParent.classList.add('filter-option-multi-select__checkboxes-list-item--active');
                        filterCheckbox.checked = true;

                        hasActiveCheckboxes = true;
                    }
                });
            }

            if (hasActiveCheckboxes) {
                this.classList.add(this.activeClass);
            }

            filterCheckbox.addEventListener('click', () => {
                let currentFilter = this.appFacets.getFilter(this.filterCode);

                if (!currentFilter) {
                    currentFilter = [];
                } else if (currentFilter && !Array.isArray(currentFilter)) {
                    currentFilter = Object.values(currentFilter);
                }

                if (filterCheckbox.checked) {
                    currentFilter.push(filterCheckbox.value);
                } else {
                    currentFilter = currentFilter.filter((item) => {
                        return item !== filterCheckbox.value;
                    });
                }

                if (!currentFilter.length) {
                    this.appFacets.removeFilter(this.filterCode);
                } else {
                    this.appFacets.setFilter(this.filterCode, currentFilter);
                }
            });
        });
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        this.filterOptionTrigger.addEventListener('click', () => {
            this.toggleActiveClass();
        });

        this.form.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                this.onSearchFieldSubmit();
            }
        });

        this.searchField.addEventListener('keyup', () => {
            this.onSearchFieldChange();
        });

        this.resetButton.addEventListener('click', () => {
            this.searchField.classList.remove(this.searchFieldActiveClass);
            this.searchField.value = '';
            this.checkboxesListExpanded = false;

            this.getCheckboxesList(true).then((response) => {
                response.json().then((jsonData) => {
                    if (jsonData && 'html' in jsonData) {
                        this.checkboxesList.innerHTML = jsonData['html'];
                        this.initFilters();
                        this.initCheckboxesDropdown();
                    }
                });
            });
        });
    }

    onSearchFieldSubmit() {
        this.getCheckboxesList().then((response) => {
            response.json().then((jsonData) => {
                if (jsonData && 'html' in jsonData) {
                    this.checkboxesList.innerHTML = jsonData['html'];
                    this.initFilters();
                    this.initCheckboxesDropdown();
                }
            });
        });
    }

    async getCheckboxesList(resetList = false) {
        let response;

        this.checkboxesListToggle.remove();

        if (resetList) {
            response = await fetch(this.url);
        } else {
            let url = this.appFacets.buildRequestUrl(this.url);

            url.searchParams.set('query', this.searchField.value);

            response = await fetch(url.toString());
        }

        return response;
    }
}

customElements.define('app-filter-option-multi-select', FilterOptionMultiSelect);
