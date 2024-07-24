/* eslint-disable no-console */
import { defineStore } from 'pinia'

export const usePublicationStore = defineStore('publication', {
	state: () => ({
		publicationItem: false,
		publicationList: [],
		publicationDataKey: false,
		attachmentItem: false,
		publicationAttachments: [],
		conceptPublications: [],
		conceptAttachments: [],
	}),
	actions: {
		setPublicationItem(publicationItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
			const publicationDefault = {
				title: '',
				description: '',
				catalogi: '',
				metaData: '',
				license: '',
				data: {},
				modified: '',
				published: '',
				status: 'concept',
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
						return data
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		getPublicationAttachments(publication) { // @todo this might belong in a service?
			fetch(
				'/index.php/apps/opencatalogi/api/attachments',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publicationAttachments = data
						return data
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
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
		// To prevent forms etc from braking we alway use a default/skeleton object
			const attachmentDefault = {
				reference: '',
				title: '',
				summary: '',
				description: '',
				labels: [],
				accessURL: '',
				downloadURL: '',
				type: '',
				extension: '',
				size: 0,
				anonymization: {
					anonymized: false,
					results: '',
				},
				language: {
					code: '',
					level: '',
				},
				version_of: false,
				hash: false,
				published: '',
				modified: '',
				license: '',
		  }
			this.attachmentItem = { ...attachmentDefault, ...attachmentItem }
			console.log('Active attachment item set to ' + attachmentItem)
			console.log(attachmentItem)
		},
	},
})
