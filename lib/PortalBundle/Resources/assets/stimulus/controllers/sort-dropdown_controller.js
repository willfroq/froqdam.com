import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = [
        'menu',
        'selectedText',
        'searchInput',
        'optionsContainer'
    ]

    static values = {
        url: String,
        type: String,
        id: String,
        debounce: { type: Number, default: 300 }
    }
    
    connect() {
        this.clickOutsideHandler = this.clickOutside.bind(this)
        document.addEventListener('click', this.clickOutsideHandler)
        this.searchDebounceTimer = null
    }
    
    disconnect() {
        document.removeEventListener('click', this.clickOutsideHandler)
    }

    toggle(event) {
        event.stopPropagation()

        if (this.menuTarget.classList.contains('hidden')) {
            this.open()
        } else {
            this.close()
        }
    }
    
    open() {
        this.menuTarget.classList.remove('hidden')

        if (this.hasSearchInputTarget) {
            setTimeout(() => {
                this.searchInputTarget.focus()
            }, 100)
        }
    }
    
    close() {
        this.menuTarget.classList.add('hidden')
        if (this.hasSearchInputTarget) {
            this.searchInputTarget.value = ''
        }
    }

    select(event) {
        event.preventDefault()

        const option = event.currentTarget.dataset.option
        const optionId = event.currentTarget.dataset.optionId

        this.selectedTextTarget.textContent = option
        this.close()

        const selectEvent = new CustomEvent('dropdown-select', {
            bubbles: true,
            detail: {
                option: option,
                optionId: optionId,
                type: this.typeValue
            }
        })

        this.element.dispatchEvent(selectEvent)

        this.#requestSort(event)
    }

    #requestSort(event) {
        const currentUrl = window.location.href

        let page

        if (currentUrl.includes('portal/colour-library/search')) {
            page = document.getElementById('colour-guideline-page')
        }

        if (currentUrl.includes('portal/asset-library/search')) {
            page = document.getElementById('asset-page')
        }

        if (!(page instanceof HTMLElement)) {
            return
        }

        const url = new URL(currentUrl)
        const selectedFiltername = event.currentTarget.dataset.filtername
        const selectedSortDirection = event.currentTarget.dataset.direction

        this.#deleteSortFiltersParams(url)

        url.searchParams.append(`sort_by`, selectedFiltername)
        url.searchParams.append(`sort_direction`, selectedSortDirection)

        history.pushState({}, "", url.toString())

        page.src = url
    }

    #deleteSortFiltersParams(url) {
        const keysToDelete = []

        for (const [key] of url.searchParams.entries()) {
            if (key.startsWith("sort_by") || key.startsWith("sort_direction")) {
                keysToDelete.push(key)
            }
        }

        keysToDelete.forEach(key => url.searchParams.delete(key))
    }

    clickOutside(event) {
        if (!this.element.contains(event.target) && !this.menuTarget.classList.contains('hidden')) {
            this.close()
        }
    }
}