import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'spinner']
    static values = {
        loading: Boolean,
        disabled: Boolean
    }

    connect() {
        this.loadingValue = false
        this.disabledValue = this.element.hasAttribute('disabled')
    }

    click(event) {
        if (this.disabledValue || this.loadingValue) {
            event.preventDefault()
            return
        }

        this.dispatch('click', { 
            detail: { 
                buttonId: this.element.id,
                variant: this.element.dataset.variant 
            } 
        })
    }

    setLoading(isLoading) {
        this.loadingValue = isLoading
        this.element.disabled = isLoading
        
        if (this.hasSpinnerTarget) {
            this.spinnerTarget.style.display = isLoading ? 'inline-block' : 'none'
        }
    }

    setDisabled(isDisabled) {
        this.disabledValue = isDisabled
        this.element.disabled = isDisabled
    }
} 