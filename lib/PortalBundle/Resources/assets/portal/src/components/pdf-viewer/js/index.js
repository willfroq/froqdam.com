import { UiComponent } from "../../ui-component/js";

class PdfViewer extends UiComponent {
    constructor() {
        super();

        this.activeFullscreenClass = 'pdf-viewer__fullscreen--active';
        this.activeZoomControlClass = 'pdf-viewer__zoom-control--active';
        this.activeZoomDropdownClass = 'pdf-viewer__zoom-controls-wrapper--expanded';
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
        this.isPanning = false;
        this.startX = 0;
        this.startY = 0;
        this.scrollLeft = 0;
        this.scrollTop = 0;
        this.zoomLevels = [
            { label: '50%', value: 0.5 },
            { label: '75%', value: 0.75 },
            { label: '100%', value: 1 },
            { label: '125%', value: 1.25 },
            { label: '150%', value: 1.5 },
            { label: '175%', value: 1.75 },
            { label: '200%', value: 2 },
            { label: '225%', value: 2.25 },
            { label: '250%', value: 2.5 },
            { label: '275%', value: 2.75 },
            { label: '300%', value: 3 },
            { label: '325%', value: 3.25 },
            { label: '350%', value: 3.5 },
            { label: '375%', value: 3.75 },
            { label: '400%', value: 4 },
            { label: '425%', value: 4.25 },
            { label: '450%', value: 4.5 },
            { label: '475%', value: 4.75 },
            { label: '500%', value: 5 },
        ];

        this.innerWrapper = document.createElement('div');
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
        this.appendElements();
        pdfjsLib.getDocument(this.url).promise
            .then((pdfData) => {
                this.pdfData = pdfData;
                this.createToolbar();
                this.renderPage(this.initialPage);
                this.initEventListeners();
                this.hideLoader();
            }).catch(() => {
            this.hideLoader();
        })
    }

    appendElements() {
        this.pdfCanvas = document.createElement('canvas');
        this.pdfCanvasWrapper = document.createElement('div');
        this.pdfCanvasContext = this.pdfCanvas.getContext('2d');
        this.pdfCanvasWrapper.classList.add('pdf-viewer__canvas-wrapper');
        this.pdfCanvasWrapper.append(this.pdfCanvas);
        this.append(this.innerWrapper);
        this.innerWrapper.append(this.pdfCanvasWrapper);
        document.dispatchEvent(new Event('showAssetPreviewLoader'));
    }

    /**
     * Render PDF toolbar HTML and append it to main element container
     *
     * @returns {void}
     */
    createToolbar() {
        this.pdfToolbar = this.createElement('div', 'pdf-viewer__toolbar', this.generateToolbarHtml());
        this.zoomIndicator = this.pdfToolbar.querySelector('[data-role="pdf_viewer_zoom_indicator"]');
        this.zoomControlsWrapper = this.pdfToolbar.querySelector('[data-role="pdf_viewer_zoom_controls_wrapper"]');
        this.innerWrapper.append(this.pdfToolbar);
    }

    generateToolbarHtml() {
        const pageControlsHtml = this.pdfData.numPages > 1
            ? `<div class="pdf-viewer__page-controls-wrapper">
                 <button data-role="pdf_viewer_prev_page_button"><i>&lsaquo;</i></button>
                 <span data-role="pdf_viewer_page_indicator">1</span>&nbsp;/&nbsp;<span data-role="pdf_viewer_pages_count">${this.pdfData.numPages}</span>
                 <button data-role="pdf_viewer_next_page_button"><i>&rsaquo;</i></button>
               </div>`
            : '';

        const zoomControlsHtml = this.zoomLevels.map(zoomLevel => `
            <button class="pdf-viewer__zoom-control ${zoomLevel.value === this.currentZoomLevel ? this.activeZoomControlClass : ''}" data-role="pdf_viewer_zoom_button" data-zoom-level="${zoomLevel.value}">
                <span>${zoomLevel.label}</span>
            </button>
        `).join('');

        return `${pageControlsHtml}
                <div class="pdf-viewer__zoom-controls-wrapper" data-role="pdf_viewer_zoom_controls_wrapper">
                    <button data-role="pdf_viewer_decrease_zoom_button"></button>
                    <button data-role="pdf_viewer_zoom_indicator"><span>${this.getCurrentZoomLevelText()}</span></button>
                    <div class="pdf-viewer__zoom-controls-list">${zoomControlsHtml}</div>
                    <button data-role="pdf_viewer_increase_zoom_button"></button>
                </div>
                <div class="pdf-viewer__fullscreen-button-wrapper">
                    <button class="pdf-viewer__fullscreen-button" data-role="pdf_viewer_fullscreen_button"></button>
                </div>`;
    }

    /**
     * Retrieve label for current zoom level value from configuration
     *
     * @returns {string}
     */
    getCurrentZoomLevelText() {
        const match = this.zoomLevels.find(zoomLevel => parseFloat(zoomLevel.value) === parseFloat(this.currentZoomLevel));
        return match ? match.label : '';
    }

    /**
     * Initialize component event listeners
     *
     * @returns {void}
     */
    initEventListeners() {
        this.initZoomEventListeners();
        this.initFullscreenEventListeners();
        this.initPanEventListeners();
        if (this.pdfData.numPages > 1) this.initPagesEventListeners();
    }

    /**
     * Initialize event listeners specifically for zoom functionality
     *
     * @returns {void}
     */
    initZoomEventListeners() {
        const zoomButtons = this.pdfToolbar.querySelectorAll('[data-role="pdf_viewer_zoom_button"]');
        this.zoomIndicator.addEventListener('click', () => {
            this.toggleActiveZoomDropdownClass('add')
        });

        zoomButtons.forEach(zoomButton => {
            zoomButton.addEventListener('click', () => {
                this.currentZoomLevel = zoomButton.getAttribute('data-zoom-level');
                this.renderPage(this.currentPage);
                this.updateZoomSelector();
                this.toggleActiveZoomDropdownClass('remove');
            });
        });

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_decrease_zoom_button"]').addEventListener('click', () => {
            this.changeZoomLevel(-1);
        });

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_increase_zoom_button"]').addEventListener('click', () => {
            this.changeZoomLevel(1);
        });

        this.addEventListener('wheel', (event) => {
            if (event.ctrlKey) {
                event.preventDefault();
                const wrapper = document.getElementsByClassName('pdf-viewer__inner-wrapper')[0];
                const pdfCanvas = this.pdfCanvas;

                const rect = wrapper.getBoundingClientRect();
                const cursorX = event.clientX - rect.left;
                const cursorY = event.clientY - rect.top;
                const scrollX = cursorX + wrapper.scrollLeft;
                const scrollY = cursorY + wrapper.scrollTop;

                const ratioX = scrollX / pdfCanvas.offsetWidth;
                const ratioY = scrollY / pdfCanvas.offsetHeight;

                if (event.deltaY > 0) {
                    this.changeZoomLevel(-1);
                } else {
                    this.changeZoomLevel(1);
                }

                setTimeout(() => {
                    wrapper.scrollLeft = ratioX * pdfCanvas.offsetWidth - cursorX;
                    wrapper.scrollTop = ratioY * pdfCanvas.offsetHeight - cursorY;
                }, 0);
            }
        });
    }


    /**
     * Initialize event listeners specifically for panning functionality
     *
     * @returns {void}
     */
    initPanEventListeners() {
        const wrapper = document.getElementsByClassName('pdf-viewer__inner-wrapper')[0];
        if (!wrapper) return;

        wrapper.addEventListener('mousedown', (event) => {
            if (event.target.closest('.pdf-viewer__zoom-controls-wrapper')) {
                return;
            }

            this.isPanning = true;
            this.startX = event.pageX - wrapper.offsetLeft + wrapper.scrollLeft;
            this.startY = event.pageY - wrapper.offsetTop + wrapper.scrollTop;
            this.scrollLeft = wrapper.scrollLeft;
            this.scrollTop = wrapper.scrollTop;

            wrapper.style.cursor = 'grabbing';
        });
        wrapper.addEventListener('mousemove', (event) => {
            if (!this.isPanning) return;
            event.preventDefault();
            const x = event.pageX - wrapper.offsetLeft;
            const y = event.pageY - wrapper.offsetTop;
            const walkX = x - (this.startX - this.scrollLeft);
            const walkY = y - (this.startY - this.scrollTop);
            wrapper.scrollLeft = this.scrollLeft - walkX;
            wrapper.scrollTop = this.scrollTop - walkY;
            wrapper.style.cursor = 'grabbing';
        });
        wrapper.addEventListener('mouseup', () => {
            this.isPanning = false;
            wrapper.style.cursor = 'grab';
        });
        wrapper.addEventListener('mouseleave', () => {
            this.isPanning = false;
            wrapper.style.cursor = 'grab';
        });
    }

    changeZoomLevel(direction) {
        const index = this.zoomLevels.findIndex(zoomLevel => parseFloat(zoomLevel.value) === parseFloat(this.currentZoomLevel));
        const newIndex = index + direction;
        if (newIndex >= 0 && newIndex < this.zoomLevels.length) {
            this.currentZoomLevel = this.zoomLevels[newIndex].value;
            this.renderPage(this.currentPage);
            this.updateZoomSelector();
        }
    }

    /**
     * Retrieve correct method to enter full-screen mode for current browser
     *
     * @returns {null|string}
     */
    getFullscreenAPI() {
        return this.innerWrapper.requestFullscreen || this.innerWrapper.webkitRequestFullscreen;
    }

    /**
     * Retrieve correct method to exit full-screen mode for current browser
     *
     * @returns {null|string}
     */
    getExitFullscreenAPI() {
        return document.exitFullscreen || document.webkitExitFullscreen;
    }

    getCurrentFullscreenElement() {
        return document.fullscreenElement || document.webkitCurrentFullScreenElement;
    }

    /**
     * Initialize event listeners specifically for fullscreen switch functionality
     *
     * @returns {void}
     */
    initFullscreenEventListeners() {
        const fullscreenAPI = this.getFullscreenAPI();
        const exitFullscreenAPI = this.getExitFullscreenAPI();

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_fullscreen_button"]').addEventListener('click', () => {
            if (!this.getCurrentFullscreenElement()) {
                document.body.classList.add(this.activeFullscreenClass);
                fullscreenAPI.call(this.innerWrapper).then(() => {
                    this.currentZoomLevel = this.initialZoomLevel;
                    this.renderPage(this.currentPage);
                    this.updateZoomSelector();
                    this.toggleActiveZoomDropdownClass('remove');
                });
            } else {
                document.body.classList.remove(this.activeFullscreenClass);
                exitFullscreenAPI.call(document).then(() => {
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
        this.pdfToolbar.querySelector('[data-role="pdf_viewer_prev_page_button"]').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.renderPage(this.currentPage);
                this.updatePageIndicator();
            }
        });

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_next_page_button"]').addEventListener('click', () => {
            if (this.currentPage < this.pdfData.numPages) {
                this.currentPage++;
                this.renderPage(this.currentPage);
                this.updatePageIndicator();
            }
        });
    }

    /**
     * Set active state on zoom button that matches current zoom level
     *
     * @returns {void}
     */
    updatePageIndicator() {
        this.pdfToolbar.querySelector('[data-role="pdf_viewer_page_indicator"]').innerText = this.currentPage;
        this.pdfCanvas.parentElement.scrollTop = 0;
    }

    updateZoomSelector() {
        const zoomButtons = this.pdfToolbar.querySelectorAll('[data-role="pdf_viewer_zoom_button"]');
        zoomButtons.forEach(button => button.classList.remove(this.activeZoomControlClass));
        const activeButton = this.pdfToolbar.querySelector(`[data-role="pdf_viewer_zoom_button"][data-zoom-level="${this.currentZoomLevel}"]`);
        if (activeButton) activeButton.classList.add(this.activeZoomControlClass);
        this.zoomIndicator.innerText = this.getCurrentZoomLevelText();
    }

    /**
     * Initialize PDF rendering if PDFJS library is loaded
     *
     * @returns {void}
     */
    render() {
        this.url = this.getAttribute('url');
        if (window.pdfjsLib) this.initialize();
    }

    /**
     * Render given PDF page number based on given scale size (if provided) or current zoom level value
     *
     * @param {int} pageNumber
     * @param {float} scale
     * @returns {void}
     */
    renderPage(pageNumber, scale = this.currentZoomLevel) {
        this.pdfData.getPage(pageNumber).then(page => {
            const viewport = page.getViewport({ scale });
            this.pdfCanvas.width = viewport.width;
            this.pdfCanvas.height = viewport.height;
            this.pdfCanvas.style.width = `${viewport.width}px`;
            this.pdfCanvas.style.height = `${viewport.height}px`;

            const renderContext = {
                canvasContext: this.pdfCanvasContext,
                viewport
            };
            page.render(renderContext);
        });
    }

    /**
     * Add or remove active dropdown class for zoom selector based on current state
     *
     * @param {string|null} method
     * @returns {void}
     */
    toggleActiveZoomDropdownClass(method = null) {
        const action = method || (this.zoomControlsWrapper.classList.contains(this.activeZoomDropdownClass) ? 'remove' : 'add');
        this.zoomControlsWrapper.classList[action](this.activeZoomDropdownClass);
    }

    hideLoader() {
        document.dispatchEvent(new Event('hideAssetPreviewLoader'));
    }

    createElement(tag, className = '', innerHTML = '') {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (innerHTML) element.innerHTML = innerHTML;
        return element;
    }
}

customElements.define('app-pdf-viewer', PdfViewer);
