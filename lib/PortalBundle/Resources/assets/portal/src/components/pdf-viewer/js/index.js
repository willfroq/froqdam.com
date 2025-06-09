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
        this.currentRotation = 0;
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
        this.useNativeViewer = false;
        this.nativeViewerFrame = null;
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

        const nativeViewerToggle = `
            <label class="inline-flex items-center cursor-pointer ml-4 mr-5">
                <input type="checkbox" class="sr-only peer" data-role="pdf_viewer_native_toggle">
                <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500 dark:peer-checked:bg-emerald-600"></div>
            </label>`;

        return `${pageControlsHtml}
                <div class="pdf-viewer__zoom-controls-wrapper" data-role="pdf_viewer_zoom_controls_wrapper">
                    <button data-role="pdf_viewer_decrease_zoom_button"></button>
                    <button data-role="pdf_viewer_zoom_indicator"><span>${this.getCurrentZoomLevelText()}</span></button>
                    <div class="pdf-viewer__zoom-controls-list">${zoomControlsHtml}</div>
                    <button data-role="pdf_viewer_increase_zoom_button"></button>
                </div>
                ${nativeViewerToggle}
                <div class="pdf-viewer__rotation-controls-wrapper mr-3">
                    <button data-role="pdf_viewer_rotate_left_button" title="Rotate Left">
                        <svg width="18" height="18" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <path fill="white" d="M480 256c0 123.4-100.5 223.9-223.9 223.9c-48.86 0-95.19-15.58-134.2-44.86c-14.14-10.59-17-30.66-6.391-44.81c10.61-14.09 30.69-16.97 44.8-6.375c27.84 20.91 61 31.94 95.89 31.94C344.3 415.8 416 344.1 416 256s-71.67-159.8-159.8-159.8C205.9 96.22 158.6 120.3 128.6 160H192c17.67 0 32 14.31 32 32S209.7 224 192 224H48c-17.67 0-32-14.31-32-32V48c0-17.69 14.33-32 32-32s32 14.31 32 32v70.23C122.1 64.58 186.1 32.11 256.1 32.11C379.5 32.11 480 132.6 480 256z"/>
                        </svg>
                    </button>
                    <button data-role="pdf_viewer_rotate_right_button" title="Rotate Right">
                        <svg width="18" height="18" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <path fill="white" d="M496 48V192c0 17.69-14.31 32-32 32H320c-17.69 0-32-14.31-32-32s14.31-32 32-32h63.39c-29.97-39.7-77.25-63.78-127.6-63.78C167.7 96.22 96 167.9 96 256s71.69 159.8 159.8 159.8c34.88 0 68.03-11.03 95.88-31.94c14.22-10.53 34.22-7.75 44.81 6.375c10.59 14.16 7.75 34.22-6.375 44.81c-39.03 29.28-85.36 44.86-134.2 44.86C132.5 479.9 32 379.4 32 256s100.5-223.9 223.9-223.9c69.15 0 134 32.47 176.1 86.12V48c0-17.69 14.31-32 32-32S496 30.31 496 48z"/>
                        </svg>
                    </button>
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
        this.initRotationEventListeners();
        this.initNativeViewerToggle();
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
            const viewport = page.getViewport({ scale, rotation: this.currentRotation });
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

    initNativeViewerToggle() {
        const toggle = this.pdfToolbar.querySelector('[data-role="pdf_viewer_native_toggle"]');
        if (!toggle) return;

        toggle.addEventListener('change', (event) => {
            this.useNativeViewer = event.target.checked;
            this.switchViewer();
        });
    }

    switchViewer() {
        if (this.useNativeViewer) {
            // Hide PDF.js viewer
            this.pdfCanvasWrapper.style.display = 'none';

            // Hide zoom controls
            this.zoomControlsWrapper.style.display = 'none';

            // Create and show native viewer iframe
            if (!this.nativeViewerFrame) {
                this.nativeViewerFrame = document.createElement('iframe');
                this.nativeViewerFrame.style.width = '100%';
                this.nativeViewerFrame.style.height = '100%';
                this.nativeViewerFrame.style.border = 'none';
                this.nativeViewerFrame.src = this.url;
                this.innerWrapper.insertBefore(this.nativeViewerFrame, this.pdfCanvasWrapper);
            }
            this.nativeViewerFrame.style.display = 'block';
        } else {
            // Show PDF.js viewer
            this.pdfCanvasWrapper.style.display = 'block';

            // Show zoom controls
            this.zoomControlsWrapper.style.display = 'flex';

            // Hide native viewer iframe
            if (this.nativeViewerFrame) {
                this.nativeViewerFrame.style.display = 'none';
            }
        }
    }

    /**
     * Initialize event listeners specifically for rotation functionality
     *
     * @returns {void}
     */
    initRotationEventListeners() {
        this.pdfToolbar.querySelector('[data-role="pdf_viewer_rotate_left_button"]').addEventListener('click', () => {
            this.rotate(-90);
        });

        this.pdfToolbar.querySelector('[data-role="pdf_viewer_rotate_right_button"]').addEventListener('click', () => {
            this.rotate(90);
        });
    }

    /**
     * Rotate the PDF by the specified angle
     *
     * @param {number} angle - The angle to rotate by (in degrees)
     * @returns {void}
     */
    rotate(angle) {
        this.currentRotation = (this.currentRotation + angle) % 360;
        this.renderPage(this.currentPage);
    }
}

customElements.define('app-pdf-viewer', PdfViewer);
