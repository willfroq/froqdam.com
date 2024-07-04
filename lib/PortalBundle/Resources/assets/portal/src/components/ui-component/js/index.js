export class UiComponent extends HTMLElement {
    constructor() {
        super();

        this.appFacets = null;
        this.context = null;
    }
    
    /**
     * Callback to execute immediately after the HTML element is inserted in the DOM
     * 
     * @returns {void}
     */
    connectedCallback() {
        this.context = this.closest('app-context');
        this.appFacets = this.context ? this.context.appFacets : window.appFacets;
    }
}