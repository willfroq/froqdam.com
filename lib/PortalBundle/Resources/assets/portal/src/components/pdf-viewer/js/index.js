import {UiComponent} from "../../ui-component/js";

class PdfViewer extends UiComponent {
    constructor() {
        super();

        this.activeFullscreenClass = 'pdf-viewer__fullscreen--active';
        this.activeZoomControlClass = 'pdf-viewer__zoom-control--active';
        this.activeZoomDropdownClass = 'pdf-viewer__zoom-controls-wrapper--expanded';
        this.innerWrapper = document.createElement('div');
        this.initialPage = 1;
        this.initialZoomLevel = 0.75;
        this.currentPage = this.initialPage;
        this.currentZoomLevel = this.initialZoomLevel;
        this.url = null;
        this.pdfCanvas = null;
        this.pdfCanvasContext = null;
        this.pdfData = null;
        this.pdfToolbar = null;
        this.zoomIndicator = null;
        this.zoomControlsWrapper = null;
        this.zoomLevels = [
            {
                label: '50%',
                value: 0.5
            },
            {
                label: '75%',
                value: 0.75
            },
            {
                label: '100%',
                value: 1
            },
            {
                label: '110%',
                value: 1.1
            },
            {
                label: '125%',
                value: 1.25
            },
            {
                label: '150%',
                value: 1.5
            },
            {
                label: '175%',
                value: 1.75
            },
            {
                label: '200%',
                value: 2
            },
            {
                label: '250%',
                value: 2.5
            },
            {
                label: '300%',
                value: 3
            },
            {
                label: '400%',
                value: 4
            },
            {
                label: '500%',
                value: 5
            },
        ];

        this.innerWrapper.classList.add('pdf-viewer__inner-wrapper');
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
     * Render first PDF document page and PDF toolbar on page load
     *
     * @returns {void}
     */
    initialize() {
        this.pdfCanvas = document.createElement('canvas');
        this.pdfCanvasWrapper = document.createElement('div');
        this.pdfCanvasContext = this.pdfCanvas.getContext('2d');

        this.pdfCanvasWrapper.classList.add('pdf-viewer__canvas-wrapper');
        this.pdfCanvasWrapper.append(this.pdfCanvas);
        this.append(this.innerWrapper);
        this.innerWrapper.append(this.pdfCanvasWrapper);

        document.dispatchEvent(new Event('showAssetPreviewLoader'));
        pdfjsLib.getDocument(this.url).promise
            .then((pdfData) => {
                this.pdfData = pdfData;

                this.createToolbar();
                this.renderPage(this.initialPage);
                this.initEventListeners();
                document.dispatchEvent(new Event('hideAssetPreviewLoader'));
            })
            .catch(() => {
                document.dispatchEvent(new Event('hideAssetPreviewLoader'));
            })
    }

    /**
     * Render PDF toolbar HTML and append it to main element container
     *
     * @returns {void}
     */
    createToolbar() {
        this.pdfToolbar = document.createElement('div');

        let toolbarHtml = '',
            currentZoomLevelText = this.getCurrentZoomLevelText();

        if (this.pdfData.numPages > 1) {
            toolbarHtml += '<div class="pdf-viewer__page-controls-wrapper">';
            toolbarHtml += '<button data-role="pdf_viewer_prev_page_button"><i>&lsaquo;</i></button>';
            toolbarHtml += `<span data-role="pdf_viewer_page_indicator">1</span>&nbsp;/&nbsp;<span data-role="pdf_viewer_pages_count">${this.pdfData.numPages}</span>`;
            toolbarHtml += '<button data-role="pdf_viewer_next_page_button"><i>&rsaquo;</i></button>';
            toolbarHtml += '</div>';
        }

        toolbarHtml += '<div class="pdf-viewer__zoom-controls-wrapper" data-role="pdf_viewer_zoom_controls_wrapper">';
        toolbarHtml += '<button data-role="pdf_viewer_decrease_zoom_button"></button>';
        toolbarHtml += `<button data-role="pdf_viewer_zoom_indicator"><span>${currentZoomLevelText}</span></button>`;
        toolbarHtml += '<div class="pdf-viewer__zoom-controls-list">';

        this.zoomLevels.forEach((zoomLevel) => {
            toolbarHtml += `<button class="pdf-viewer__zoom-control ${zoomLevel.value === this.currentZoomLevel ? this.activeZoomControlClass : ''}" data-role="pdf_viewer_zoom_button" data-zoom-level="${zoomLevel.value}"><span>${zoomLevel.label}</span></button>`;
        });

        toolbarHtml += '</div>';
        toolbarHtml += '<button data-role="pdf_viewer_increase_zoom_button"></button>';
        toolbarHtml += '</div>';
        toolbarHtml += '<div class="pdf-viewer__fullscreen-button-wrapper"><button class="pdf-viewer__fullscreen-button" data-role="pdf_viewer_fullscreen_button"></button></div>'

        this.pdfToolbar.classList.add('pdf-viewer__toolbar');
        this.pdfToolbar.innerHTML = toolbarHtml;
        this.zoomIndicator = this.pdfToolbar.querySelector('[data-role="pdf_viewer_zoom_indicator"]');
        this.zoomControlsWrapper = this.pdfToolbar.querySelector('[data-role="pdf_viewer_zoom_controls_wrapper"]');

        this.innerWrapper.append(this.pdfToolbar);
    }

    /**
     * Retrieve label for current zoom level value from configuration
     *
     * @returns {string}
     */
    getCurrentZoomLevelText() {
        let matches = this.zoomLevels.filter((zoomLevel) => {
            return parseFloat(zoomLevel.value) === parseFloat(this.currentZoomLevel);
        });

        return matches.length ? matches[0].label : '';
    }

    /**
     * Initialize component event listeners
     *
     * @returns {void}
     */
    initEventListeners() {
        this.initZoomEventListeners();
        this.initFullscreenEventListeners();

        if (this.pdfData.numPages > 1) {
            this.initPagesEventListeners();
        }
    }

    /**
     * Initialize event listeners specifically for zoom functionality
     *
     * @returns {void}
     */
    initZoomEventListeners() {
        let zoomButtons = this.pdfToolbar.querySelectorAll('[data-role="pdf_viewer_zoom_button"]');

        this.zoomIndicator.addEventListener('click', () => {
            this.toggleActiveZoomDropdownClass();
        });

        zoomButtons.forEach((zoomButton) => {
            zoomButton.addEventListener('click', () => {
                this.currentZoomLevel = zoomButton.getAttribute('data-zoom-level');
                this.renderPage(this.currentPage);
                this.updateZoomSelector();
                this.toggleActiveZoomDropdownClass('remove');
            });
        });

        this.querySelector('[data-role="pdf_viewer_decrease_zoom_button"]').addEventListener('click', () => {
            let prevZoomLevel;

            for (let i = (this.zoomLevels.length - 1); i > 0; i--) {
                if (parseFloat(this.zoomLevels[i].value) === parseFloat(this.currentZoomLevel)) {
                    prevZoomLevel = i - 1;
                    break;
                }
            }

            if (prevZoomLevel !== null && prevZoomLevel >= 0) {
                this.currentZoomLevel = this.zoomLevels[prevZoomLevel].value;
                this.renderPage(this.currentPage);
                this.updateZoomSelector();
            }
        });

        this.querySelector('[data-role="pdf_viewer_increase_zoom_button"]').addEventListener('click', () => {
            let nextZoomLevel;

            for (let i = 0; i < this.zoomLevels.length; i++) {
                if (parseFloat(this.zoomLevels[i].value) === parseFloat(this.currentZoomLevel)) {
                    nextZoomLevel = i + 1;
                    break;
                }
            }

            if (nextZoomLevel !== null && nextZoomLevel < this.zoomLevels.length) {
                this.currentZoomLevel = this.zoomLevels[nextZoomLevel].value;
                this.renderPage(this.currentPage);
                this.updateZoomSelector();
            }
        });
    }

    /**
     * Retrieve correct method to exit full-screen mode for current browser
     *
     * @returns {null|string}
     */
    getExitFullscreenAPI() {
        if (document.exitFullscreen) {
            return 'exitFullscreen';
        } else if (document.webkitExitFullscreen) {
            return 'webkitExitFullscreen';
        }

        return null;
    }

    /**
     * Retrieve correct method to enter full-screen mode for current browser
     *
     * @returns {null|string}
     */
    getFullscreenAPI() {
        if (this.innerWrapper.requestFullscreen) {
            return 'requestFullscreen';
        } else if (this.webkitRequestFullscreen) { /* Safari */
            return 'webkitRequestFullscreen';
        }

        return null;
    }

    /**
     * Retrieve active full-screen element for current browser
     *
     * @returns {HTMLElement}
     */
    getCurrentFullscreenElement() {
        if (this.innerWrapper.requestFullscreen) {
            return document.fullscreenElement;
        } else if (this.webkitRequestFullscreen) {
            return document.webkitCurrentFullScreenElement;
        }
    }

    /**
     * Initialize event listeners specifically for fullscreen switch functionality
     *
     * @returns {void}
     */
    initFullscreenEventListeners() {
        let fullscreenAPI = this.getFullscreenAPI(),
            exitFullscreenAPI = this.getExitFullscreenAPI();

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_fullscreen_button"]').addEventListener('click', () => {
            if (!this.getCurrentFullscreenElement()) {
                document.body.classList.add(this.activeFullscreenClass);

                this[fullscreenAPI]().then(() => {
                    this.currentZoomLevel = this.initialZoomLevel;
                    this.renderPage(this.currentPage);
                    this.updateZoomSelector();
                    this.toggleActiveZoomDropdownClass('remove');
                });
            } else {
                document.body.classList.remove(this.activeFullscreenClass);

                document[exitFullscreenAPI]().then(() => {
                    this.currentZoomLevel = this.initialZoomLevel;
                    this.renderPage(this.currentPage);
                    this.updateZoomSelector();
                    this.toggleActiveZoomDropdownClass('remove');
                });
            }
        });
    }

    /**
     * Initialize event listeners specifically for pages navigation functionality
     *
     * @returns {void}
     */
    initPagesEventListeners() {
        this.querySelector('[data-role="pdf_viewer_prev_page_button"]').addEventListener('click', () => {
            let prevPage = this.currentPage - 1;

            if (prevPage < 1) {
                return;
            }

            this.currentPage = prevPage;
            this.renderPage(prevPage, this.currentZoomLevel);
            this.querySelector('[data-role="pdf_viewer_page_indicator"]').innerText = this.currentPage;
            this.pdfCanvas.parentElement.scrollTop = 0;
        });

        this.querySelector('[data-role="pdf_viewer_next_page_button"]').addEventListener('click', () => {
            let nextPage = this.currentPage + 1;

            if (nextPage > this.pdfData.numPages) {
                return;
            }

            this.currentPage = nextPage;
            this.renderPage(this.currentPage, this.currentZoomLevel);
            this.querySelector('[data-role="pdf_viewer_page_indicator"]').innerText = this.currentPage;
            this.pdfCanvas.parentElement.scrollTop = 0;
        });
    }

    /**
     * Set active state on zoom button that matches current zoom level
     *
     * @returns {void}
     */
    updateZoomSelector() {
        let zoomButtons = this.pdfToolbar.querySelectorAll('[data-role="pdf_viewer_zoom_button"]');

        zoomButtons.forEach((item) => {
            item.classList.remove(this.activeZoomControlClass);
        });

        this.pdfToolbar
            .querySelector(`[data-role="pdf_viewer_zoom_button"][data-zoom-level="${this.currentZoomLevel}"]`)
            .classList
            .add(this.activeZoomControlClass);

        this.zoomIndicator.innerText = this.getCurrentZoomLevelText();
    }

    /**
     * Initialize PDF rendering if PDFJS library is loaded
     *
     * @returns {void}
     */
    render() {
        this.url = this.getAttribute('url');

        if ('pdfjsLib' in window) {
            this.initialize();
        }
    }

    /**
     * Render given PDF page number based on given scale size (if provided) or current zoom level value
     *
     * @param {int} pageNumber
     * @param {float} scale
     * @returns {void}
     */
    renderPage(pageNumber, scale = null) {
        if (!scale) {
            scale = this.currentZoomLevel;
        }

        this.pdfData.getPage(pageNumber).then((page) => {
            let outputScale = 1,
                viewport = page.getViewport({
                    scale: scale
                });

            this.pdfCanvas.width = Math.floor(viewport.width * outputScale);
            this.pdfCanvas.height = Math.floor(viewport.height * outputScale);
            this.pdfCanvas.style.width = Math.floor(viewport.width) + 'px';
            this.pdfCanvas.style.height = Math.floor(viewport.height) + 'px';

            let renderingContext = {
                canvasContext: this.pdfCanvasContext,
                viewport: viewport
            }

            page.render(renderingContext);
        });
    }

    /**
     * Add or remove active dropdown class for zoom selector based on current state
     *
     * @param {string|null} method
     * @returns {void}
     */
    toggleActiveZoomDropdownClass(method = null) {
        if (!method) {
            method = !this.zoomControlsWrapper.classList.contains(this.activeZoomDropdownClass) ? 'add' : 'remove';
        }

        this.zoomControlsWrapper.classList[method](this.activeZoomDropdownClass);
    }
}

customElements.define('app-pdf-viewer', PdfViewer);
