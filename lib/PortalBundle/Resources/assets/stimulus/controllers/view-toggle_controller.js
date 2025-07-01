import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['gridButton', 'listButton']
    static values = {
        currentView: String
    }

    connect() {
        this.currentViewValue = this.element.dataset.view || 'grid'
        this.updateButtonStates()
    }

    toggle(event) {
        const newView = event.params.view;

        this.currentViewValue = newView;
        this.element.dataset.view = newView;
        this.element.dataset.viewToggleCurrentViewValue = newView;

        this.updateButtonStates();

        const viewChangeEvent = new CustomEvent('view-change', {
            bubbles: true,
            detail: { view: newView }
        });

        document.dispatchEvent(viewChangeEvent);

        const url = new URL(window.location);

        url.searchParams.set('view', newView)
        window.history.replaceState(null, '', url.toString())

        const turboFrame = document.getElementById('colour-guideline-page')

        if (turboFrame) {
            turboFrame.src = url
        }
    }

    updateButtonStates() {
        const isGridActive = this.currentViewValue === 'grid';
        const isListActive = this.currentViewValue === 'list';

        if (this.hasGridButtonTarget) {
            this.updateButton(this.gridButtonTarget, isGridActive);
        }

        if (this.hasListButtonTarget) {
            this.updateButton(this.listButtonTarget, isListActive);
        }
    }

    updateButton(button, isActive) {
        const icon = button.querySelector('span');

        if (isActive) {
            button.classList.remove('bg-white', 'hover:bg-gray-50');
            button.classList.add('bg-[#009383]');
            if (icon) {
                icon.classList.remove('text-[#009383]');
                icon.classList.add('text-white');
            }
        } else {
            button.classList.remove('bg-[#009383]');
            button.classList.add('bg-white', 'hover:bg-gray-50');
            if (icon) {
                icon.classList.remove('text-white');
                icon.classList.add('text-[#009383]');
            }
        }
    }
}