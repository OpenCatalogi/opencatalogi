/* eslint-disable no-console */
import { Attachment, Publication } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const usePublicationStore = defineStore('publication', {
	state: () => ({
		publicationItem: false,
		publicationMetaData: false,
		publicationList: [],
		publicationDataKey: false,
		attachmentItem: false,
		attachmentFile: null,
		publicationAttachments: false,
		conceptPublications: [],
		conceptAttachments: [],
	}),
	actions: {
		setPublicationItem(publicationItem) {
			// To prevent forms etc from braking we alway use a default/skeleton object
			this.publicationItem = publicationItem && new Publication(publicationItem)
			console.log('Active publication item set to ' + publicationItem && publicationItem.id)
		},
		setPublicationList(publicationList) {
			this.publicationList = publicationList.map((publicationItem) => new Publication(publicationItem))
			console.log('Active publication item set to ' + publicationList.length)
		},
		async refreshPublicationList(normalSearch = [], advancedSearch = null, sortField = null, sortDirection = null) {
			// @todo this might belong in a service?
			let endpoint = '/index.php/apps/opencatalogi/api/publications'
			const params = new URLSearchParams()
			for (const item of normalSearch) {
				if (item.key && item.value !== undefined) {
					params.append(item.key, item.value)
				}
			}
			if (advancedSearch !== null && advancedSearch !== '') {
				params.append('_search', advancedSearch)
			}
			if (sortField !== null && sortField !== '' && sortDirection !== null && sortDirection !== '') {
				if (sortField === 'Titel') {
					sortField = 'title'
				}
				if (sortField === 'Datum gepubliceerd') {
					sortField = 'published'
				}
				if (sortField === 'Datum aangepast') {
					sortField = 'modified'
				}
				params.append('_order[' + sortField + ']', sortDirection)
			}
			if (params.toString()) {
				endpoint += '?' + params.toString()
			}

			return fetch(
				endpoint,
				{
					method: 'GET',
				},
			)
				.then(
					(response) => {
						response.json().then(
							(data) => {
								this.setPublicationList(data?.results)
								return data
							},
						)
					},
				)
				.catch(
					(err) => {
						console.error(err)
						return err
					},
				)
		},
		getPublicationAttachments(publicationId) {
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationId}/attachments`,
				{
					method: 'GET',
				},
			)
				.then(
					(response) => {
						response.json().then(
							(data) => {
								this.publicationAttachments = data.results.map(
									(attachmentItem) => new Attachment(attachmentItem),
								)
								return data
							},
						)
					},
				)
				.catch(
					(err) => {
						console.error(err)
						return err
					},
				)
		},
		async getConceptPublications(options = {}) { // @todo this might belong in a service?
			const response = await fetch('/index.php/apps/opencatalogi/api/publications?status=concept',
				{ method: 'GET' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((publicationItem) => new Publication(publicationItem))

			options.doNotSetStore !== true && (this.conceptPublications = data)

			return { response, data }
		},
		async getConceptAttachments(options = {}) { // @todo this might belong in a service?
			const response = await fetch('/index.php/apps/opencatalogi/api/attachments?status=concept',
				{ method: 'GET' },
			)

			const rawData = await response.json()

			const data = rawData.results.map((attachmentItem) => new Attachment(attachmentItem))

			options.doNotSetStore !== true && (this.conceptAttachments = data)

			return { response, data }
		},
		// @todo why does the following run through the store? -- because its impossible with props, and its vital information for the modal.
		setPublicationDataKey(publicationDataKey) {
			this.publicationDataKey = publicationDataKey
			console.log('Active publication data key set to ' + publicationDataKey)
		},
		setAttachmentItem(attachmentItem) {
			this.attachmentItem = attachmentItem && new Attachment(attachmentItem)
			console.log('Active attachment item set to ' + attachmentItem)
		},
		setAttachmentFile(files) {
			this.attachmentFile = files
			console.log('Active attachment files set to ' + files)
		},
		setPublicationMetaData(metaData) {
			this.publicationMetaData = metaData
		},
	},
	setPublicationList(publicationList) {
		this.publicationList = publicationList.map((publicationItem) => new Publication(publicationItem))
		console.log('Active publication item set to ' + publicationList.length)
	},
	/* istanbul ignore next */ // ignore this for Jest until moved into a service
	async refreshPublicationList(search = null) {
		// @todo this might belong in a service?
		let endpoint = '/index.php/apps/opencatalogi/api/publications'
		if (search !== null && search !== '') {
			endpoint = endpoint + '?_search=' + search
		}
		return fetch(
			endpoint,
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.setPublicationList(data?.results)
					return data
				})
			})
			.catch((err) => {
				console.error(err)
				return err
			})
	},
	/* istanbul ignore next */ // ignore this for Jest until moved into a service
	getPublicationAttachments(publicationId) {
		fetch(
			'/index.php/apps/opencatalogi/api/publications/' + publicationId + '/attachments',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.publicationAttachments = data.results.map(
						(attachmentItem) => new Attachment(attachmentItem),
					)
					return data
				})
			})
			.catch((err) => {
				console.error(err)
				return err
			})
	},
	/* istanbul ignore next */ // ignore this for Jest until moved into a service
	getConceptPublications() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/publications?status=concept',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.conceptPublications = data
					return data
				})
			})
			.catch((err) => {
				console.error(err)
				return err
			})
	},
	/* istanbul ignore next */ // ignore this for Jest until moved into a service
	getConceptAttachments() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/attachments?status=concept',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.conceptAttachments = data
					return data
				})
			})
			.catch((err) => {
				console.error(err)
				return err
			})
	},
	// @todo why does the following run through the store? -- because its impossible with props, and its vital information for the modal.
	setPublicationDataKey(publicationDataKey) {
		this.publicationDataKey = publicationDataKey
		console.log('Active publication data key set to ' + publicationDataKey)
	},
	setAttachmentItem(attachmentItem) {
		this.attachmentItem = attachmentItem && new Attachment(attachmentItem)
		console.log('Active attachment item set to ' + attachmentItem)
	},
	setAttachmentFile(files) {
		this.attachmentFile = files
	},
	setPublicationMetaData(metaData) {
		this.publicationMetaData = metaData
	},
},
)
