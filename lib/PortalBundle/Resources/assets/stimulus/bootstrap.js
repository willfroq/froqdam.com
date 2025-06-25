import { startStimulusApp } from '@symfony/stimulus-bridge'
import { definitionsFromContext } from 'stimulus/webpack-helpers'

const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

const assetLibraryControllers = require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers/asset-library',
    true,
    /\.[jt]sx?$/
)

const colourLibraryControllers = require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers/colour-library',
    true,
    /\.[jt]sx?$/
)

app.load(definitionsFromContext(assetLibraryControllers))
app.load(definitionsFromContext(colourLibraryControllers))

export { app }