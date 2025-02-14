import {UiComponent} from "../../ui-component/js"

class DownloadAll extends UiComponent {
    constructor() {
        super()

        this.url = null
        this.storageName = null
        this.initListener = this.initListener.bind(this)
        this.downloadAll = this.downloadAll.bind(this)
    }

    connectedCallback() {
        super.connectedCallback();

        this.render();
    }

    render () {
        this.url = this.getAttribute('data-download-all-url')
        this.storageName = this.getAttribute('storage-name')
        this.initListener();
    }
    initListener () {
        this.addEventListener('click', this.downloadAll)
    }

    downloadAll() {
        const button = document.getElementById('download-all-button')

        button.href = `${this.url}/?assetResourceIds=${this.getAssetList()}`
        button.click()
    }

    getAssetList() {
        return JSON.parse(localStorage.getItem(this.storageName)) ?? null;
    }
}

customElements.define('app-download-all', DownloadAll);
