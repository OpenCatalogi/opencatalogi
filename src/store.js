/* eslint-disable no-console */
// The store script handles app whide variables (or state), for the use of these variables and there governing concepts read the design.md
import { reactive } from 'vue'

export const store = reactive({
	// The curently active menu item, defaults to '' wich triggers the dashboard
	selected: 'dashboard',
	// The currently active modal, managed trought the state to ensure that only one modal can be active at the same time
	modal: false,
	modalData: [], // optional data to pass to the modal
	// The curently active item (or object) , managed trought the state to ensure that only one modal can be active at the same time
	item: false,
	catalogItem: false,
	directoryItem: false,
	metaDataItem: false,
	publicationItem: false,
	// Lets add some setters
	setSelected(selected) {
		this.selected = selected
		console.log('Active menu item set to ' + selected)
	},
	setModal(modal) {
		this.modal = modal
		console.log('Active modal item set to ' + modal)
	},
	setItem(item) {
		this.item = item
		console.log('Active object item set to ' + item)
	},
	setCatalogItem(catalogItem) {
		this.catalogItem = catalogItem
		console.log('Active catalog item set to ' + catalogItem)
	},
	setDirectoryItem(directoryItem) {
		this.directoryItem = directoryItem
		console.log('Active directory item set to ' + directoryItem)
	},
	setMetadataItem(metaDataItem) {
		this.metaDataItem = metaDataItem
		console.log('Active metadata item set to ' + metaDataItem)
	},
	setPublicationItem(publicationItem) {
		this.publicationItem = publicationItem
		console.log('Active publication item set to ' + publicationItem)
	},
})
