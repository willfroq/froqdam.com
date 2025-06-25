import { UiComponent } from "../../ui-component/js";

class Selector extends UiComponent {
    constructor() {
        super();
        this.state = null;
        this.target = null;
        this.onClickListener = this.onClickListener.bind(this);
        this.updateClassBasedOnState = this.updateClassBasedOnState.bind(this);
    }

    static get observedAttributes() {
        return ['state'];
    }

    connectedCallback() {
        setTimeout(() => {
            super.connectedCallback();
            this.render();
            this.updateClassBasedOnState();
        }, 100)
    }

    disconnectedCallback() {
        this.cleanup();
        window.removeEventListener('storage', this.updateClassBasedOnState);
    }

    render() {
        this.state = this.getAttribute('state');
        this.triggerId = this.getAttribute('trigger-id') ?? 'button'

        this.attachEventListeners();
        window.addEventListener('storage', this.updateClassBasedOnState);
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

        const stateIndex = storedStates.indexOf(this.state);

        if (stateIndex !== -1) {
            storedStates.splice(stateIndex, 1);
        }

        if (stateIndex === -1) {
            storedStates.push(this.state);
        }

        localStorage.setItem(storageKey, JSON.stringify(storedStates));
        this.updateClassBasedOnState();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'state' && oldValue !== newValue) {
            this.state = newValue;
            this.attachEventListeners();
            this.updateClassBasedOnState();
        }
    }

    updateClassBasedOnState() {

        const storageKey = 'selectorStates';
        const storedStates = JSON.parse(localStorage.getItem(storageKey)) || [];

        if (storedStates.includes(this.state)) {
            this.activateElement(this);
            return
        }

        this.deactivateElement(this)
    }

    activateElement(element) {
        element.classList.add('element-selected');
        const trigger = element.querySelector(`#${this.triggerId}`)

        if (trigger === null) {
            return
        }

        trigger.innerHTML = 'Remove from Basket';
        trigger.classList.remove('button-primary');
        trigger.classList.add('button-error', 'bg-red-500', 'text-white');
    }

    deactivateElement(element) {
        element.classList.remove('element-selected');
        const trigger = element.querySelector(`#${this.triggerId}`)

        if (trigger === null) {
            return
        }

        trigger.innerHTML = 'Add to Basket';
        trigger.classList.remove('button-error', 'bg-red-500', 'text-white');
        trigger.classList.add('button-primary');
    }

    updateButtonIndicator(storedStates) {
        const indicator = document.getElementById('share-button');
        const numberElement = indicator.querySelector('span');

        if (storedStates?.length){
            indicator.classList.add('active')
            numberElement.classList.remove('hidden')
            numberElement.textContent = storedStates.length
            return;
        }

        numberElement.classList.add('hidden')
        indicator.classList.remove('active')
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

    updateMainList() {
        const gridItems = document.querySelectorAll('app-grid-item');

        gridItems.forEach(gridItem => {
            const appSelectors = gridItem.querySelectorAll('app-selector');

            appSelectors.forEach(selector => {
                if (selector.getAttribute('state') === this.state) {
                    this.deactivateElement(selector);
                }
            });
        });
    }
}

customElements.define('app-selector-details', Selector);