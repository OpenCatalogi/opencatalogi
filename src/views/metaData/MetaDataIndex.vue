<script setup>
import { UIStore, searchStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MetaDataList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!metadataStore.metaDataItem || UIStore.selected != 'metaData'"
				class="detailContainer"
				name="Geen Metadata"
				description="Nog geen metadata beschrijving geselecteerd">
				<template #icon>
					<FileTreeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="UIStore.setModal('addMetaData')">
						Metadata beschrijving toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<MetaDataDetails v-if="metadataStore.metaDataItem && UIStore.selected === 'metaData'" :meta-data-item="metadataStore.metaDataItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MetaDataList from './MetaDataList.vue'
import MetaDataDetails from './MetaDataDetail.vue'
// eslint-disable-next-line n/no-missing-import
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'

export default {
	name: 'MetaDataIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		MetaDataList,
		MetaDataDetails,
		FileTreeOutline,
	},
	data() {
		return {

		}
	},
}
</script>
