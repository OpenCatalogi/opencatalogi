<template>
	<NcContent app-name="opencatalog">
		<MainMenu />
		<Views />
		<Modals />
	</NcContent>
</template>

<script>

import { NcContent } from '@nextcloud/vue'
import MainMenu from './navigation/MainMenu.vue'
import Modals from './modals/Moodals.vue'
import Views from './views/views.vue'
import { store } from './store.js'

export default {
	name: 'App',
	components: {
		NcContent,
		MainMenu,
		Modals,
		Views,
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
		},
	},
}
</script>
