import {UiComponent} from "../../ui-component/js";

class DownloadAsset extends UiComponent {
    constructor() {
        super();
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
     * Initialize component event listeners
     *
     * @returns {void}
     */
    initEventListeners() {
        const self = this;
        self.addEventListener('click', (event) => {
            event.preventDefault();
            self.downloadFile(self.getAttribute('href'));
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.initEventListeners();
    }

    async downloadFile(downloadLink) {
        const isLoggedIn = await this.isActive();
        if (isLoggedIn) {
            const link = document.createElement('a');
            link.href = downloadLink;
            link.setAttribute('download', '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    async isActive() {
        try {
            const response = await fetch('/portal/auth/is-active', {method: 'POST', redirect: 'manual'});
            if (response.status === 200) {
                return true;
            } else if (response.status === 0) {
                window.location.href = '/portal/auth/login';
            }
            return false;
        } catch (error) {
            console.error('Error:', error);
            return false;
        }
    }
}

customElements.define('app-download-asset', DownloadAsset);