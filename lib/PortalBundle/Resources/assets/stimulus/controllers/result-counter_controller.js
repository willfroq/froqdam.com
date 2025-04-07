import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['counter']

    connect() {
        window.addEventListener('search:results', this.updateCount.bind(this))
    }

    disconnect() {
        window.removeEventListener('search:results', this.updateCount.bind(this))
    }

    updateCount(event) {
        this.counterTarget.textContent = event.detail.count
    }
} 