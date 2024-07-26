/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationStore } from './publication.js'
import { mockPublicationsData } from '../../entities/publication'

describe('Metadata Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication item correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(mockPublicationsData()[0])

		expect(store.publicationItem).toEqual(mockPublicationsData()[0])
	})

	it('sets publication list correctly', () => {
		const store = usePublicationStore()

		store.setPublicationList(mockPublicationsData())

        console.log(JSON.stringify(store.publicationList, null, 2));  // Pretty print the JSON object
        console.log(JSON.stringify(mockPublicationsData(), null, 2));  // Pretty print the JSON object

		expect(store.publicationList[0].title).toEqual(mockPublicationsData()[0].title)
	})

	// TODO: fix this
	it('set publication data.data property key correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(mockPublicationsData()[0])
		store.setPublicationDataKey('contactPoint')

		expect(store.publicationItem).toEqual(mockPublicationsData()[0])
		expect(store.publicationDataKey).toBe('contactPoint')
	})

	// TODO: fix this
	it('set attachment item correctly', () => {
		const store = usePublicationStore()

		store.setAttachmentItem(mockPublicationsData()[0].attachments[0])

		expect(store.attachmentItem.id).toBe(mockPublicationsData()[0].attachments[0].id)
		expect(store.attachmentItem.reference).toBe('') // some of these are '' because its not in the test data
		expect(store.attachmentItem.title).toBe(mockPublicationsData()[0].attachments[0].title)
		expect(store.attachmentItem.summary).toBe(mockPublicationsData()[0].attachments[0].summary)
		expect(store.attachmentItem.description).toBe(mockPublicationsData()[0].attachments[0].description)
		expect(store.attachmentItem.labels).toEqual([])
		expect(store.attachmentItem.accessURL).toBe(mockPublicationsData()[0].attachments[0].accessURL)
		expect(store.attachmentItem.downloadURL).toBe(mockPublicationsData()[0].attachments[0].downloadURL)
		expect(store.attachmentItem.type).toBe(mockPublicationsData()[0].attachments[0].type)
		expect(store.attachmentItem.extension).toBe('')
		expect(store.attachmentItem.size).toBe(0)
		// attachmentItem.anonymization
		expect(store.attachmentItem.anonymization.anonymized).toBe(mockPublicationsData()[0].attachments[0].anonymization.anonymized)
		// attachmentItem.language
		expect(store.attachmentItem.language.code).toBe(mockPublicationsData()[0].attachments[0].language.code)
		expect(store.attachmentItem.language.level).toBe(mockPublicationsData()[0].attachments[0].language.level)
		expect(store.attachmentItem.version_of).toBe(mockPublicationsData()[0].attachments[0].version_of)
		expect(store.attachmentItem.hash).toBe(mockPublicationsData()[0].attachments[0].hash)
		expect(store.attachmentItem.published).toBe(mockPublicationsData()[0].attachments[0].published)
		expect(store.attachmentItem.modified).toBe(mockPublicationsData()[0].attachments[0].modified)
		expect(store.attachmentItem.license).toBe(mockPublicationsData()[0].attachments[0].license)
	})
})