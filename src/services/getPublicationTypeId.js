export const getPublicationTypeId = (url) => {
	const publicationTypeId = url.substring(url.lastIndexOf('/') + 1, url.length)
	return publicationTypeId
}
