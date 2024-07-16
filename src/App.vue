<template>
	<NcContent app-name="opencatalogi">
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

export default {
	name: 'App',
	components: {
		NcContent,
		MainMenu,
		Modals,
		Dialogs,
		Views,
	},
}
</script>
