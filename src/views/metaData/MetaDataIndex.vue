<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MetaDataList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.metaDataId || store.selected != 'metaData'"
				class="detailContainer"
				name="Geen Metadata"
				description="Nog geen metadata beschrijving geselecteerd">
				<template #icon>
					<FileTreeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('addMetaData')">
						Metadata beschrijving toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<MetaDataDetails v-if="store.metaDataId && store.selected === 'metaData'" :meta-data-id="store.metaDataId" />
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
			activeMetaData: false,
			metaDataId: undefined,
		}
	},
}
</script>
