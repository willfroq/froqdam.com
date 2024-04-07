## Filter option - type category
This component extends the multi-select filter option component to allow nesting filter options.

### Configuration attributes
- filter-code: this attribute specifies the filter code to use in AJAX requests when
  the value of this filter option changes.
- checkboxes-list-expand-text: localized text for the toggle button to expand collapsed options list
- checkboxes-list-collapse-text: localized text for the toggle button to collapse the expanded options list
- url: the Url to reload HTML markup for the current filter after any filter is applied

### Sample HTML
```
<app-filter-option-category class="filter-option filter-option-category"
                            filter-code="category"
                            checkboxes-list-expand-text="View all"
                            checkboxes-list-collapse-text="View less"
                            url="https://app.froq.test/ajax_endpoints/request.php?type=load-category-checkboxes-list">
    <div class="filter-option__title"  data-role="filter_option_title">
        <strong>Category</strong>
        <button class="filter-option__trigger" data-role="filter_option_trigger"></button>
    </div>
    <div class="filter-option__content" data-role="filter_option_content">
        <form class="filter-option__search-field-wrapper"
              data-role="filter_option_search_form">
            <input aria-label="Category"
                   data-role="filter_option_search_field"
                   class="input-text filter-option__search-field"
                   placeholder="Category"
                   type="text"
            />
        </form>
        <ul class="filter-option-category__checkboxes-list"
            data-role="filter_option_checkboxes_list">
            <li class="filter-option-category__checkboxes-list-item--has-children">
                <div class="top-level-selector">
                    <input class="input-checkbox"
                           data-role="filter_option_multi_select_checkbox"
                           value="4"
                           id="filter_option_category_checkbox_4"
                           type="checkbox"
                    />
                    <label for="filter_option_category_checkbox_4">
                        <span>Kaas line</span>
                        <span>(777)</span>
                    </label>
                    <button class="children-list__trigger" data-role="children_list_trigger"></button>
                </div>
                <div class="children-list">
                    <ul>
                        <li>
                            <input class="input-checkbox"
                                   data-role="filter_option_multi_select_checkbox"
                                   value="4_0"
                                   id="filter_option_category_checkbox_4_0"
                                   type="checkbox"
                            />
                            <label for="filter_option_category_checkbox_4_0">
                                <span>Room kaas</span>
                                <span>(11)</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</app-filter-option-category>
```

### Dependencies
This component has a dependency with the following components:

- "filter-option-multi-select"