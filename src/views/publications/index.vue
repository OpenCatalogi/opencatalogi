<template>
	<NcAppContent>
		<template #list>
			<PublicationList @publicationId="updatePublicationId" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!publicationId"
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicaite geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action />
			</NcEmptyContent>
			<PublicationDetails v-if="store.item" :publication-id="publicationId" />
		</template>
	</NcAppContent>
</template>

<script>
import { store } from '../../store.js'
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
		store
	},
	data() {
		return {
			publicationId: undefined,
			selectedId: undefined,
			store: {
				selected: 'dashboard',
				modal: false,
				item: false
			}
		}
	},
	mounted() {
		this.selectedId = this.getIdFromUrl()
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
