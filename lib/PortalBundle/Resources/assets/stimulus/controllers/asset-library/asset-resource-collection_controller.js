import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['gridContainer', 'listContainer'];
    static values = {
        currentView: String
    }
    
    connect() {
        document.addEventListener('view-change', this.handleViewChange.bind(this));
        
        this.updateView(this.currentViewValue);
    }
    
    disconnect() {
        document.removeEventListener('view-change', this.handleViewChange.bind(this));
    }
    
    handleViewChange(event) {
        const view = event.detail.view;
        this.updateView(view);
    }
    
    updateView(view) {
        if (view === 'grid') {
            this.showGridView();
        } else if (view === 'list') {
            this.showListView();
        }
        
        this.currentViewValue = view;
    }
    
    showGridView() {
        this.gridContainerTarget.classList.remove('hidden');
        this.listContainerTarget.classList.add('hidden');
    }
    
    showListView() {
        this.gridContainerTarget.classList.add('hidden');
        this.listContainerTarget.classList.remove('hidden');
    }
}