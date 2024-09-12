import {UiComponent} from "../../ui-component/js";

class ItemsSorter extends UiComponent {
    constructor() {
        super();

        this.activeClass = 'sort-button__active';
        this.sortButtonSelector = null;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.sortButtonSelector = this.querySelector('[data-role="sort_button_selector"]');

        this.preselectItem();
        this.initEventListeners();
    }

    /**
     * Set sort dropdown value based on currently applied filter
     *
     * @returns {void}
     */
    preselectItem() {
        let currentFilter = this.appFacets.getFilter('sort_by');

        if (currentFilter) {
            let direction = this.appFacets.getFilter('sort_direction'),
                currentListItemSelector = `[data-role="sort_button_list_item"][data-code="${currentFilter}"]`;

            if (direction) {
                currentListItemSelector += `[data-sorting-direction="${direction}"]`
            }

            let currentListItem = this.querySelector(currentListItemSelector);

            this.sortButtonSelector.innerText = currentListItem ? currentListItem.textContent.trim() : '';
        }
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        document.addEventListener('afterSetUrlParamsAsFilters', () => {
            this.preselectItem();
        });

        this.appFacets.eventDispatcher.addEventListener('facetsUpdated', () => {
            this.preselectItem();
        });

        document.body.addEventListener('click', (e) => {
            if (this.classList.contains(this.activeClass) && e.target.matches('*:not(.sort-button):not(.sort-button *)')) {
                this.classList.remove(this.activeClass);
            }
        });

        if(this.sortButtonSelector){
            this.sortButtonSelector?.addEventListener('click', () => {
                this.toggleActiveClass();
            });
        }

        this.querySelectorAll('[data-role="sort_button_list_item"]').forEach((listItem) => {
            listItem.addEventListener('click', () => {
                this.classList.remove(this.activeClass);
                this.sortButtonSelector.innerText = listItem.innerText.trim();

                this.appFacets.removeFilterWithoutEventDispatch('sort_direction');

                if (listItem.getAttribute('data-sorting-direction')) {
                    this.appFacets.setFilterWithoutEventDispatch(
                        'sort_direction',
                        listItem.getAttribute('data-sorting-direction')
                    );
                }

                this.appFacets.setFilter('sort_by', listItem.getAttribute('data-code'));
            });
        });
    }

    /**
     * Add or remove active class based on current state
     *
     * @returns {void}
     */
    toggleActiveClass() {
        let method = !this.classList.contains(this.activeClass) ? 'add' : 'remove';

        this.classList[method](this.activeClass);
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

customElements.define('app-sort-button', ItemsSorter);
