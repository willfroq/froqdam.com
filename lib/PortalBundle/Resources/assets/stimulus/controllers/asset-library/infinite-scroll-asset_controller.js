import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from '@hotwired/turbo'

export default class extends Controller {
    static values = {
        page: Number,
        url: String
    }

    connect() {
        this.loading = false
        this.lastScrollY = window.scrollY
        this.scrollThreshold = 50 // px from bottom to trigger load
        window.addEventListener("scroll", this.#onScroll)
    }

    disconnect() {
        window.removeEventListener("scroll", this.#onScroll)
    }

    #onScroll = () => {
        const currentScrollY = window.scrollY
        const scrollingDown = currentScrollY > this.lastScrollY
        this.lastScrollY = currentScrollY

        if (!scrollingDown || this.loading) return

        const scrollPosition = window.innerHeight + currentScrollY
        const pageHeight = document.documentElement.scrollHeight

        // Trigger when within threshold from bottom
        if (pageHeight - scrollPosition < this.scrollThreshold) {
            this.#loadMore()
        }
    }

    #loadMore() {
        this.pageValue += 1
        const queryString = window.location.search;
        const url = queryString.includes('?') ?
            `${this.urlValue}${queryString}&page=${this.pageValue}` :
            `${this.urlValue}?page=${this.pageValue}`

        fetch(url, {
            headers: {
                Accept: "text/vnd.turbo-stream.html"
            }
        })
            .then(response => {
                if (response.status !== 200) {
                    return
                }

                return response.text()
            })
            .then(html => {
                if (html.includes('empty-asset-guideline-items') || html.includes('login-form')) {
                    return
                }

                renderStreamMessage(html)
            })
            .catch(error => console.error('Infinite scroll error:', error))
    }
}
