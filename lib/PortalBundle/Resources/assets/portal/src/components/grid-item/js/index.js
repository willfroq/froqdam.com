import {UiComponent} from "../../ui-component/js";

class GridItem extends UiComponent {
    constructor() {
        super();

        this.linkUrl = null;
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
     * Trigger page redirect when clicking on current grid item
     *
     * @returns {void}
     */
    initEventListeners() {
        this.addEventListener('click', () => {
            window.location.href = this.linkUrl;
        });
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this.linkUrl = this.getAttribute('link-url');

        this.titleElements = this.getElementsByClassName('truncate-text-title')

        Array.from(this.titleElements).forEach(element => this.truncateText(element))

        setTimeout(this.hasThumbnail.bind(this), 5000);
    }

    /**
     * @param {HTMLElement} textElement
     * @param {number} maxLength
     *
     * @returns {void}
     */
    truncateText(textElement, maxLength = 37) {
        let text = textElement.textContent.trim();

        if (text.length > maxLength) {
            const half = Math.floor((maxLength - 3) / 2);

            textElement.textContent = text.slice(0, half) + '...' + text.slice(text.length - half)
        }
    }

    /**
     * @returns {void}
     */
    hasThumbnail() {
        const imageContainersElements = this.getElementsByClassName('image')

        Array.from(imageContainersElements).forEach(imageContainer => {
            const image = imageContainer.firstElementChild

            if (!image?.naturalWidth) {
                const replacementElement = document.createElement('div')

                replacementElement.classList.add('thumbnail-placeholder')

                imageContainer?.replaceChild(replacementElement, image);
            }
        })
    }
}

customElements.define('app-grid-item', GridItem);