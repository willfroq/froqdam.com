import {UiComponent} from "../../ui-component/js";

class AssetInformation extends UiComponent {
    constructor() {
        super();

        this.defaultHideText = 'Hide other';
        this.defaultShowText = 'View all';
        this.sectionCollapsedClass = 'asset-information__section--collapsed';
        this.sectionHasMultipleEntriesClass = 'asset-information__section--has-multiple-entries';
        this.sectionShowAllClass = 'asset-information__section--show-all';
        this.toggleLinkClass = 'asset-information__section-toggle-link';
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
        window.addEventListener('resize', () => {
            this.setAssetInformationMaxHeight();
        });

        document.addEventListener('keydown', this.handleEscKeyPress.bind(this));
    }

    /**
     * Handle "ESC" key press to navigate back to previous page
     *
     * @param {KeyboardEvent} event
     * @returns {void}
     */
    handleEscKeyPress(event) {
        if (event.key === 'Escape' || event.key === 'Esc' || event.key === '27') {
            event.preventDefault();
            history.go(-1);
        }
    }

    /**
     * Create HTML link to toggle visibility of all entries for given section and initialize event listener on it
     *
     * @param {HTMLElement} section
     * @returns {HTMLAnchorElement}
     */
    createToggleLink(section) {
        let toggleLink = document.createElement('a'),
            hideText = section.getAttribute('data-hide-text') ?? this.defaultHideText,
            showText = section.getAttribute('data-show-text') ?? this.defaultShowText;

        toggleLink.href = 'Javascript:void(0)';
        toggleLink.innerText = showText;

        toggleLink
            .classList
            .add(this.toggleLinkClass);

        toggleLink
            .addEventListener('click', () => {
                let method = section.classList.contains(this.sectionShowAllClass) ? 'remove' : 'add';

                section.classList[method](this.sectionShowAllClass);
                toggleLink.innerText = method === 'add' ? hideText : showText;
            });

        return toggleLink;
    }

    /**
     * Callback to execute immediately after the HTML element is rendered
     *
     * @returns {void}
     */
    render() {
        this
            .querySelectorAll('[data-role="asset_information_section"]')
            .forEach((section) => {
                let hasMultipleEntries = section.querySelectorAll('dl').length > 1;

                section
                    .querySelector('[data-role="asset_information_section_title"]')
                    .addEventListener('click', () => {
                        let method = !section.classList.contains(this.sectionCollapsedClass) ? 'add' : 'remove';

                        section.classList[method](this.sectionCollapsedClass);
                    });

                if (hasMultipleEntries) {
                    section
                        .classList
                        .add(this.sectionHasMultipleEntriesClass);

                    section
                        .querySelector('[data-role="asset_information_section_content"]')
                        .append(this.createToggleLink(section));
                }
            });

        this.setAssetInformationMaxHeight();
        this.initEventListeners();

        this.appendCloseBtn()
    }

    /**
     * Set global CSS variable to force maximum height of asset information components (if present) to be the same
     * of PDF viewer
     *
     * @returns {void}
     */
    setAssetInformationMaxHeight() {
        if (!window.CSS) {
            return;
        }

        if (window.CSS && CSS.supports('max-height', 'var(--asset-information-max-height)')) {
            document.querySelector(':root').style.setProperty(
                '--asset-information-max-height',
                window.getComputedStyle(document.querySelector('.asset-detail-preview')).height
            );
        } else {
            document
                .querySelectorAll('.asset-information')
                .forEach((assetInformationSidebar) => {
                    assetInformationSidebar.style.maxHeight = window.getComputedStyle(this).height
                });
        }
    }

    appendCloseBtn() {

        if (!document.referrer || !window.location.host) {
            return;
        }

        if (!document.referrer.includes(window.location.host)) {
            return;
        }

        let titleEl = document.getElementById('detail-title');

        if (!titleEl) {
            return;
        }

        let closeBtnLinkEl = document.createElement("a");
        closeBtnLinkEl.href = "#" ;
        closeBtnLinkEl.onclick = () => history.go(-1);

        let closeBtnIconEl = document.createElement('span')
        closeBtnIconEl.classList.add('edit-popup__close-popup-button')

        closeBtnLinkEl.append(closeBtnIconEl);

        titleEl.prepend(closeBtnLinkEl)
    }
}

customElements.define('app-asset-information', AssetInformation);
