import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        currentView: String
    }
    
    connect() {
        this.currentViewValue = this.element.dataset.view || 'grid'
    }
    
    toggle() {

    }
}