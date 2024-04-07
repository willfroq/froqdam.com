import {UiComponent} from "../../ui-component/js";

class ImageViewer extends UiComponent {
    constructor() {
        super();
    }

    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    render() {
        const image = new Image();

        image.src = this.getAttribute('src') ?? '';
        image.classList.add('asset-detail-preview__image');
        image.alt = 'Preview';

        document.dispatchEvent(new Event('showAssetPreviewLoader'));
        image.onload = () => {
            document.dispatchEvent(new Event('hideAssetPreviewLoader'));
        }

        this.append(image);
    }
}

customElements.define('app-image-viewer', ImageViewer);
