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
			if (typeof this.metaDataItem.properties === 'string') {
				this.metaDataItem.properties = JSON.parse(this.metaDataItem.properties)
			}

			this.metaDataItem = new Metadata(metaDataItem)

			console.log('Active metadata object set to ' + metaDataItem.id)
		},
		setMetaDataList(metaDataList) {
			this.metaDataList = metaDataList.map(
				(metadataItem) => new Metadata(metadataItem),
			)
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
			const propertyKeys = this.metaDataItem.properties[property]

			return propertyKeys
		},
	},
})
