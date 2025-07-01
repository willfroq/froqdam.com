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
        const referrer = document.referrer

        if (referrer && referrer.includes('/portal/colour-library/')) {
            window.history.go(-1)
        } else {
            window.location.href = '/portal/colour-library/search'
        }
    }
}
