import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'selectedText'];
    
    connect() {
        document.addEventListener('click', this.clickOutside.bind(this));
    }
    
    disconnect() {
        document.removeEventListener('click', this.clickOutside.bind(this));
    }
    
    toggle(event) {
        event.stopPropagation();
        
        this.menuTarget.classList.toggle('hidden');
    }
    
    select(event) {
        event.preventDefault();
        
        const option = event.currentTarget.dataset.option;
        
        this.selectedTextTarget.textContent = option;
        
        this.menuTarget.classList.add('hidden');
        
        console.log('Selected sort option:', option);
    }
    
    clickOutside(event) {
        if (!this.element.contains(event.target) && !this.menuTarget.classList.contains('hidden')) {
            this.menuTarget.classList.add('hidden');
        }
    }
}