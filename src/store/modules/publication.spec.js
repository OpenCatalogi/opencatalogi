/* eslint-disable no-console */
import { setActivePinia, createPinia } from 'pinia'

import { usePublicationStore } from './publication.js'

describe('Metadata Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets publication item correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(publicationList[0])

		expect(store.publicationItem).toEqual(publicationList[0])
	})

	it('sets publication list correctly', () => {
		const store = usePublicationStore()

		store.setPublicationList(publicationList)

		expect(store.publicationList).toEqual(publicationList)
	})

	it('set publication data.data property key correctly', () => {
		const store = usePublicationStore()

		store.setPublicationItem(publicationList[0])
		store.setPublicationDataKey('contactPoint')

		expect(store.publicationItem).toEqual(publicationList[0])
		expect(store.publicationDataKey).toBe('contactPoint')
	})

	it('set attachment item correctly', () => {
		const store = usePublicationStore()

		store.setAttachmentItem(publicationList[0].attachments[0])

		expect(store.attachmentItem.id).toBe(publicationList[0].attachments[0].id)
		expect(store.attachmentItem.reference).toBe('') // some of these are '' because its not in the test data
		expect(store.attachmentItem.title).toBe(publicationList[0].attachments[0].title)
		expect(store.attachmentItem.summary).toBe('')
		expect(store.attachmentItem.description).toBe(publicationList[0].attachments[0].description)
		expect(store.attachmentItem.labels).toEqual([])
		expect(store.attachmentItem.accessURL).toBe(publicationList[0].attachments[0].accessURL)
		expect(store.attachmentItem.downloadURL).toBe(publicationList[0].attachments[0].downloadURL)
		expect(store.attachmentItem.type).toBe(publicationList[0].attachments[0].type)
		expect(store.attachmentItem.extension).toBe('')
		expect(store.attachmentItem.size).toBe(0)
		// attachmentItem.anonymization
		expect(store.attachmentItem.anonymization.anonymized).toBe(false)
		expect(store.attachmentItem.anonymization.results).toBe('')
		// attachmentItem.language
		expect(store.attachmentItem.language.code).toBe('')
		expect(store.attachmentItem.language.level).toBe('')
		expect(store.attachmentItem.version_of).toBe(false)
		expect(store.attachmentItem.hash).toBe(false)
		expect(store.attachmentItem.published).toBe(publicationList[0].attachments[0].published)
		expect(store.attachmentItem.modified).toBe(publicationList[0].attachments[0].modified)
		expect(store.attachmentItem.license).toBe(publicationList[0].attachments[0].license)
	})
})

const publicationList = [{
	id: '09c2096c-3404-4f60-ae38-0d8e4ed7051d',
	title: 'Woningoppervlaktes',
	description: 'Woningoppervlaktes geeft het gebruiksoppervlakte aan woningen per gebied, geclassificeerd ten behoeve van het bepalen van de benodigde parkeercapaciteit.',
	catalogi: '7a048bfd-210f-4e93-a1e8-5aa9261740b7',
	metaData: '468f440f-7af0-453a-8d5f-ffe644ab0673',
	data: {
		id: '33f88aa9-6ac0-4f6c-967e-ecf787fd6a3d',
		reference: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
		title: 'Input voor OpenCatalogi',
		summary: 'Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x',
		category: 'Dataset',
		portal: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
		published: '2020-04-07',
		modified: '2020-12-29',
		featured: false,
		organization: {
			id: '9d8309c4-a244-4482-95db-e1488c643bb8',
			title: 'Gemeente Rotterdam',
			image: 'https://avatars.githubusercontent.com/u/93453128?v=4',
		},
		schema: 'https://openwoo.app/schemas/metadata.dcat_catalog.schema.json',
		status: 'published',
		license: 'CC0 1.0',
		attachments: [
			{
				id: 'ba9e5f64-f6ee-4c62-99bd-e9176372f4c2',
				title: 'woningoppervlaktes feature layer',
				description: 'ESRI feature layer met woningoppervlaktes per TIR-buurt en per TIR-blok.',
				license: 'notspecified',
				type: 'API',
				published: '24-12-2020',
				modified: '30 december 2020, 11:55 (UTC+01:00)',
				accessURL: 'https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Woningoppervlaktes/FeatureServer',
				downloadURL: 'https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Woningoppervlaktes/FeatureServer',
			},
		],
		attachment_count: 1,
		themes: [
			'SODA',
			'kennisloods',
			'mobiliteit',
			'oppervlakte',
			'oppervlaktes',
			'parkeercapaciteit',
			'parkeren',
			'soda verblijfsobject',
			'verblijfsobjecten',
			'woning',
			'woningen',
			'woningoppervlakte',
			'woningoppervlaktes',
		],
		data: {
			spatial: '[55500,428647,101033,447000]',
			contactPoint: {
				name: 'gemeente Rotterdam, Stadsontwikkeling, SODA',
				email: 'dataSO@rotterdam.nl',
			},
			qualifiedAttribution: {
				responsible: {
					name: 'gemeente Rotterdam, Stadsontwikkeling, SODA',
					email: 'dataSO@rotterdam.nl',
				},
				role: {
					name: 'beheerder',
				},
			},
			accrualPeriodicity: 'onregelmatig',
		},
		anonymization: {
			anonymized: true,
		},
		language: {
			code: 'nl-nl',
			level: 'A1',
		},
	},
	attachments: [
		{
			id: 'ba9e5f64-f6ee-4c62-99bd-e9176372f4c2',
			title: 'woningoppervlaktes feature layer',
			description: 'ESRI feature layer met woningoppervlaktes per TIR-buurt en per TIR-blok.',
			license: 'notspecified',
			type: 'API',
			published: '24-12-2020',
			modified: '30 december 2020, 11:55 (UTC+01:00)',
			accessURL: 'https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Woningoppervlaktes/FeatureServer',
			downloadURL: 'https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Woningoppervlaktes/FeatureServer',
		},
	],
	license: 'notspecified',
	modified: '2020-12-29',
	published: '2020-04-07',
	status: 'published',
	featured: '',
	publication: '',
	portal: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
	category: 'Dataset',
	image: 'https://dev.opencatalogi.nl/static/logo_OpenCatalogi-8b1b0a001c3f37dae4d3f69b5964ec72.png',
},
{
	_id: '0a06cbd2-3509-46c3-b0e3-1b9132b95f60',
	title: 'Kubus bijzondere bijstand',
	description: 'Het rapport geeft de boekingen van de bijzondere bijstand aan.',
	catalogi: '7a048bfd-210f-4e93-a1e8-5aa9261740b7',
	metaData: '468f440f-7af0-453a-8d5f-ffe644ab0673',
	data: {
		id: '33f88aa9-6ac0-4f6c-967e-ecf787fd6a3d',
		reference: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
		title: 'Input voor OpenCatalogi',
		summary: 'Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x',
		category: 'Dataset',
		portal: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
		published: '2020-04-07',
		modified: '2020-12-29',
		featured: false,
		organization: {
			id: '9d8309c4-a244-4482-95db-e1488c643bb8',
			title: 'Gemeente Rotterdam',
			image: 'https://avatars.githubusercontent.com/u/93453128?v=4',
		},
		schema: 'https://openwoo.app/schemas/metadata.dcat_catalog.schema.json',
		status: 'published',
		license: 'CC0 1.0',
		attachment_count: 1,
		themes: [
			'bijzondere bijstand',
		],
		data: {
			spatial: '[55500,428647,101033,447000]',
			contactPoint: {
				name: 'Rob Sinon',
				email: 'r.sinon@rotterdam.nl',
			},
			qualifiedAttribution: {
				responsible: {
					name: 'Rob Sinon',
					email: 'r.sinon@rotterdam.nl',
				},
				role: {
					name: 'Informatie-adviseur',
				},
			},
			accrualPeriodicity: 'onregelmatig',
		},
		anonymization: {
			anonymized: true,
		},
		language: {
			code: 'nl-nl',
			level: 'A1',
		},
	},
	attachments: [
		{
			id: 'ba9e5f64-f6ee-4c62-99bd-e9176372f4c2',
			title: 'Kubus bijzondere bijstand',
			description: 'Het rapport geeft de boekingen van de bijzondere bijstand aan.',
			license: 'notspecified',
			type: 'text/csv',
			published: '07-04-2021',
			modified: '7 april 2021, 11:11 (UTC+02:00)',
			accessURL: 'https://catalogus-rotterdam.dataplatform.nl/dataset/32730756-a64f-437d-ac4d-2c77a05c7afe/resource/90d8366f-e3f7-43cb-800e-0ab6c934a82d/download/kubus-bijzondere-bijstand.csv',
			downloadURL: 'https://rio.rotterdam.nl/Project/SODAStadsOntwikkelingData/Pages/ThoZIFnen0KbY6eBjPvh-A',
		},
	],
	license: 'notspecified',
	modified: '2021-04-07',
	published: '2021-04-07',
	status: 'published',
	featured: '',
	publication: '',
	portal: 'https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken',
	category: 'Dataset',
	image: 'https://dev.opencatalogi.nl/static/logo_OpenCatalogi-8b1b0a001c3f37dae4d3f69b5964ec72.png',
	_schema: 'publication',
	id: '0a06cbd2-3509-46c3-b0e3-1b9132b95f60',
}]
