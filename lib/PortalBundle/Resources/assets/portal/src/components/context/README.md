## Context component

This component has no aesthetic value. It is used to allow different filterable sections 
of the UI (like grids and lists) to have separate filters applied.

**Wrapping a context component around any UI component will have that UI component affecting only 
filterable components (like lists and grids) within the same context component.**

```
<app-search-bar></app-search-bar>
<app-list></app-list>
<app-list></app-list>
<app-context>
    <app-search-bar></app-search-bar>
    <app-list id="app_list_3"></app-list>
</app-context>
```

In the example above we have two search bars, the first search bar will affect results in both
the lists that follow it.

The search bar in the app-context tag will only affect the list next to it.

### Dependencies

This component has a dependency with the global script facets.js