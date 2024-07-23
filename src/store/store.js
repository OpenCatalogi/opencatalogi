/* eslint-disable no-console */
// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import { useUIStore } from './modules/ui.js'
import { useSearchStore } from './modules/search.js'
import { useCatalogiStore } from './modules/catalogi.js'
import { useDirectoryStore } from './modules/directory.js'
import { useMetadataStore } from './modules/metadata.js'
import { usePublicationStore } from './modules/publication.js'

export {
	// generic
	useUIStore,
	useSearchStore,
	// feature-specific
	useCatalogiStore,
	useDirectoryStore,
	useMetadataStore,
	usePublicationStore,
}
