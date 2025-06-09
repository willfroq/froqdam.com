import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['clearButton']

    clearSidebar(event) {
        event.preventDefault()

        const url = event.currentTarget.dataset.url

        if (url) {
            window.location.href = event.currentTarget.dataset.url
        } else {
            console.warn("No redirect URL specified")
        }
    }
}