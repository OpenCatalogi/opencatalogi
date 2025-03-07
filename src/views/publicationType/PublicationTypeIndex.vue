<script setup>
import { navigationStore, searchStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationTypeList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!publicationTypeStore.publicationTypeItem || navigationStore.selected != 'publicationType'"
				class="detailContainer"
				name="Geen publicatietype"
				description="Nog geen publicatietype geselecteerd">
				<template #icon>
					<FileTreeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('addPublicationType')">
						Publicatietype toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationTypeDetails v-if="publicationTypeStore.publicationTypeItem && navigationStore.selected === 'publicationType'" :publication-type-item="publicationTypeStore.publicationTypeItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PublicationTypeList from './PublicationTypeList.vue'
import PublicationTypeDetails from './PublicationTypeDetail.vue'
// eslint-disable-next-line n/no-missing-import
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'

export default {
	name: 'PublicationTypeIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		PublicationTypeList,
		PublicationTypeDetails,
		FileTreeOutline,
	},
	data() {
		return {

		}
	},
}
</script>
