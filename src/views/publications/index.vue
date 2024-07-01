<template>
	<NcContent app-name="opencatalog">
		<MainMenu :selected="selectedId" />
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
				<PublicationDetails v-if="publicationId" :publication-id="publicationId" />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import PublicationList from './list.vue'
import PublicationDetails from './details.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline'

export default {
	name: 'PublicationIndex',
	components: {
		NcContent,
		NcAppContent,
		NcEmptyContent,
		MainMenu,
		ListBoxOutline,
		PublicationList,
		PublicationDetails,
	},
	data() {
		return {
			publicationId: undefined,
			selectedId: undefined,
		}
	},
	mounted() {
		this.selectedId = this.getIdFromUrl()
	},
	methods: {
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
