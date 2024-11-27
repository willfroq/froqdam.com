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
        super.connectedCallback();
        this.render();
        this.updateClassBasedOnState();
    }

    disconnectedCallback() {
        this.cleanup();
        window.removeEventListener('storage', this.updateClassBasedOnState);
        super.disconnectedCallback();
    }

    render() {
        this.state = this.getAttribute('state');
        this.method = this.getAttribute('method') ?? 'add'

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
        const storageKey = 'selectorStates';
        let storedStates = JSON.parse(localStorage.getItem(storageKey)) || [];

        const stateIndex = storedStates.indexOf(this.state);

        if (stateIndex !== -1) {
            storedStates.splice(stateIndex, 1);
            this.runDispatch(storedStates);
        }

        if (stateIndex === -1 && this.method === 'add') {
            storedStates.push(this.state);
        }

        if (this.method === 'delete') {
            this.updateMainList();
        }

        localStorage.setItem(storageKey, JSON.stringify(storedStates));
        this.updateClassBasedOnState();
        this.updateButtonIndicator(storedStates);
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'state' && oldValue !== newValue) {
            this.state = newValue;
            this.attachEventListeners();
            this.updateClassBasedOnState();
        }
    }

    updateClassBasedOnState() {
        if (this.method === 'delete'){
            return
        }
        const storageKey = 'selectorStates';
        const storedStates = JSON.parse(localStorage.getItem(storageKey)) || [];

        if (storedStates.includes(this.state)) {
            this.classList.add('element-selected');
            return
        }

        this.classList.remove('element-selected');
    }

    updateButtonIndicator(storedStates) {
        const indicator = document.getElementById('share-button');
        const numberElement = indicator.querySelector('span');

        if (storedStates.length){
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
                    selector.classList.remove('element-selected');
                }
            });
        });
    }
}

customElements.define('app-selector', Selector);
