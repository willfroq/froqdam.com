import { Controller } from '@hotwired/stimulus'
import {isEmpty} from "lodash"

export default class extends Controller {
    static targets = [
        "searchInput",
        "colourGuidelinePage",
        "assetPage",
        "sidebarCheckbox",
        "clearButton",
        "sidebarSearchInput",
        "sidebarRangeMinInput",
        "sidebarRangeMaxInput",
        "sidebarStartDateInput",
        "sidebarEndDateInput",
    ]

    static values = {
        url: String,
    }

    /** @return void */
    connect() {
        if (this.hasSearchInputTarget) {
            this.searchInputTarget.addEventListener("keydown", this.#handleKeydown.bind(this))
            this.searchInputTarget.addEventListener("blur", this.#handleBlur.bind(this))
        }

        if (this.hasSidebarSearchInputTarget) {
            this.sidebarSearchInputTarget.addEventListener("keydown", this.#handleKeydown.bind(this))
        }

        this.#updateClearButtonVisibility()
    }

    /** @return void */
    handleInput() {
        this.#updateClearButtonVisibility()
    }

    /** @return void */
    clearSearch() {
        this.searchInputTarget.value = ""
        this.#updateClearButtonVisibility()
        this.#submit()
    }

    /** @return void */
    #updateClearButtonVisibility() {
        if (this.hasClearButtonTarget) {
            const hasValue = this.searchInputTarget.value.trim().length > 0

            if (hasValue) {
                this.clearButtonTarget.classList.remove("opacity-0", "pointer-events-none")
                this.clearButtonTarget.classList.add("opacity-100")
            } else {
                this.clearButtonTarget.classList.remove("opacity-100")
                this.clearButtonTarget.classList.add("opacity-0", "pointer-events-none")
            }
        }
    }

    /** @return void */
    #handleKeydown(event) {
        if (event.key !== "Enter") {
            return
        }

        event.preventDefault()

        if (event.currentTarget.dataset.sidebarSearchInput === 'sidebarSearchInput') {
            this.#sidebarInputSubmit(event)

            return
        }

        this.#submit()
    }

    /**
     * @param event
     *
     * @return void
     */
    #handleBlur(event) {
        event.preventDefault()

        this.#submit()
    }

    /** @return void */
    #submit() {
        const searchTerm = this.searchInputTarget.value.trim()
        const currentUrl = window.location.href
        const currentUrlOrigin = window.location.origin
        const currentQuery = new URL(currentUrlOrigin).searchParams.get("query")

        if (searchTerm === currentQuery) return

        const url = new URL(currentUrl)

        if (searchTerm) {
            url.searchParams.set("query", searchTerm)
        } else {
            url.searchParams.delete("query")
        }

        history.pushState({}, "", url.toString())

        if (currentUrl.includes('portal/colour-library/search')) {
            this.colourGuidelinePageTarget.src = url

            return
        }

        if (currentUrl.includes('portal/asset-library/search')) {
            this.assetPageTarget.src = url
        }
    }

    /**
     * @param event
     *
     * @return void
     */
    toggle(event) {
        const checkbox = event.target
        const isChecked = checkbox.checked
        const searchTerm = checkbox.value
        const filterName = checkbox.dataset.filtername

        const currentUrl = window.location.href
        const url = new URL(currentUrl)
        const queryString = url.search
        const queryStringDecoded = decodeURIComponent(queryString)

        const currentSearchParams = this.#parseFiltersFromQuery(queryStringDecoded)

        if (isChecked === true) {
            if (isEmpty(currentSearchParams.filters) || isEmpty(currentSearchParams.filters[filterName])) {
                url.searchParams.append(`filters[${filterName}][0]`, searchTerm)

                history.pushState({}, "", url.toString())

                if (currentUrl.includes('portal/colour-library/search')) {
                    this.colourGuidelinePageTarget.src = url

                    return
                }

                if (currentUrl.includes('portal/asset-library/search')) {
                    this.assetPageTarget.src = url
                }

                return
            }

            if (!isEmpty(currentSearchParams.filters)) {
                const clickedFilterGroup = Array.from(currentSearchParams.filters[filterName])

                this.#deleteFiltersParams(url)

                clickedFilterGroup.push(searchTerm)

                currentSearchParams.filters[filterName] = clickedFilterGroup

                this.#setCurrentUrlParams(currentSearchParams, filterName, url)

                history.pushState({}, "", url.toString())

                if (currentUrl.includes('portal/colour-library/search')) {
                    this.colourGuidelinePageTarget.src = url

                    return
                }

                if (currentUrl.includes('portal/asset-library/search')) {
                    this.assetPageTarget.src = url
                }

                return
            }
        }

        if (isChecked === false) {
            if (!isEmpty(currentSearchParams.filters)) {
                const clickedFilterGroup = [...new Set(Array.from(currentSearchParams.filters[filterName]))]

                this.#deleteFiltersParams(url)

                currentSearchParams.filters[filterName] = clickedFilterGroup.filter(item => item !== searchTerm)

                this.#setCurrentUrlParams(currentSearchParams, filterName, url)

                history.pushState({}, "", url.toString())

                if (currentUrl.includes('portal/colour-library/search')) {
                    this.colourGuidelinePageTarget.src = url

                    return
                }

                if (currentUrl.includes('portal/asset-library/search')) {
                    this.assetPageTarget.src = url
                }
            }
        }
    }

    #sidebarInputSubmit(event) {
        const filterName = event.currentTarget.dataset.filterName

        const searchTerm = event.currentTarget.value.trim()

        const currentUrl = window.location.href
        const url = new URL(currentUrl)

        url.searchParams.append(`filters[${filterName}]=`, searchTerm)

        history.pushState({}, "", url.toString())

        if (currentUrl.includes('portal/colour-library/search')) {
            this.colourGuidelinePageTarget.src = url

            return
        }

        if (currentUrl.includes('portal/asset-library/search')) {
            this.assetPageTarget.src = url
        }
    }

    /**
     * @param event
     *
     * @return void
     */
    numberRangeSubmit(event) {
        if (event.key !== "Enter") {
            return
        }

        event.preventDefault()

        const filterName = event.currentTarget.dataset.filterName

        const inputMax = document.getElementById(`range-max-input-${filterName}`)
        const inputMin = document.getElementById(`range-min-input-${filterName}`)

        const currentUrl = window.location.href
        const url = new URL(currentUrl)

        const maxValue = inputMax.value.trim()
        const minValue = inputMin.value.trim()

        url.searchParams.append(`filters[${filterName}][max]=`, String(maxValue))
        url.searchParams.append(`filters[${filterName}][min]=`, String(minValue))

        history.pushState({}, "", url.toString())

        if (currentUrl.includes('portal/colour-library/search')) {
            this.colourGuidelinePageTarget.src = url

            return
        }

        if (currentUrl.includes('portal/asset-library/search')) {
            this.assetPageTarget.src = url
        }
    }

    /**
     * @param event
     *
     * @return void
     */
    dateRangeSubmit(event) {
        const filterName = event.currentTarget.dataset.filterName

        const currentUrl = window.location.href
        const url = new URL(currentUrl)

        let startDateValue = ''
        let endDateValue = ''

        if (event.currentTarget.dataset.startdate === 'startDate') {
            startDateValue = this.sidebarStartDateInputTarget.value.trim()

            url.searchParams.append(`filters[${filterName}][startDate]=`, String(startDateValue))
        }

        if (event.currentTarget.dataset.enddate === 'endDate') {
            endDateValue = this.sidebarEndDateInputTarget.value.trim()

            url.searchParams.append(`filters[${filterName}][endDate]=`, String(endDateValue))
        }

        history.pushState({}, "", url.toString())

        if (currentUrl.includes('portal/colour-library/search')) {
            this.colourGuidelinePageTarget.src = url

            return
        }

        if (currentUrl.includes('portal/asset-library/search')) {
            this.assetPageTarget.src = url
        }
    }

    /**
     * @param queryStringDecoded string
     * @returns {{filters: {}}}
     */
    #parseFiltersFromQuery(queryStringDecoded) {
        const urlQuery = new URLSearchParams(queryStringDecoded)

        let currentSearchParams = { filters: {} }

        for (const [key, value] of urlQuery.entries()) {
            const match = key.match(/^filters\[(\w+)]\[(\d+)]$/)

            if (match) {
                const filterKey = match[1]

                if (!currentSearchParams.filters[filterKey]) {
                    currentSearchParams.filters[filterKey] = []
                }

                currentSearchParams.filters[filterKey].push(value)
            }
        }

        return currentSearchParams
    }

    /**
     * @param url URL
     */
    #deleteFiltersParams(url) {
        const keysToDelete = []

        for (const [key] of url.searchParams.entries()) {
            if (key.startsWith("filters[")) {
                keysToDelete.push(key)
            }
        }

        keysToDelete.forEach(key => url.searchParams.delete(key))
    }

    /**
     * @param currentSearchParams {{filters: {}}}
     * @param filterName string
     * @param url URL
     *
     * @returns void
     */
    #setCurrentUrlParams(currentSearchParams, filterName, url) {
        for (let filterNameKey in currentSearchParams.filters) {
            if (!Object.hasOwn(currentSearchParams.filters, filterNameKey)) {
                continue
            }

            Array.from(currentSearchParams.filters[filterNameKey]).forEach((filterValue, filterKey) => {
                url.searchParams.append(`filters[${filterNameKey}][${filterKey}]`, String(filterValue))
            })
        }
    }
}