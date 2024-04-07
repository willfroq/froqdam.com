import {FilterOptionMinMax} from "../../filter-option-min-max/js";

export class FilterOptionDateRange extends FilterOptionMinMax {
    constructor() {
        super();

        this.maxPropertyCode = 'endDate';
        this.minPropertyCode = 'startDate';
    }

    initialize() {
        super.initialize();

        if (this.max?.value) {
            this.max.parentElement.setAttribute('data-value', this.getFormattedDateText(this.max.value));
        }

        if (this.min?.value) {
            this.min.parentElement.setAttribute('data-value', this.getFormattedDateText(this.min.value));
        }
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        super.initEventListeners();

        this.max.addEventListener('click', () => {
            this.max.showPicker();
        });

        this.min.addEventListener('click', () => {
            this.min.showPicker();
        });

        this.max.addEventListener('change', () => {
            this.onFormSubmit();
            this.max.parentElement.setAttribute('data-value', this.max.value);
        });

        this.min.addEventListener('change', () => {
            this.onFormSubmit();
            this.min.parentElement.setAttribute('data-value', this.min.value);
        });
    }

    getFormattedDateText(value) {
        let date = new Date(value),
            mappings = {
                days: {
                    0: 'Sun',
                    1: 'Mon',
                    2: 'Tue',
                    3: 'Wed',
                    4: 'Thu',
                    5: 'Fri',
                    6: 'Sat'
                },
                months: {
                    0: 'Jan',
                    1: 'Feb',
                    2: 'Mar',
                    3: 'Apr',
                    4: 'May',
                    5: 'Jun',
                    6: 'Jul',
                    7: 'Aug',
                    8: 'Sep',
                    9: 'Oct',
                    10: 'Nov',
                    11: 'Dec'
                }
            };

        return `${mappings['days'][date.getDay()]}, ${mappings['months'][date.getMonth()]} ${date.getDate()}`;
    }
}

customElements.define('app-filter-option-date-range', FilterOptionDateRange);
