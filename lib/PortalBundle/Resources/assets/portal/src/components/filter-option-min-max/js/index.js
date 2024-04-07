import {FilterOption} from "../../filter-option/js";

export class FilterOptionMinMax extends FilterOption {
    constructor() {
        super();

        this.maxPropertyCode = 'max';
        this.minPropertyCode = 'min';
    }

    initialize() {
        super.initialize();

        this.filterCode = this.getAttribute('filter-code');

        if (this.filterCode) {
            this.appFacets.registerFilter(this.filterCode);
        }

        this.formMax = this.querySelector('[data-role="filter_option_range_max_form"]');
        this.formMin = this.querySelector('[data-role="filter_option_range_min_form"]');
        this.max = this.querySelector('[data-role="filter_option_range_max"]');
        this.min = this.querySelector('[data-role="filter_option_range_min"]');

        let filter = this.appFacets.getFilter(this.filterCode);

        if (!filter) {
            return;
        }

        let currentFilterValueMin = filter[this.minPropertyCode],
            currentFilterValueMax = filter[this.maxPropertyCode];

        if (currentFilterValueMin) {
            this.min.value = currentFilterValueMin;
        }

        if (currentFilterValueMax) {
            this.max.value = currentFilterValueMax;
        }

        if (currentFilterValueMin || currentFilterValueMax) {
            this.classList.add(this.activeClass);
        }
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        super.initEventListeners();

        this.formMax.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') {
                return;
            }

            this.onFormSubmit();
        });

        this.formMin.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') {
                return;
            }

            this.onFormSubmit();
        });

        this.min.addEventListener(
            "blur",
            this.onFormSubmit.bind(this)
        )

        this.max.addEventListener(
            "blur",
            this.onFormSubmit.bind(this)
        )
    }

    onFormSubmit(e) {
        let filterValue = {};

        if (this.min.value) {
            filterValue[this.minPropertyCode] = `${this.min.value}`;
        }

        if (this.max.value) {
            filterValue[this.maxPropertyCode] = `${this.max.value}`;

        }

        this.appFacets.setFilter(this.filterCode, filterValue);
    }
}
