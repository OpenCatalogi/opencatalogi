/* eslint-disable @typescript-eslint/no-explicit-any */
/* eslint-disable no-console */
import { isRef, Ref } from 'vue'
import pinia from '../../pinia'
import { Attachment, Publication, TAttachment, TPublication } from '../../entities/index.js'
import { defineStore } from 'pinia'
import axios, { AxiosResponse } from 'axios'

const apiEndpoint = '/index.php/apps/opencatalogi/api/objects/publication'

interface Options {
    /**
     * Do not save the received item to the store, this can be enabled if API calls get run in a loop
     */
    doNotSetStore?: boolean
}

interface PublicationStoreState {
    publicationItem: Publication;
    publicationPublicationType: string;
    publicationList: Publication[];
    publicationDataKey: string;
    attachmentItem: Attachment;
    attachmentFile: object;
    publicationAttachments: Attachment[];
    conceptPublications: Publication[];
    conceptAttachments: Attachment[];
}

export const usePublicationStore = defineStore('publication', {
	state: () => ({
		publicationItem: null,
		publicationPublicationType: null,
		publicationList: [],
		publicationDataKey: null,
		attachmentItem: null,
		attachmentFile: null,
		publicationAttachments: null,
		conceptPublications: [],
		conceptAttachments: [],
		tagsList: [],
		currentPage: null,
		limit: null,
	} as unknown as PublicationStoreState),
	actions: {
		setPublicationItem(publicationItem: Publication | TPublication) {
			// To prevent forms etc from braking we alway use a default/skeleton object
			this.publicationItem = publicationItem && new Publication(publicationItem)
			console.log('Active publication item set to ' + publicationItem && publicationItem.id)
		},
		setPublicationList(publicationList: Publication[] | TPublication[]) {
			this.publicationList = publicationList.map((publicationItem) => new Publication(publicationItem))
			console.log('Lenght of publication list set to ' + publicationList.length)
		},
		setTagsList(tagsList: string[]) {
			this.tagsList = tagsList
		},
		async refreshPublicationList(
			// eslint-disable-next-line @typescript-eslint/no-explicit-any
			normalSearch: { [key: string]: any }[] = [],
			advancedSearch: string = null,
			sortField: string = null,
			sortDirection: string = null,
		) {
			// @todo this might belong in a service?
			let endpoint = apiEndpoint
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

			return fetch(endpoint,
				{ method: 'GET' },
			)
				.then((response) => {
					return response.json().then((data) => {
						this.setPublicationList(data?.results)
						return data
					})

				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		/* istanbul ignore next */
		async getOnePublication(id: number, options: Options = {}) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'get' },
			)

			const data = new Publication(await response.json())

			options.doNotSetStore !== true && this.setPublicationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async addPublication(item: Publication) {
			if (!(item instanceof Publication)) {
				throw Error('Please pass a Publication item from the Publication class')
			}

			const validateResult = item.validate()
			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(apiEndpoint,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Publication(await response.json())

			this.refreshPublicationList()
			this.setPublicationItem(data)

			// dynamic import the navigationStore to avoid circular imports
			const { useNavigationStore } = await import('../modules/navigation')
			const navigationStore = useNavigationStore(pinia)
			navigationStore.setSelectedCatalogus(data?.catalog ?? data?.catalog)

			return { response, data }
		},
		/* istanbul ignore next */
		async editPublication(item: Publication) {
			if (!(item instanceof Publication)) {
				throw Error('Please pass a Publication item from the Publication class')
			}

			const validateResult = item.validate()

			if (!validateResult.success) {
				throw Error(validateResult.error.issues[0].message)
			}

			const response = await fetch(
				`${apiEndpoint}/${item.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(validateResult.data),
				},
			)

			const data = new Publication(await response.json())

			this.refreshPublicationList()
			this.setPublicationItem(data)

			return { response, data }
		},
		/* istanbul ignore next */
		async deletePublication(id: number) {
			if (!id) {
				throw Error('Passed id is falsy')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}`,
				{ method: 'DELETE' },
			)

			this.refreshPublicationList()
			this.setPublicationItem(false)

			return { response }
		},
		/* istanbul ignore next */
		async downloadPublication(id: number, title: string, type: 'pdf' | 'zip') {
			if (!id) {
				throw Error('Passed id is falsy')
			}
			if (!type) {
				throw Error('Passed type is pdf or zip')
			}

			const response = await fetch(
				`${apiEndpoint}/${id}/download`,
				{
					method: 'GET',
					headers: {
						Accept: `application/${type}`,
					},
				},
			)

			const blob = await response.blob()

			const download = () => {
				const url = window.URL.createObjectURL(new Blob([blob]))
				const link = document.createElement('a')
				link.href = url

				link.setAttribute('download', `${title}.${type.toLowerCase()}`)
				document.body.appendChild(link)
				link.click()
			}

			return { response, blob, download }
		},
		// ################################
		// ||                            ||
		// ||        ATTACHMENTS         ||
		// ||                            ||
		// ################################
		/* istanbul ignore next */
		async getPublicationAttachments(id: number, options: any = {}) {
			if (!id) {
				throw Error('Passed publication id is falsy')
			}

			let endpoint = `${apiEndpoint}/${id}/files`

			const params = []

			if (options.page) {
				params.push('_page=' + options.page)
			}
			if (options.limit) {
				params.push('_limit=' + options.limit)
			}

			if (params.length > 0) {
				endpoint += '?' + params.join('&')
			}

			const response = await fetch(
				endpoint,
				{ method: 'GET' },
			)

			const rawData = await response.json()

			// const data = rawData.results.map(
			// (attachmentItem: TAttachment) => new Attachment(attachmentItem),
			// )

			this.publicationAttachments = rawData

			return { response, rawData }
		},

		/**
		 * Creates a publication attachment.
		 * @param files - The files to create the attachment for.
		 * @param reset - The reset function to call.
		 * @param share - Whether the attachment should be shared.
		 * @return {Promise<AxiosResponse<any, any>>} The response from the API.
		 */
		async createPublicationAttachment(files: Ref<any> | any[], reset: any, share: boolean = false): Promise<AxiosResponse<any, any>> {
			if (!files) {
				throw Error('No files to import')
			}
			if (!reset) {
				throw Error('No reset function to call')
			}

			const formData = new FormData()

			// Flatten and format the files and tags
			if (isRef(files)) files = files.value as any[]

			// At this point, files should be an array.
			if (!Array.isArray(files)) {
				throw new Error('Files is not an array')
			}

			files.forEach((file: any) => {
				formData.append('files[]', file)
				if (file.tags) {
					formData.append('tags[]', file.tags.join(','))
				}
				formData.append('share', share.toString())
			})

			return await axios.post(
				`/index.php/apps/opencatalogi/api/objects/publication/${this.publicationItem.id}/filesMultipart`,
				formData,
				{
					headers: {
						'Content-Type': 'multipart/form-data',
					},
				},
			)
				.then((response) => {
					console.info('Importing files:', response.data)
					return response
				})
				.catch((err) => {
					console.error('Error importing files:', err)
					throw err
				})
		},

		/**
		 * Deletes a file from a publication.
		 * @param id - The id of the publication.
		 * @param filePath - The path of the file to delete.
		 * @return {Promise<AxiosResponse<any, any>>} The response from the API.
		 */
		async deleteFile(id: number, filePath: string): Promise<AxiosResponse<any, any>> {
			return await axios.delete(
				`/index.php/apps/opencatalogi/api/objects/publication/${id}/files/${filePath}`,
			)
				.then((response) => {
					console.info('Deleting file:', response.data)
					return response
				})
				.catch((err) => {
					console.error('Error deleting file:', err)
					throw err
				})
		},
		/**
		 * Deletes a file from a publication.
		 * @param id - The id of the publication.
		 * @param filePath - The path of the file to delete.
		 * @return {Promise<AxiosResponse<any, any>>} The response from the API.
		 */
		async publishFile(id: number, filePath: string): Promise<AxiosResponse<any, any>> {
			return await axios.post(
				`/index.php/apps/opencatalogi/api/objects/publication/${id}/publish/files/${filePath}`,
			)
				.then((response) => {
					console.info('Publishing file:', response.data)
					return response
				})
				.catch((err) => {
					console.error('Error publishing file:', err)
					throw err
				})
		},
		/**
		 * Deletes a file from a publication.
		 * @param id - The id of the publication.
		 * @param filePath - The path of the file to delete.
		 * @return {Promise<AxiosResponse<any, any>>} The response from the API.
		 */
		async depublishFile(id: number, filePath: string): Promise<AxiosResponse<any, any>> {
			return await axios.post(
				`/index.php/apps/opencatalogi/api/objects/publication/${id}/files/depublish/${filePath}`,
			)
				.then((response) => {
					console.info('Depublishing file:', response.data)
					return response
				})
				.catch((err) => {
					console.error('Error depublishing file:', err)
					throw err
				})
		},
		/**
		 * Edits the tags of a file.
		 * @param id - The id of the publication.
		 * @param filePath - The path of the file to edit.
		 * @param tags - The tags to edit.
		 * @return {Promise<AxiosResponse<any, any>>} The response from the API.
		 */
		async editTags(id: number, filePath: string, tags: string[]): Promise<AxiosResponse<any, any>> {

			const formData = new FormData()
			tags && tags.forEach((tag) => {
				formData.append('tags[]', tag)
			})

			return await axios.post(`/index.php/apps/opencatalogi/api/objects/publication/${id}/files/${filePath}`,
				formData,
				{
					headers: {
						'Content-Type': 'multipart/form-data',
					},
				})
				.then((response) => {
					console.info('Editing tags:', response.data)
					return response
				})
				.catch((err) => {
					console.error('Error editing tags:', err)
					throw err
				})
		},

		getConceptPublications() { // @todo this might belong in a service?
			fetch('/index.php/apps/opencatalogi/api/publications?status=Concept',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.conceptPublications = data.results.map((publicationItem: TPublication) => new Publication(publicationItem))
						return data
					})
				})
				.catch((err) => {
					console.error(err)
					return err
				})
		},
		async getConceptAttachments() { // @todo this might belong in a service?
			const response = await fetch('/index.php/apps/opencatalogi/api/attachments?status=Concept', {
				method: 'GET',
			})

			const data = (await response.json()).results

			const entities = data.map((attachmentItem: TAttachment) => new Attachment(attachmentItem))

			this.conceptAttachments = entities

			return { response, data, entities }
		},

		async getTags() {
			const response = await fetch(
				'/index.php/apps/opencatalogi/api/tags',
				{ method: 'get' },
			)
			const data = await response.json()

			const filteredData = data.filter((tag: string) => !tag.startsWith('object:'))

			this.setTagsList(filteredData)
			return { response, data }
		},
		setPublicationDataKey(publicationDataKey: string) {
			this.publicationDataKey = publicationDataKey
			console.log('Active publication data key set to ' + publicationDataKey)
		},
		setAttachmentItem(attachmentItem: Attachment | TAttachment) {
			this.attachmentItem = attachmentItem && new Attachment(attachmentItem)
			console.log('Active attachment item set to ' + attachmentItem)
		},
		setAttachmentFile(files: object) {
			this.attachmentFile = files
			console.log('Active attachment files set to ' + files)
		},
		setPublicationPublicationType(publicationType: string) {
			this.publicationPublicationType = publicationType
		},
		setCurrentPage(currentPage: number) {
			this.currentPage = currentPage
		},
		setLimit(limit: number) {
			this.limit = limit
		},
	},
})
