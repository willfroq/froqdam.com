## UI Component

This is the base component from which any other component that is actually used to render UI
must be extended from.

This allows to define properties and methods that are shared across all UI elements in the project.

**IMPORTANT: This component does not have a matching HTML tag. 
The markup from following example will NOT create an instance of this component**

```
<app-ui-component></app-ui-component>
```