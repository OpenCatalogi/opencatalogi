/* eslint-disable no-console */
import { defineStore } from 'pinia'
import { Metadata } from '../../entities/index.js'

export const useMetadataStore = defineStore('metadata', {
	state: () => ({
		metaDataItem: false,
		metaDataList: [],
		metadataDataKey: false,
	}),
	actions: {
		setMetaDataItem(metaDataItem) {
			this.metaDataItem = metaDataItem && new Metadata(metaDataItem)

			// for backward compatibility
			if (typeof this.metaDataItem?.properties === 'string') {
				this.metaDataItem.properties = JSON.parse(this.metaDataItem.properties)
			}

			console.log('Active metadata object set to ' + metaDataItem && metaDataItem.id)
		},
		setMetaDataList(metaDataList) {
			this.metaDataList = metaDataList.map(
				(metadataItem) => new Metadata(metadataItem),
			)
			console.log('Active metadata lest set')
		},
		async refreshMetaDataList(search = null) { 
            // @todo this might belong in a service?
            let endpoint = '/index.php/apps/opencatalogi/api/metadata'
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
						this.metaDataList = data.results.map(
							(metadataItem) => new Metadata(metadataItem),
						)
						return data
					})
				})
				.catch((err) => {
					console.error(err)
					return err
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
				format: '',
				maxDate: '',
				required: false,
				default: false,
				$ref: '', // $ref should probably be removed as it is not mentioned in the schema
				cascadeDelete: false,
				exclusiveMinimum: 0,
			}

			const propertyKeys = this.metaDataItem.properties[property]

			return { ...defaultKeys, ...propertyKeys }
		},
	},
})
