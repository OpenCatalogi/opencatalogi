/* eslint-disable no-console */
// The store script handles app whide variables (or state), for the use of these variables and there governing concepts read the design.md
import { reactive } from 'vue'

export const store = reactive({
	// The curently active menu item, defaults to '' wich triggers the dashboard
	selected: 'dashboard',
	// The currently active modal, managed trought the state to ensure that only one modal can be active at the same time
	modal: false,
	// The curetnly active dialog
	dialog: false,
	// The current search term
	search: '',
	searchResults: '',
	// Catlogi
	catalogiItem: false,
	catalogiList: [],
	// Directory
	listingItem: false,
	listingList: [],
	// Metadata
	metaDataItem: false,
	metaDataList: [],
	metadataDataKey: false,
	// Publications
	publicationItem: false,
	publicationList: [],
	// ??
	publicationDataKey: false,
	attachmentItem: false,
	// Lets add some setters
	setSelected(selected) {
		this.selected = selected
		console.log('Active menu item set to ' + selected)
	},
	setModal(modal) {
		this.modal = modal
		console.log('Active modal set to ' + modal)
	},
	setDialog(dialog) {
		this.dialog = dialog
		console.log('Active dialog set to ' + dialog)
	},
	setSearch(search) {
		this.search = search
		console.log('Active search set to ' + search)
	},
	setSearchResults(searchResults) {
		this.searchResults = searchResults
		console.log('Active search set to ' + searchResults)
	},
	getSearchResults() {
		fetch(
			'/index.php/apps/opencatalogi/api/search?_search=' + this.search,
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.searchResults = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
	clearSearch() {
		this.search = ''
	},
	// Catlogi
	setCatalogiItem(catalogiItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const catalogiDefault = { name: '', summery: '' }
		this.catalogiItem = { ...catalogiDefault, ...catalogiItem }
		console.log('Active catalog item set to ' + catalogiItem.id)
	},
	setCatalogiList(catalogiList) {
		this.catalogiList = catalogiList
		console.log('Catalogi list set to ' + catalogiList.length + ' item')
	},
	refreshCatalogiList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/catalogi',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.catalogiList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
	// Directory
	setListingItem(listingItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const listingDefault = {
			name: '',
			url: '',
			summery: '',
			status: '',
			lastSync: '',
		}
		this.listingItem = { ...listingDefault, ...listingItem }
		console.log('Active directory item set to ' + listingItem.id)
	},
	setListingList(listingList) {
		this.listingList = listingList
		console.log('Active directory item set to ' + listingList.length)
	},
	refreshListingList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/directory',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.listingList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
	// MetaDataObjects
	setMetaDataItem(metaDataItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const metaDataDefault = {
			name: '',
			summery: '',
		}
		this.metaDataItem = { ...metaDataDefault, ...metaDataItem }
		console.log('Active metadata object set to ' + metaDataItem.id)
	},
	setMetaDataList(metaDataList) {
		this.metaDataList = metaDataList
		console.log('Active metadata lest set')
	},
	refreshMetaDataList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/metadata',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.metaDataList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
	setMetadataDataKey(metadataDataKey) {
		this.metadataDataKey = metadataDataKey
		console.log('Active metadata data key set to ' + metadataDataKey)
	},
	getMetadataPropertyKeys(property) {
		const defaultKeys = {
			type: '',
			description: '',
			required: false,
			default: false,
			format: '',
			$ref: '',
			cascadeDelete: false,
			maxDate: '',
			exclusiveMinimum: 0,
		}

		const propertyKeys = JSON.parse(this.metaDataItem.properties)[property]

		return { ...defaultKeys, ...propertyKeys }
	},
	// Publications
	setPublicationItem(publicationItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const publicationDefault = {
			title: '',
			description: '',
			catalogi: {},
			metaData: {},
			license: '',
			modified: '',
			published: '',
			status: '',
			featured: '',
			publication: '',
			portal: '',
			category: '',
			image: '',
		 }
		this.publicationItem = { ...publicationDefault, ...publicationItem }
		console.log('Active publication item set to ' + publicationItem.id)
	},
	setPublicationList(publicationList) {
		this.publicationList = publicationList
		console.log('Active publication item set to ' + publicationList.length)
	},
	refreshPublicationList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/publications',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.publicationList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
	// @todo why does the following run through the store? -- because its impossible with props, and its vital information for the modal.
	setPublicationDataKey(publicationDataKey) {
		this.publicationDataKey = publicationDataKey
		console.log('Active publication data key set to ' + publicationDataKey)
	},
	setAttachmentItem(attachmentItem) {
		this.attachmentItem = attachmentItem
		console.log('Active attachment item set to ' + attachmentItem)
	},
})
