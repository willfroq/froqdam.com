import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'selectedText', 'searchInput', 'optionsContainer'];
    static values = {
        url: String,
        type: String,
        id: String,
        debounce: { type: Number, default: 300 }
    }
    
    connect() {
        this.clickOutsideHandler = this.clickOutside.bind(this);
        document.addEventListener('click', this.clickOutsideHandler);
        this.searchDebounceTimer = null;
    }
    
    disconnect() {
        document.removeEventListener('click', this.clickOutsideHandler);
    }
    
    toggle(event) {
        event.stopPropagation();
        
        if (this.menuTarget.classList.contains('hidden')) {
            this.open();
        } else {
            this.close();
        }
    }
    
    open() {
        this.menuTarget.classList.remove('hidden');

        if (this.hasSearchInputTarget) {
            setTimeout(() => {
                this.searchInputTarget.focus();
            }, 100);
        }
    }
    
    close() {
        this.menuTarget.classList.add('hidden');
        if (this.hasSearchInputTarget) {
            this.searchInputTarget.value = '';
        }
    }
    
    search() {
        clearTimeout(this.searchDebounceTimer);
        
        this.searchDebounceTimer = setTimeout(() => {
            const query = this.searchInputTarget.value;
            
            const url = new URL(this.urlValue, window.location.origin);
            url.searchParams.append('q', query);
            url.searchParams.append('type', this.typeValue);
            url.searchParams.append('id', this.idValue);
            
            fetch(url, {
                headers: {
                    'Accept': 'text/vnd.turbo-stream.html',
                }
            });
        }, this.debounceValue);
    }
    
    select(event) {
        event.preventDefault();
        
        const option = event.currentTarget.dataset.option;
        const optionId = event.currentTarget.dataset.optionId;
        
        this.selectedTextTarget.textContent = option;
        this.close();
        
        const selectEvent = new CustomEvent('dropdown-select', {
            bubbles: true,
            detail: {
                option: option,
                optionId: optionId,
                type: this.typeValue
            }
        });
        
        this.element.dispatchEvent(selectEvent);
    }
    
    clickOutside(event) {
        if (!this.element.contains(event.target) && !this.menuTarget.classList.contains('hidden')) {
            this.close();
        }
    }
}