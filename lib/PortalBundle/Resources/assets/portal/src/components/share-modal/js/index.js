import {UiComponent} from "../../ui-component/js";

export class ShareModal extends UiComponent {
    constructor() {
        super();
        this.storageName = null
        this.renderList = this.renderList.bind(this)
    }

    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    render () {
        this.storageName = this.getAttribute('storage-name')
        this.container = document.getElementById('asset-list-container')
        this.url = this.getAttribute('data-selected-assets-url')
        this.list = this.getAssetList();

        this.initEventListeners();
        this.renderList();
    }

    initEventListeners () {
        this.addEventListener('updateShareList', (event) => {
            this.updateList(event.detail.newList).then(r => r);
        });

        this.addEventListener('onOpenShareModal', async (event) => {
            await this.updateList(this.getAssetList());
        });
    }

    renderList () {
        this.container.innerHTML = '';

        if(!this.list || this.list.length === 0) {
            return
        }

        const template = document.getElementById('element-template').innerHTML;

        this.list.forEach(item => {
            const filename = item.filename === undefined ? 'Loading...' : item.filename

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = template;
            tempDiv.innerHTML = tempDiv.innerHTML.replace(/{filename}/g, filename);
            tempDiv.innerHTML = tempDiv.innerHTML.replace(/{id}/g, item.id);

            const imgContainer = tempDiv.querySelector('.img-container-thumbnail');
            if (imgContainer && item.thumbnail) {
                imgContainer.appendChild(this.createImageElement(item.thumbnail));
            }

            if (imgContainer && !item.thumbnail) {
                imgContainer.appendChild(this.emptyElement());
            }

            this.container.insertAdjacentHTML('beforeend', tempDiv.innerHTML);
        });
    }

    emptyElement () {
        const emptyElement = document.createElement('div');
        emptyElement.className = 'bg-white h-full w-full flex items-center justify-center';

        return emptyElement
    }

    createImageElement (thumbnail) {
        const imgElement = document.createElement('img');
        imgElement.className = 'image';
        imgElement.src = thumbnail;
        imgElement.alt = '';

        return imgElement
    }


    getAssetList () {
        return JSON.parse(localStorage.getItem(this.storageName)) ?? null;
    }

    async updateList(newList) {
        await this.getAssetsDetail(newList)
        this.renderList();
    }

    async getAssetsDetail(newList) {
        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(newList)
            });

            this.list = await response.json();
        } catch (err) {
            console.log(err, 'error');
        }
    }

}

customElements.define('app-share-modal', ShareModal);