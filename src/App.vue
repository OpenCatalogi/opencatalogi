<template>
	<NcContent app-name="opencatalog">
		<MainMenu />
		<Views /> 
		<Modals  />
	</NcContent>
</template>

<script>

import { NcContent, NcAppContent } from '@nextcloud/vue'
import MainMenu from './navigation/MainMenu.vue'
import Modals from './modals/modals.vue'
import Views from './views/views.vue'
import { store } from './store.js'

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppContent,
		MainMenu,
		Modals,
		Views,
		store,
	},
	methods: {
		setModal(modal) {
			store.modal = modal
		},			
		setSelected(selected) {
			store.selected = selected
		},
		setItem(item) {
			store.item = item
		}
	}
}
</script>
