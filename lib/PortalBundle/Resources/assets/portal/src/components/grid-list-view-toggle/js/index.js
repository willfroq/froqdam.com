import {UiComponent} from "../../ui-component/js";

class GridListViewToggle extends UiComponent {
    constructor() {
        super();

        this.containerId = null;
        this.activeClass = 'grid-list-view-toggle__button--active';
        this.buttons = [];
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

    render() {
        this.containerId = this.getAttribute('container-id');
        this.buttons = this.querySelectorAll('button');
        this.gridUrl = this.getAttribute('grid-url');
        this.listUrl = this.getAttribute('list-url');

        this.initEventListeners();
        this.loadContent(this.gridUrl)
            .then((jsonData) => {
                this.appFacets.removeFilterWithoutEventDispatch('page');
                this.appFacets.removeFilterWithoutEventDispatch('sort_direction');

                if (jsonData && 'html' in jsonData) {
                    document.getElementById(this.containerId).innerHTML = jsonData['html'];
                }
            });
    }

    /**
     * Initialize default event listeners on element and children
     * 
     * @returns {void}
     */
    initEventListeners() {
        this.buttons.forEach((button) => {
            button.addEventListener('click', () => {
                let buttonRole = button.getAttribute('data-role'),
                    url = this.gridUrl;

                switch(buttonRole) {
                    case 'grid_view_toggle':
                        url = this.gridUrl;
                        break;

                    case 'list_view_toggle':
                        url = this.listUrl;
                        break;
                }

                this.loadContent(url)
                    .then((jsonData) => {
                        this.appFacets.removeFilterWithoutEventDispatch('page');
                        this.appFacets.removeFilterWithoutEventDispatch('sort_direction');

                        if (jsonData && 'html' in jsonData) {
                            document.getElementById(this.containerId).innerHTML = jsonData['html'];
                        }
                    });

                this.buttons.forEach((button) => {
                    button.classList.remove(this.activeClass);
                });

                button.classList.add(this.activeClass);
            });
        });
    }

    /**
     * Load target container content asynchronously from given Url and return promise
     *
     * @param {String} url
     * @returns {Promise<Response>}
     */
    async loadContent(url) {
        let response = await fetch(url);

        return response.json();
    }
}

customElements.define('app-grid-list-view-toggle', GridListViewToggle);
