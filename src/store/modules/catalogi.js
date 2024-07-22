/* eslint-disable no-console */
export default {
	catalogiItem: false,
	catalogiList: [],
	setCatalogiItem(catalogiItem) {
		// To prevent forms etc from braking we alway use a default/skeleton object
		const catalogiDefault = {
			name: '',
			summery: '',
			description: '',
		}
		this.catalogiItem = { ...catalogiDefault, ...catalogiItem }
		console.log('Active catalog item set to ' + catalogiItem.id)
	},
	setCatalogiList(catalogiList) {
		this.catalogiList = catalogiList
		console.log('Catalogi list set to ' + catalogiList.length + ' item')
	},
	refreshCatalogiList() { // @todo this might belong in a service?
		fetch(
			'/index.php/apps/opencatalogi/api/catalogi',
			{
				method: 'GET',
			},
		)
			.then((response) => {
				response.json().then((data) => {
					this.catalogiList = data
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
}
