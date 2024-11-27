import {UiComponent} from "../../ui-component/js";

export class Modal extends UiComponent {
    constructor() {
        super();

        this.trigger = null;
        this.modal = null;
        this.closeButton = null;
        this.overlay = null;
        this.onOpenEventListener = null
        this.onOpenEventListenerSource = null
        this.openModal = this.openModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
    }

    connectedCallback() {
        super.connectedCallback();
        setTimeout(this.render.bind(this));
    }

    render() {
        this.trigger = document.getElementById(this.getAttribute('trigger-id'));
        this.closeButton = document.getElementById(this.getAttribute('close-id') ?? 'close-modal');
        this.onOpenEventListener = this.getAttribute('onOpen-listener') ?? null;
        this.onOpenEventListenerSource = this.getAttribute('onOpen-listener-source') ?? null;
        this.overlay =  document.getElementById('modal-overlay');
        this.modal = this.querySelector('dialog');

        this.initEventListeners();
    }

    initEventListeners () {
        this.closeButton.addEventListener('click', () => this.closeModal());
        this.overlay.addEventListener('click', () => this.closeModal());
        this.trigger.addEventListener('click', () => this.openModal());
    }
    openModal() {
        if (!this.modal) {
            return
        }

        this.modal.setAttribute('open', true);
        this.overlay.classList.add('modal-overlay--visible')

        if (!this.onOpenEventListener || !this.onOpenEventListenerSource){
            return
        }

        const targetEvent = document.querySelector(this.onOpenEventListenerSource);
        const updateEvent = new CustomEvent(this.onOpenEventListener);
        targetEvent.dispatchEvent(updateEvent);
    }

    closeModal() {
        if (this.modal) {
            this.modal.removeAttribute('open');
            this.overlay.classList.remove('modal-overlay--visible')
        }
    }
}

customElements.define('app-modal', Modal);