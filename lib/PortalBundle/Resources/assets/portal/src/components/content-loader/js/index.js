import {UiComponent} from "../../ui-component/js";

class ContentLoader extends UiComponent {
    constructor() {
        super();

        this.url = null;
    }

    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    initEventListeners() {
        this.appFacets.eventDispatcher.addEventListener('facetsUpdated', this.loadContent.bind(this));
    }

    loadContent() {
        this.appFacets.removeFilterWithoutEventDispatch('page');

        document.dispatchEvent(new Event('showSearchResultsLoader'));

        fetch(this.appFacets.buildRequestUrl(this.url), {
            redirect: 'manual'
        })
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                } else if (response.status === 0) {
                    window.location.href = '/portal/auth/login';
                } else {
                    document.dispatchEvent(new Event('hideSearchResultsLoader'));
                }
            })
            .then(jsonData => {
                if (jsonData && 'html' in jsonData) {
                    this.innerHTML = jsonData['html'];

                    this.querySelectorAll('.main-form').forEach((formEl) => {
                        formEl.addEventListener('submit', (evt) => {
                            evt.preventDefault();
                        })
                    });

                    let newUrl = new URL(window.location.href);

                    newUrl.searchParams.delete('page');

                    window.history.pushState({path: newUrl.href}, null, newUrl.href);
                    document.dispatchEvent(new Event('hideSearchResultsLoader'));
                }
            })
            .catch(() => {
                document.dispatchEvent(new Event('hideSearchResultsLoader'));
            })
    }

    render() {
        this.url = this.getAttribute('url') ?? null;

        if (!this.url) {
            return;
        }

        this.initEventListeners();
    }
}

customElements.define('app-content-loader', ContentLoader);
