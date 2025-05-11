import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from '@hotwired/turbo'

export default class extends Controller {
    static values = {
        page: Number,
        url: String
    }

    connect() {
        this.#createObserver()
    }

    #createObserver() {
        this.sentinel = document.createElement('div')
        this.element.appendChild(this.sentinel)

        this.observer = new IntersectionObserver(entries => {
            if (entries.find(entry => entry).isIntersecting) {
                this.#loadMore()
            }
        }, {
            threshold: 1.0
        })

        this.observer.observe(this.sentinel)
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
                if (html.includes('empty-colour-guideline-items') || html.includes('login-form')) {
                    return
                }

                renderStreamMessage(html)
            })
            .catch(error => console.error('Infinite scroll error:', error))
    }
}
