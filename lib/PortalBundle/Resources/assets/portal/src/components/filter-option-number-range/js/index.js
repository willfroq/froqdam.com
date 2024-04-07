import {FilterOptionMinMax} from "../../filter-option-min-max/js";

export class FilterOptionNumberRange extends FilterOptionMinMax {
    constructor() {
        super();
    }
}

customElements.define('app-filter-option-number-range', FilterOptionNumberRange);