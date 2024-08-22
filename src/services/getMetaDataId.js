export const getMetaDataId = (url) => {
	const metaDataId = url.substring(url.lastIndexOf('/') + 1, url.length)
	return metaDataId
}
