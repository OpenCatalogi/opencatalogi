/* eslint-disable no-console */
import { defineStore } from 'pinia'

interface DialogProperties {
    objectType?: string;
    dialogName?: string;
    displayName?: string;
    isMultiple?: boolean;
}

interface NavigationStoreState {
    selected: 'dashboard' | 'publication' | 'catalogi' | 'publicationType' | 'organizations' | 'themes' | 'search' | 'directory' | 'pages' | 'menus' | 'glossary';
    selectedCatalogus: string;
    modal: string;
    dialog: string;
    dialogProperties: DialogProperties | null;
    transferData: string;
}

export const useNavigationStore = defineStore('ui', {
	state: () => ({
		// The currently active menu item, defaults to '' which triggers the dashboard
		selected: 'dashboard',
		// The currently selected catalogi within 'publications'
		selectedCatalogus: null,
		// The currently active modal, managed trough the state to ensure that only one modal can be active at the same time
		modal: null,
		// The currently active dialog
		dialog: null,
		// Properties for the active dialog
		dialogProperties: null,
		// Any data needed in various models, dialogs, views which cannot be transferred through normal means or without writing crappy/excessive code
		transferData: null,
	} as NavigationStoreState),
	actions: {
		setSelected(selected: NavigationStoreState['selected']) {
			this.selected = selected
			console.log('Active menu item set to ' + selected)
		},
		setSelectedCatalogus(selectedCatalogus: string) {
			this.selectedCatalogus = selectedCatalogus
			console.log('Active catalogus menu set to ' + selectedCatalogus)
		},
		setModal(modal: NavigationStoreState['modal']) {
			this.modal = modal
			console.log('Active modal set to ' + modal)
		},
		setDialog(dialog: NavigationStoreState['dialog'], properties?: DialogProperties) {
			this.dialog = dialog
			this.dialogProperties = properties || null
			console.log('Active dialog set to ' + dialog, properties ? 'with properties' : '')
		},
		setTransferData(transferData: NavigationStoreState['transferData']) {
			this.transferData = transferData
		},
		getTransferData(): NavigationStoreState['transferData'] {
			const tempData = this.transferData
			this.transferData = null
			return tempData
		},
	},
})
