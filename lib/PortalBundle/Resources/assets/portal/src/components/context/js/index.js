import {AppFacets} from "../../../scripts/facets";

class Context extends HTMLElement {
    constructor() {
        super();

        this.appFacets = new AppFacets(this);
    }
}

customElements.define('app-context', Context);