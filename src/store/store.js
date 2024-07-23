/* eslint-disable no-console */
// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import pinia from '../pinia.js'
import { useUIStore } from './modules/ui.js'
import { useSearchStore } from './modules/search.js'
import { useCatalogiStore } from './modules/catalogi.js'
import { useDirectoryStore } from './modules/directory.js'
import { useMetadataStore } from './modules/metadata.js'
import { usePublicationStore } from './modules/publication.js'

const UIStore = useUIStore(pinia)
const searchStore = useSearchStore(pinia)
const catalogiStore = useCatalogiStore(pinia)
const directoryStore = useDirectoryStore(pinia)
const metadataStore = useMetadataStore(pinia)
const publicationStore = usePublicationStore(pinia)

export {
	// generic
	UIStore,
	searchStore,
	// feature-specific
	catalogiStore,
	directoryStore,
	metadataStore,
	publicationStore,
}
