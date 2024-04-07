import {FilterOptionMultiSelect} from "../../filter-option-multi-select/js";

export class FilterOptionCategory extends FilterOptionMultiSelect {
    constructor() {
        super();

        this.listItemWithChildrenActiveClass = 'filter-option-category__checkboxes-list-item--children-list-expanded';
    }

    initFilters() {
        super.initFilters();

        this.querySelectorAll('.filter-option-category__checkboxes-list-item--has-children').forEach((listItemWithChildren) => {
            let hasActiveChildren = listItemWithChildren.querySelectorAll('input:checked').length;
            
            if (hasActiveChildren) {
                listItemWithChildren.classList.add(this.listItemWithChildrenActiveClass);
            }
        });

        this.querySelectorAll('[data-role="children_list_trigger"]').forEach((childrenListTrigger) => {
            childrenListTrigger.addEventListener('click', () => {
                this.toggleChildrenList(childrenListTrigger.parentElement.parentElement);
            });
        });
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        super.initEventListeners();
    }

    toggleChildrenList(listItem) {
        let method = !listItem.classList.contains(this.listItemWithChildrenActiveClass) ? 'add' : 'remove';

        listItem.classList[method](this.listItemWithChildrenActiveClass);
    }
}

customElements.define('app-filter-option-category', FilterOptionCategory);