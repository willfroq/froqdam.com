import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['gridButton', 'listButton']
    static values = {
        current: String
    }

    connect() {
        this.updateActiveButton()
    }

    setLayout(event) {
        const layout = event.currentTarget.dataset.layout
        this.currentValue = layout
        this.updateActiveButton()
        
        const layoutEvent = new CustomEvent('layout:changed', {
            detail: { layout }
        })
        window.dispatchEvent(layoutEvent)
    }

    updateActiveButton() {
        this.gridButtonTarget.classList.toggle('active', this.currentValue === 'grid')
        this.listButtonTarget.classList.toggle('active', this.currentValue === 'list')
    }
} 