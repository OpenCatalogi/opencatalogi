/* eslint-disable no-console */
// The store script handles app whide variables (or state), for the use of these variables and there governing concepts read the design.md
import { reactive } from 'vue'

export const store = reactive({
	// The curently active menu item, defaults to '' wich triggers the dashboard
	selected: 'metaData',
	// The current search term
	search: '',
	// The currently active modal, managed trought the state to ensure that only one modal can be active at the same time
	modal: false,
	modalData: [], // optional data to pass to the modal
	// The curently active item (or object) , managed trought the state to ensure that only one modal can be active at the same time
	item: false,
	catalogiItem: false,
	listItem: false,
	listingId: false,
	listingItem: false,
	// Metadata
	metaDataItem: false,
	metaDataList: [],
	// Publications
	publicationId: false,
	publicationItem: false,
	publicationDataKey: false,
	attachmentId: false,
	refresh: false,
	// Lets add some setters
	setSelected(selected) {
		this.selected = selected
		console.log('Active menu item set to ' + selected)
	},
	setSearch(search) {
		this.search = search
		console.log('Active search set to ' + search)
	},
	clearSearch() {
		this.search = ''
	},
	setModal(modal) {
		this.modal = modal
		console.log('Active modal item set to ' + modal)
	},
	setItem(item) {
		this.item = item
		console.log('Active object item set to ' + item)
	},
	setCatalogiItem(catalogiItem) {
		this.catalogiItem = catalogiItem
		console.log('Active catalog item set to ' + catalogiItem)
	},
	setListingItem(listingItem) {
		this.listingItem = listingItem
		console.log('Active directory item set to ' + listingItem.id)
	},
	// MetaDataObjects
	setMetaDataItem(metaDataItem) {
		this.metaDataItem = metaDataItem
		console.log('Active metadata object set to ' + metaDataItem.id)
	},
	setMetaDataList(metaDataList) {
		this.metaDataList = metaDataList
		console.log('Active metadata lest set')
	},
	// Publications
	setPublicationId(publicationId) {
		this.publicationId = publicationId
		console.log('Active publication id set to ' + publicationId)
	},
	setPublicationItem(publicationItem) {
		this.publicationItem = publicationItem
		console.log('Active publication item set to ' + publicationItem)
	},
	setPublicationDataKey(publicationDataKey) {
		this.publicationDataKey = publicationDataKey
		console.log('Active publication data key set to ' + publicationDataKey)
	},
	setAttachmentId(attachmentId) {
		this.attachmentId = attachmentId
		console.log('Active attachment item set to ' + attachmentId)
	},
	setRefresh(refresh) {
		this.refresh = refresh
		setTimeout(() => (this.refresh = false), 1000)
		console.log('Active attachment item set to ' + refresh)
	},
})
