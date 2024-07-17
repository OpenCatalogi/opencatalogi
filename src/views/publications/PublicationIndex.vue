<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationList :search="store.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.publicationItem.id || store.selected != 'publication'"
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
			<PublicationDetails v-if="store.publicationItem.id && store.selected === 'publication'" :publication-id="store.publicationItem.id " />
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
