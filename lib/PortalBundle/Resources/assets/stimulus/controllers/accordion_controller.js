import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'content', 'icon'];

    connect() {
        this.isExpanded = this.buttonTarget.getAttribute('aria-expanded') === 'true';
        if (this.isExpanded) {
            this.contentTarget.style.maxHeight = 'none';
        }
    }

    toggle(event) {
        event.preventDefault();
        this.isExpanded = !this.isExpanded;
        this.buttonTarget.setAttribute('aria-expanded', this.isExpanded);
        
        if (this.isExpanded) {
            const scrollHeight = this.contentTarget.scrollHeight;
            this.contentTarget.style.maxHeight = `${scrollHeight}px`;
            
            setTimeout(() => {
                if (this.isExpanded) {
                    this.contentTarget.style.maxHeight = 'none';
                }
            }, 200);
            
            this.iconTarget.classList.add('rotate-180');
        } else {    
            const scrollHeight = this.contentTarget.scrollHeight;
            this.contentTarget.style.maxHeight = `${scrollHeight}px`;
            
            this.contentTarget.offsetHeight;
            
            this.contentTarget.style.maxHeight = '0';
            this.iconTarget.classList.remove('rotate-180');
        }
    }
}