/* eslint-disable no-console */
export default {
	// The currently active menu item, defaults to '' which triggers the dashboard
	selected: 'dashboard',
	// The currently selected catalogi within 'publications'
	selectedCatalogus: false,
	// The currently active modal, managed trough the state to ensure that only one modal can be active at the same time
	modal: false,
	// The currently active dialog
	dialog: false,
	setSelected(selected) {
		this.selected = selected
		console.log('Active menu item set to ' + selected)
	},
	setSelectedCatalogus(selectedCatalogus) {
		this.selectedCatalogus = selectedCatalogus
		console.log('Active catalogus menu set to ' + selectedCatalogus)
	},
	setModal(modal) {
		this.modal = modal
		console.log('Active modal set to ' + modal)
	},
	setDialog(dialog) {
		this.dialog = dialog
		console.log('Active dialog set to ' + dialog)
	},
}
