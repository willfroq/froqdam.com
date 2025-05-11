import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['text', 'icon'];
    
    expanded = false;
    
    toggle() {
        this.expanded = !this.expanded;
        
        this.textTarget.textContent = this.expanded ? 'View less' : `View all (${this.element.dataset.count})`;
        
        this.iconTarget.classList.toggle('rotate-180');
        
        const viewAllEvent = new CustomEvent('filter:viewAll', {
            bubbles: true,
            detail: { expanded: this.expanded }
        });
        
        this.element.dispatchEvent(viewAllEvent);
    }
}