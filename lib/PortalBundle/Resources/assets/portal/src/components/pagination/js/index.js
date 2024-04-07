import {UiComponent} from "../../ui-component/js";

export class Pagination extends UiComponent {
    constructor() {
        super();

        this.pageNextButton = null;
        this.pagePrevButton = null;
        this.pageSelector = null;
        this.pageSelectorForm = null;
        this.pagesList = null;
        this.sizeSelector = null;
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

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        this.appFacets.eventDispatcher.addEventListener('facetsUpdated', () => {
            this.pageSelector.value = 1;
            this.update();
        });

        this.appFacets.eventDispatcher.addEventListener('reloadPagination', () => {
            this.update();
        });

        this.pageSelectorForm.addEventListener('submit', (e) => {
            e.preventDefault();
        });

        this.sizeSelector.addEventListener('change', () => {
            this.appFacets.setFilter('size', this.sizeSelector.value);
        });

        this.pagePrevButton.addEventListener('click', () => {
            if (this.pageSelector.value <= 1) {
                return;
            }

            this.pageSelector.value--;

            this.appFacets.setFilterWithoutEventDispatch('page', this.pageSelector.value);
            this.appFacets.dispatchPaginationUpdatedEvent();
        });

        this.pageNextButton.addEventListener('click', () => {
            this.pageSelector.value++;

            this.appFacets.setFilterWithoutEventDispatch('page', this.pageSelector.value);
            this.appFacets.dispatchPaginationUpdatedEvent();
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.pageNextButton = this.querySelector('[data-role="page_next"]');
        this.pagePrevButton = this.querySelector('[data-role="page_prev"]');
        this.pageSelector = this.querySelector('[data-role="page_selector"]');
        this.pageSelectorForm = this.querySelector('[data-role="page_selector_form"]');
        this.sizeSelector = this.querySelector('[data-role="size_selector"]');
        this.pagesList = this.querySelector('[data-role="pages_list"]');

        this.pageSelector.value = 1;

        this.initEventListeners();
    }

    update() {
        this.pagePrevButton.disabled = this.pageSelector.value <= 1;
        this.pageNextButton.disabled = !this.appFacets.paginationData.nextPage;

        let currentPage = parseInt(this.pageSelector.value),
            pagesCount = this.appFacets.paginationData.pages,
            collapsePages = pagesCount > 5,
            pagesListHtml = '';

        pagesListHtml += `<div class="pagination__pages ${collapsePages ? 'pagination__pages--collapse-pages' : ''}">`;

        for (let page = 1; page <= this.appFacets.paginationData.pages; page++) {
            let addBreak = false,
                pageAfterActivePage = currentPage + 1,
                pageBeforeActivePage = currentPage - 1,
                buttonClasses = [
                    'pagination__pages-button'
                ];

            if (collapsePages && page === 1 && Math.abs(currentPage - page) > 1) {
                addBreak = true;
            }

            if (collapsePages && page === pageAfterActivePage && Math.abs(pagesCount - pageAfterActivePage) > 1) {
                addBreak = true;
            }

            if (page === pageBeforeActivePage) {
                buttonClasses.push('pagination__pages-button--before-active');
            }

            if (page === currentPage) {
                buttonClasses.push('pagination__pages-button--active');
            }

            if (page === pageAfterActivePage) {
                buttonClasses.push('pagination__pages-button--after-active');
            }

            pagesListHtml += '' +
                `<button class="${buttonClasses.join(' ')}" data-role="navigate_to_page" data-page-number="${page}" aria-label="${page}">` +
                    `<span>${page}</span>` +
                '</button>';

            if (collapsePages && addBreak) {
                pagesListHtml += '<div class="pagination__pages-break">...</div>';
            }
        }

        pagesListHtml += '</div>';

        this.pagesList.innerHTML = pagesListHtml;

        this.pagesList.querySelectorAll('[data-role="navigate_to_page"]').forEach((navigateToPageButton) => {
            navigateToPageButton.addEventListener('click', () => {
                this.pageSelector.value = navigateToPageButton.getAttribute('data-page-number');

                this.appFacets.setFilterWithoutEventDispatch('page', this.pageSelector.value);
                this.appFacets.dispatchPaginationUpdatedEvent();
            });
        });
    }
}

customElements.define('app-pagination', Pagination);
