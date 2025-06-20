/* eslint-disable n/no-missing-import */
/* eslint-disable import/no-unresolved */
/* eslint-disable import/extensions */
// fk these rules above here

// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import pinia from '../pinia.js'
import { useNavigationStore } from './modules/navigation'
import { useSearchStore } from './modules/search'
import { useObjectStore } from './modules/object' // Import the object store
import { useCatalogStore } from './modules/catalog' // Import the catalog store

const navigationStore = useNavigationStore(pinia)
const searchStore = useSearchStore(pinia)
const objectStore = useObjectStore(pinia) // Initialize the object store
const catalogStore = useCatalogStore(pinia) // Initialize the catalog store

export {
	// generic
	navigationStore,
	searchStore,
	objectStore, // Export the object store
	catalogStore, // Export the catalog store
}
