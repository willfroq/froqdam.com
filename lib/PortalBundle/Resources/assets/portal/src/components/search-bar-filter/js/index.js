import { UiComponent } from "../../ui-component/js";

export class SearchBarFilter extends UiComponent {
    constructor() {
        super();

        this.filterInputs = null;
        this.listItems = null;
        this.searchValue = null;
        this.viewAll = null;
    }
    
    /**
     * Callback to execute immediately after the HTML element is inserted in the DOM
     * 
     * @returns {void}
     */
    render() {
        this.filterInputs = this.querySelector("#filter-search");
        this.listItems = this.querySelectorAll("li");
        this.initEventListeners();
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.filterInputs.addEventListener("input", this.searchFilters);
    }

    /**
     * Check input with current list items and adjust where needed
     *
     * @returns {void}
     */    
    searchFilters(evt) {
        this.viewAll = this.parentElement.parentElement.getElementsByClassName("filter-option-multi-select__checkboxes-list-toggle")[0];
        this.searchValue = evt.target.value.toLowerCase();
        if(this.searchValue == ""){
            this.viewAll.style.visibility = "visible";
        } else {
            this.viewAll.style.visibility = "hidden";
        }
        this.parentElement.listItems.forEach(item => {
            this.checkboxName = item.querySelector("input").value.toLowerCase();
            this.isMatch = !! this.checkboxName.match(this.searchValue);
            this.isNoMatch = ! this.checkboxName.match(this.searchValue);
            if(this.searchValue == "") {
                this.isMatch = false;
                this.isNoMatch = false;
            }
            item.classList.toggle("multi-choice-active", this.isMatch);
            item.classList.toggle("multi-choice-inactive", this.isNoMatch);
        });
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

customElements.define("app-filter-search", SearchBarFilter);