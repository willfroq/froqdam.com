import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    connect() {
        document.addEventListener('keydown', this.handleKeydown.bind(this))
    }

    handleKeydown(event) {
        if (event.key === 'Escape') {
            this.back()
        }
    }

    back() {
        window.history.go(-1);
    }
}
