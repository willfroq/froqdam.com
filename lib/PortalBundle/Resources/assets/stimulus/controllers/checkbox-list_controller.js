import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'viewAllButton', 'viewAllText', 'viewAllIcon'];
    static values = {
        totalCount: Number,
        initialCount: Number
    };

    expanded = false;

    connect() {
        // Ensure initial state is correct
        this.updateVisibility();

        // Handle any initially selected items beyond the visible limit
        this.handleInitialSelectedItems();
    }

    toggle() {
        this.expanded = !this.expanded;
        this.updateVisibility();
        this.updateButtonText();
        this.rotateIcon();

        // Dispatch custom event for potential external listeners
        this.dispatch('toggle', {
            detail: {
                expanded: this.expanded,
                visibleCount: this.getVisibleItemCount()
            }
        });
    }

    updateVisibility() {
        this.itemTargets.forEach((item, index) => {
            const shouldShow = this.expanded || index < this.initialCountValue || this.isItemSelected(item);

            if (shouldShow) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    updateButtonText() {
        if (this.hasViewAllTextTarget) {
            this.viewAllTextTarget.textContent = this.expanded
                ? 'View less'
                : `View all (${this.totalCountValue})`
        }
    }

    rotateIcon() {
        if (this.hasViewAllIconTarget) {
            if (this.expanded) {
                this.viewAllIconTarget.classList.add('rotate-180');
            } else {
                this.viewAllIconTarget.classList.remove('rotate-180');
            }
        }
    }

    handleInitialSelectedItems() {
        // If there are selected items beyond the initial count,
        // we should keep them visible
        const hasSelectedBeyondLimit = this.itemTargets.slice(this.initialCountValue)
            .some(item => this.isItemSelected(item));

        if (hasSelectedBeyondLimit && !this.expanded) {
            this.updateVisibility();
        }
    }

    isItemSelected(item) {
        const checkbox = item.querySelector('input[type="checkbox"]');
        return checkbox && checkbox.checked;
    }

    getVisibleItemCount() {
        return this.itemTargets.filter(item => !item.classList.contains('hidden')).length;
    }

    getHiddenItemCount() {
        return this.itemTargets.filter(item => item.classList.contains('hidden')).length;
    }
}