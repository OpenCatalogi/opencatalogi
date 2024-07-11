<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.publicationId || store.selected != 'publication'"
				class="detailContainer"
				name="Geen publicatie"
				description="Nog geen publicatie geselecteerd">
				<template #icon>
					<ListBoxOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('publicationAdd')">
						Publicatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationDetails v-if="store.publicationId && store.selected === 'publication'" :publication-id="store.publicationId" />
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
}
</script>
