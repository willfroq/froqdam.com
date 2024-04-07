(() => {
    document.addEventListener('showAssetPreviewLoader', () => {
        document.querySelector('.asset-detail-preview__loading-screen').classList.add('asset-detail-preview__loading-screen--visible')
    });

    document.addEventListener('hideAssetPreviewLoader', () => {
        document.querySelector('.asset-detail-preview__loading-screen').classList.remove('asset-detail-preview__loading-screen--visible')
    });

    document.addEventListener('showSearchResultsLoader', () => {
        document.querySelector('.search-results__loading-screen').classList.add('search-results__loading-screen--visible')
    });

    document.addEventListener('hideSearchResultsLoader', () => {
        document.querySelector('.search-results__loading-screen').classList.remove('search-results__loading-screen--visible')
    });
})()
