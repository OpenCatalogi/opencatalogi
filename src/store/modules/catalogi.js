/* eslint-disable no-console */
import { Catalogi } from '../../entities/index.js'

export default {
	catalogiItem: false,
	catalogiList: [],
	setCatalogiItem(catalogiItem) {
		this.catalogiItem = new Catalogi(
			catalogiItem.id,
			catalogiItem.name,
			catalogiItem.summary,
			catalogiItem._schema,
			catalogiItem._id,
		)
		console.log('Active catalog item set to ' + catalogiItem.id)
	},
	setCatalogiList(catalogiList) {
		this.catalogiList = catalogiList.map((catalogiItem) => new Catalogi(
			catalogiItem.id,
			catalogiItem.name,
			catalogiItem.summary,
			catalogiItem._schema,
			catalogiItem._id,
		))
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
					this.catalogiList = data.results.map((catalogiItem) => new Catalogi(
						catalogiItem.id,
						catalogiItem.name,
						catalogiItem.summary,
						catalogiItem._schema,
						catalogiItem._id,
					))
				})
			})
			.catch((err) => {
				console.error(err)
			})
	},
}
