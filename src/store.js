// The store script handles app whide variables (or state), for the use of these variables and there governing concepts read the design.md 
import { reactive } from 'vue'

export const store = reactive({
	// The curently active menu item, defaults to '' wich triggers the dashboard
	selected: 'dashboard',
	// The currently active modal, managed trought the state to ensure that only one modal can be active at the same time
	modal: false,
	modalData: [], // optional data to pass to the modal
	// The curently active item (or object) , managed trought the state to ensure that only one modal can be active at the same time
	item: false,
	// Lets add some setters
	setSelected(selected){
		this.selected = selected
		console.log('Active menu item set to ' + selected)
	},
	setModal(modal){
		this.modal = modal
		console.log('Active modal item set to ' + modal)
	},
	setItem(item){
		this.item = item
		console.log('Active object item set to ' + item)
	}
})
