import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['checkbox'];
    
    connect() {
        // Get all checked items on initial load
        this.updateSelectionState();
    }
    
    itemSelected(event) {
        // Single selection mode (uncomment if you want only one checkbox selected at a time)
        /*
        if (event.currentTarget.checked) {
            // Uncheck all other checkboxes
            this.checkboxTargets.forEach(checkbox => {
                if (checkbox !== event.currentTarget) {
                    checkbox.checked = false;
                }
            });
        }
        */
        
        this.updateSelectionState();
    }
    
    updateSelectionState() {
        // Get all selected values
        const selectedValues = this.checkboxTargets
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        // Dispatch event with selected values
        const selectionEvent = new CustomEvent('filter:selection', {
            bubbles: true,
            detail: { selectedValues }
        });
        
        this.element.dispatchEvent(selectionEvent);
    }
}