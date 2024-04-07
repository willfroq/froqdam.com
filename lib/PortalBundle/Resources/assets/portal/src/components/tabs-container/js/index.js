import {UiComponent} from "../../ui-component/js";

class TabsContainer extends UiComponent {
    constructor() {
        super();

        this.tabViewer = null;
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

    /**
     * Create tab viewer element and load content from first tab component into it component initalization
     *
     * @returns {void}
     */
    initialize() {
        let firstTab = this.querySelector('[data-role="tab"]:first-child');

        this.tabViewer = document.createElement('div');

        this.tabViewer.classList.add('tabs-container__viewer');
        this.tabViewer.setAttribute('data-role', 'tabs_container_viewer');

        this.append(this.tabViewer);

        this.loadContent(firstTab.getAttribute('url'))
            .then((response) => {
                response.json().then((jsonData) => {
                    if ('html' in jsonData) {
                        this.tabViewer.innerHTML = jsonData['html'];
                        firstTab.dispatchEvent(new Event('tabActivated'));
                    }
                })
            });
    }

    /**
     * Initialize default event listeners on element and children
     *
     * @returns {void}
     */
    initEventListeners() {
        this.querySelectorAll('[data-role="tab"]').forEach((tab) => {
            tab.addEventListener('click', () => {
                let url = tab.getAttribute('url');

                this.loadContent(url)
                    .then(response => {
                        if (response.status === 200) {
                            return response.json();
                        } else if (response.status === 0) {
                            window.location.href = '/portal/auth/login';
                        }
                    })
                    .then(jsonData => {
                        if (jsonData && 'html' in jsonData) {
                            this.tabViewer.innerHTML = jsonData['html'];
                        }
                    })
            });
        });
    }

    /**
     * Load tab content asynchronously from given Url and return promise
     *
     * @param {String} url
     * @returns {Promise<Response>}
     */
    async loadContent(url) {
        return await fetch(url, {
            redirect: 'manual',
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.initialize();
        this.initEventListeners();
    }
}

customElements.define('app-tabs-container', TabsContainer);