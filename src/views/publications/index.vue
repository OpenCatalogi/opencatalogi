<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.item || store.selected != 'publication' "
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicaite geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action />
			</NcEmptyContent>
			<PublicationDetails v-if="store.item && store.selected === 'publication'" :publication-id="publicationId" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import PublicationList from './list.vue'
import PublicationDetails from './details.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline'

export default {
	name: 'PublicationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		MainMenu,
		ListBoxOutline,
		PublicationList,
		PublicationDetails,
	},
	methods: {
		// depracticed
		updatePublicationId(variable) {
			this.publicationId = variable
		},
		getIdFromUrl() {
			const url = window.location.href
			const lastParam = url.split('/').slice(-1)[0]
			return lastParam
		},
	},
}
</script>
