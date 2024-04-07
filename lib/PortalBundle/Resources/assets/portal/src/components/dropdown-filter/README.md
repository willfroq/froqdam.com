## Dropdown filter
This component is used to render a dropdown menu that applies a filter when clicking on the 
dropdown menu list items.

### Configuration attributes
- filter-code: this attribute specifies the filter code to use in AJAX requests when 
selecting a value in the dropdown.

### Sample HTML
```
<app-dropdown-filter class="dropdown-filter" filter-code="view">
    <div class="dropdown-filter__selector-wrapper">
        <button class="dropdown-filter__selector"
                aria-label="View"
                data-role="dropdown_filter_selector">Final artwork</button>
    </div>
    <div class="dropdown-filter__list-wrapper">
        <ul class="dropdown-filter__list" data-role="dropdown_filter_list">
            <li data-role="dropdown_filter_list_item"
                data-code="final_artwork">
                Final artwork
            </li>
            <li data-role="dropdown_filter_list_item"
                data-code="test_option">
                Test option
            </li>
        </ul>
    </div>
</app-dropdown-filter>
```

### Dependencies
This component has a dependency with the following components:

- "ui-component"