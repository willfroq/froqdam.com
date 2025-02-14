import { UiComponent } from "../../ui-component/js";

class ClearBasket extends UiComponent {
    constructor() {
        super();
        this.target = null;
        this.onClickListener = this.onClickListener.bind(this);
    }

    connectedCallback() {
        super.connectedCallback();
        this.render();
    }

    disconnectedCallback() {
        this.cleanup();
    }

    render() {
        this.modalTarget = this.getAttribute('modal-target') ?? 'dialog';
        this.closeButton = this.getAttribute('close-id') ?? 'close-modal';
        this.overlay =  document.getElementById('modal-overlay');
        this.modal = this.querySelector(this.modalTarget);

        this.attachEventListeners();
    }

    attachEventListeners() {
        this.addEventListener('click', this.onClickListener);
    }

    cleanup() {
        this.removeEventListener('click', this.onClickListener);
    }

    onClickListener(e) {
        e.stopImmediatePropagation();
        const storageKey = 'selectorStates';
        let storedStates = JSON.parse(localStorage.getItem(storageKey)) || [];

        this.runDispatch(storedStates);
        this.updateMainList(storedStates);
        this.closeModal();

        localStorage.setItem(storageKey, null);
    }

    closeModal() {
        document.querySelector('#close-modal').click()
    }

    deactivateElement(element) {
        element.classList.remove('element-selected');
        element.innerHTML = element.innerHTML.replace(/Remove from/g, "Add to");
        const iconContainer = element.querySelector('#icon-selector-container');
        if(iconContainer){
            iconContainer.querySelector('.remove-icon').classList.add('d-none');
            iconContainer.querySelector('.add-icon').classList.remove('d-none');
        }
    }

    runDispatch (storedStates) {
        const shareModalElement = document.querySelector('app-share-modal');
        const updateEvent = new CustomEvent('updateShareList', {
            detail: {
                newList: storedStates
            }
        });
        shareModalElement.dispatchEvent(updateEvent);
    }

    updateMainList(clearedList = []) {
        const gridItems = document.querySelectorAll('app-grid-item');

        gridItems.forEach(gridItem => {
            const appSelectors = gridItem.querySelectorAll('app-selector');

            appSelectors.forEach(selector => {
                if (clearedList.includes(selector.getAttribute('state'))) {
                    this.deactivateElement(selector);
                }
            });
        });
    }
}

customElements.define('app-clear-basket', ClearBasket);
