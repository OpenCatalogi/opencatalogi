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
			// for backward compatibility
			if (!!metaDataItem && typeof metaDataItem?.properties === 'string') {
				metaDataItem.properties = JSON.parse(metaDataItem.properties)
			}

			this.metaDataItem = metaDataItem && new Metadata(metaDataItem)

			console.log('Active metadata object set to ' + metaDataItem && metaDataItem.id)
		},
		setMetaDataList(metaDataList) {
			this.metaDataList = metaDataList.map(
				(metadataItem) => new Metadata(metadataItem),
			)
			console.log('Active metadata lest set')
		},
		/* istanbul ignore next */ // ignore this for Jest until moved into a service
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
	},
})
