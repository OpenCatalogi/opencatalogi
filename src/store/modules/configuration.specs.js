/* eslint-disable no-console */
import { createPinia, setActivePinia } from 'pinia';

import { Configuration } from '../../entities/index.js';
import { useConfigurationStore } from './configuration.js';

describe(
    'Metadata Store', () => {
        beforeEach(
        () => {
                setActivePinia(createPinia())
            }
    )

    it(
        'sets configuration item correctly', () => {
        const store = useConfigurationStore()

            store.setConfigurationItem(testData[0])

            expect(store.configurationItem).toBeInstanceOf(Configuration)
            expect(store.configurationItem).toEqual(testData[0])
            expect(store.configurationItem.validate()).toBe(true)

            store.setConfigurationItem(testData[1])

            expect(store.configurationItem).toBeInstanceOf(Configuration)
            expect(store.configurationItem).not.toEqual(testData[1])
            expect(store.configurationItem.validate()).toBe(true)

            store.setConfigurationItem(testData[2])

            expect(store.configurationItem).toBeInstanceOf(Configuration)
            expect(store.configurationItem).toEqual(testData[2])
            expect(store.configurationItem.validate()).toBe(false)
        }
    )

    it(
        'sets configuration list correctly', () => {
        const store = useConfigurationStore()

            store.setConfigurationItem(testData)

            expect(store.configurationList).toHaveLength(testData.length)

            expect(store.configurationList[0]).toBeInstanceOf(Configuration)
            expect(store.configurationList[0]).toEqual(testData[0])
            expect(store.configurationList[0].validate()).toBe(true)

            expect(store.configurationList[1]).toBeInstanceOf(Configuration)
            expect(store.configurationList[1]).not.toEqual(testData[1])
            expect(store.configurationList[1].validate()).toBe(true)

            expect(store.configurationList[2]).toBeInstanceOf(Configuration)
            expect(store.configurationList[2]).toEqual(testData[2])
            expect(store.configurationList[2].validate()).toBe(false)
        }
    )
    }
)

const testData = [
    { // full data
        id: '1',
        reference: 'ref1',
        title: 'test 1',
        summary: 'a short form summary',
        description: 'a really really long description about this catalogus',
        image: 'https://example.com/image.jpg',
        category: 'category1',
        portal: 'portal1',
        catalogi: 'catalogi1',
        metaData: 'meta1',
        publicationDate: '2024-01-01',
        modified: '2024-01-02',
        featured: true,
        organisation: [{ name: 'Org1' }],
        data: [{ key: 'value1' }],
        attachments: ['attachment1'],
        attachmentCount: 1,
        schema: 'schema1',
        status: 'status1',
        license: 'MIT',
        themes: 'theme1',
        anonymization: { anonymized: 'yes', results: 'success' },
        language: { code: 'en', level: 'native' },
    },
    { // partial data
        id: '2',
        reference: 'ref2',
        title: 'test 2',
        summary: 'a short form summary',
        description: 'a really really long description about this catalogus',
        image: 'https://example.com/image.jpg',
        category: 'category2',
        portal: 'portal2',
        catalogi: 'catalogi2',
        metaData: 'meta2',
        publicationDate: '2024-01-01',
        modified: '2024-01-02',
        featured: true,
        organisation: [{ name: 'Org1' }],
        data: [{ key: 'value1' }],
        attachments: ['attachment1'],
        attachmentCount: 1,

        themes: 'theme1',
        anonymization: { anonymized: 'yes', results: 'success' },
        language: { code: 'en', level: 'native' },
    },
    { // invalid data
        id: '3',
        reference: 'ref3',
        title: '',
        summary: 'a short form summary',
        description: 'a really really long description about this catalogus',
        image: 'https://example.com/image.jpg',
        category: 'category3',
        portal: 'portal3',
        catalogi: 'catalogi3',
        metaData: 'meta3',
        publicationDate: '2024-01-01',
        modified: '2024-01-02',
        featured: true,
        organisation: [{ name: 'Org1' }],
        data: [{ key: 'value1' }],
        attachments: ['attachment1'],
        attachmentCount: 1,
        schema: 'schema1',
        status: 'status1',
        license: 'MIT',
        themes: 'theme1',
        anonymization: { anonymized: 'yes', results: 'success' },
        language: { code: 'en', level: 'native' },
    },
    ]
