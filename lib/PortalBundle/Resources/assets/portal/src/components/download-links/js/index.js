import {UiComponent} from "../../ui-component/js"
import copy from 'copy-to-clipboard'

class DownloadLinks extends UiComponent {
    constructor() {
        super()

        this.url = null
        this.storageName = null
        this.initListener = this.initListener.bind(this)
        this.fetchShareLink = this.fetchShareLink.bind(this)
        this.copyToClipboard = this.copyToClipboard.bind(this)
    }

    connectedCallback() {
        super.connectedCallback();

        this.render();
    }

    render () {
        this.url = this.getAttribute('data-asset-resource-url')
        this.storageName = this.getAttribute('storage-name')
        this.initListener();
    }
    initListener () {
        this.addEventListener('click', this.fetchShareLink)
    }

    fetchShareLink () {
        const assetList = this.getAssetList()

        if (!assetList){
            throw new Error('Missing parameters');
        }

        fetch(this.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(assetList)
        })
            .then(response => response.json())
            .then(data => {
                if (data?.status === 422) {
                    return data?.validationErrors
                }

                if (data?.status === 403) {
                    throw new Error('Forbidden');
                }

                this.copyToClipboard(data.publicUrl)
            })
            .catch(error => console.error(error))
    }

    getAssetList () {
        return JSON.parse(localStorage.getItem(this.storageName)) ?? null;
    }

    copyToClipboard(text) {
        copy(text, {
            debug: true,
            message: 'Press #{key} to copy',
        })

        this.notify("Link copied to clipboard", 'success')
    }

    notify (text, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        toastContainer.innerHTML = '';

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = text;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);

    }
}

customElements.define('app-download-links', DownloadLinks);
