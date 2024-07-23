/* eslint-disable no-console */
export default {
	metaDataItem: false,
	metaDataList: [],
	metadataDataKey: false,
	setMetaDataItem(metaDataItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const metaDataDefault = {
			name: '',
			version: '',
			summery: '',
			description: '',
			properties: {},
		}
		this.metaDataItem = { ...metaDataDefault, ...metaDataItem }

		// for backward compatablity
		if (typeof this.metaDataItem.properties === 'string') {
			this.metaDataItem.properties = JSON.parse(this.metaDataItem.properties)
		}

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
}
