## Filter option min max
This component is used as a base for filter options that have a minimum and maximum range. 
It adds some basic CSS and JS that is common to filter options derived from it.
It is never used directly in HTML, but only to be extended into more specific components.

### Configuration attributes
- filter-code-max: this attribute specifies the filter code to use in AJAX requests when updating the 
maximum value of a filter option.
- filter-code-min: this attribute specifies the filter code to use in AJAX requests when updating the 
minimum value of a filter option.

### Dependencies
This component has a dependency with the following components:

- "ui-component"