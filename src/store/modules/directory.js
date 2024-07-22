/* eslint-disable no-console */
export default {
	listingItem: false,
	listingList: [],
	setListingItem(listingItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const listingDefault = {
			name: '',
			url: '',
			summery: '',
			status: '',
			lastSync: '',
		}
		this.listingItem = { ...listingDefault, ...listingItem }
		console.log('Active directory item set to ' + listingItem.id)
	},
	setListingList(listingList) {
		this.listingList = listingList
		console.log('Active directory item set to ' + listingList.length)
	},
	refreshListingList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/directory',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.listingList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
}
