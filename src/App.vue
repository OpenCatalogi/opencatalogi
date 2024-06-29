<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="dashboard" 
			:selected="selected"
			:item="item"
			:modal="modal"
			@selected="setSelected"
			@item="setItem" 
			@modal="setModal"  />
		<Views 
			:selected="selected"
			:item="item"
			:modal="modal"
			@selected="setSelected"
			@item="setItem" 
			@modal="setModal"  /> 
		<Modals 
			:selected="publication"
			:item="metaData"
			:modal="modal"
			@selected="setSelected"
			@item="setItem" 
			@modal="setModal" />
	</NcContent>
</template>

<script>
import MainMenu from './navigation/MainMenu.vue'
import Modals from './views/modals/modals.vue'
import Views from './views/views/views.vue'

export default {
	name: 'App',
	components: {
		MainMenu,
		Modals,
		Views,
	},
	props: {
		modal: {
			type: string,
			required: true,
		},
		item: {
			type: string,
			required: true,
		},
		selected: {
			type: string,
			required: true,
		}
	},
	methods: {
		setModal(modal) {
			this.modal = modal
			this.$emit('modal', modal)
		},			
		setSelected(selected) {
			this.selected = selected
			this.$emit('selected', selected)
		},
		setItem(item) {
			this.item = item
			this.$emit('item', item)
		},
	},
}
</script>
