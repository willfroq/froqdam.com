export function setLoadingState(loading) {
    const viewMoreElement = document.getElementById('view-more');

    if (loading){
        this.loading = true
        document.dispatchEvent(new Event('showSearchResultsLoader'));
        viewMoreElement?.classList?.add('loading');
        return;
    }

    this.loading = false
    document.dispatchEvent(new Event('hideSearchResultsLoader'));
    viewMoreElement?.classList?.remove('loading');
}
