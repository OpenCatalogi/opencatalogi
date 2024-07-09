<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.item || !store.publicationItem"
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicaite geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('publicationAdd')">
						Publicatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationDetails v-if="store.item && store.publicationItem" :publication-id="store.publicationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PublicationList from './PublicationList.vue'
import PublicationDetails from './PublicationDetail.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'

export default {
	name: 'PublicationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		ListBoxOutline,
		PublicationList,
		PublicationDetails,
		NcButton,
	},
	methods: {
		getIdFromUrl() {
			const url = window.location.href
			const lastParam = url.split('/').slice(-1)[0]
			return lastParam
		},
	},
}
</script>
