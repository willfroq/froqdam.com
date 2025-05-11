import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ["searchInput", "colourGuidelinePage"]

    static values = {
        url: String
    }

    connect() {
        this.searchInputTarget.addEventListener("keydown", this.#handleKeydown.bind(this))
        this.searchInputTarget.addEventListener("blur", this.#handleBlur.bind(this))
    }

    #handleKeydown(event) {
        if (event.key === "Enter") {
            event.preventDefault()

            this.#submit()
        }
    }

    #handleBlur(event) {
        event.preventDefault()

        if (!this.searchInputTarget.value) return

        this.#submit()
    }

    #submit() {
        const searchTerm = this.searchInputTarget.value.trim()
        const currentUrlOrigin = window.location.origin
        const currentQuery = new URL(currentUrlOrigin).searchParams.get("query")

        if (searchTerm === currentQuery) return

        const uri = `${currentUrlOrigin}${this.urlValue}`
        const url = new URL(uri)

        url.searchParams.set("query", searchTerm)
        history.pushState({}, "", url.toString())

        this.colourGuidelinePageTarget.src = `${uri}?query=${encodeURIComponent(searchTerm)}`
    }
}