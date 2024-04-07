## Filter option - type Input
This component renders a filter option with an input field. 
The value of the filter is updated after pressing the enter key. 
All listing components (like "grid" or "list") within the same context of this component are immediately 
notified when it's value changes and send an AJAX request to retrieve updated HTML content. 

### Configuration attributes
- filter-code: this attribute specifies the filter code to use in AJAX requests when updating the value of
  a filter option.

### HTML Sample
```
<app-filter-option-input class="filter-option filter-option-input"
                         filter-code="article_number">
    <div class="filter-option__title" data-role="filter_option_title">
        <strong>Article Number</strong>
        <button class="filter-option__trigger"
                data-role="filter_option_trigger"></button>
    </div>
    <div class="filter-option__content" data-role="filter_option_content">
        <form class="filter-option__search-field-wrapper"
              data-role="filter_option_search_form">
            <input aria-label="Article Number"
                   data-role="filter_option_search_field"
                   class="input-text filter-option__search-field"
                   placeholder="Article Number"
                   type="text"
            />
        </form>
    </div>
</app-filter-option-input>
```
### Dependencies
This component has a dependency with the component "filter-option"