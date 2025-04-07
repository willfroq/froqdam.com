import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['dropdown', 'button', 'selectedText']

    connect() {
        document.addEventListener('click', this.handleClickOutside.bind(this))
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside.bind(this))
    }

    toggle(event) {
        event.stopPropagation()
        this.dropdownTarget.classList.toggle('hidden')
    }

    select(event) {
        const value = event.currentTarget.dataset.value
        const text = event.currentTarget.textContent.trim()
        
        this.selectedTextTarget.textContent = text
        this.dropdownTarget.classList.add('hidden')
        
        const sortEvent = new CustomEvent('sort:changed', {
            detail: { value, text }
        })
        window.dispatchEvent(sortEvent)
    }

    handleClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.dropdownTarget.classList.add('hidden')
        }
    }
}