## Pagination
This component renders a pagination UI that connects automatically to listing components
(like "grid" and "list") found within the same context.

The available pages are automatically calculated every time an AJAX request is sent based 
on the JSON fields "pages" and "next_page" in the JSON response.

### HTML Sample
```
<app-pagination class="pagination">
    <div class="pagination__size-selector-container">
        <span>View max.</span>
        <select aria-label="Page size"
                class="input-select pagination__size-selector"
                data-role="size_selector">
            <option value="4">4</option>
            <option value="6">6</option>
            <option value="10">10</option>
        </select>
        <span>
            per page
        </span>
    </div>
    <div>
        <form class="pagination__page-selector-form"
              data-role="page_selector_form">
            <input data-role="page_selector"
                   type="hidden"
                   value="1"
            />
            <button class="pagination__button pagination__button--prev"
                    type="button"
                    aria-label="Previous"
                    data-role="page_prev">
                <span>Prev</span>
            </button>
            <div data-role="pages_list">
                <!-- Placeholder for pages buttons -->
            </div>
            <button class="pagination__button pagination__button--next"
                    type="button"
                    aria-label="Next"
                    data-role="page_next">
                <span>Next</span>
            </button>
        </form>
    </div>
</app-pagination>
```
### Dependencies
This component has a dependency with the following components:

- "ui-component"