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

        this.printExpertNotesSections = document.getElementsByClassName('print-expert-notes-section')

        let index = 0

        for (const printExpertNotesSection of this.printExpertNotesSections) {
            if (index !== 0) {
                printExpertNotesSection.classList.add('hidden')
            }

            index++
        }
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

        let index = 0

        for (const printExpertNotesSection of this.printExpertNotesSections) {
            if (index === Number(optionId)) {
                printExpertNotesSection.classList.remove('hidden')
            }

            if (index !== Number(optionId)) {
                printExpertNotesSection.classList.add('hidden')
            }

            index++
        }
    }

    clickOutside(event) {
        if (!this.element.contains(event.target) && !this.menuTarget.classList.contains('hidden')) {
            this.close()
        }
    }
}