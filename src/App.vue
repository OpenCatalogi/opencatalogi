<template>
	<NcContent app-name="opencatalog">
		<MainMenu />
		<Views />
		<Modals />
		<Dialogs />
	</NcContent>
</template>

<script>

import { NcContent } from '@nextcloud/vue'
import MainMenu from './navigation/MainMenu.vue'
import Modals from './modals/Modals.vue'
import Dialogs from './dialogs/Dialogs.vue'
import Views from './views/Views.vue'
import { store } from './store.js'

export default {
	name: 'App',
	components: {
		NcContent,
		MainMenu,
		Modals,
		Dialogs,
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
