<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<MetaDataList @metaDataId="updateMetaDataId" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.metaDataItem"
				class="detailContainer"
				name="Geen Metadata"
				description="Nog geen metadata beschrijving geselecteerd">
				<template #icon>
					<FileTreeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('metaDataAdd')">
						Metadata beschrijving toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<!-- <MetaDataDetails v-if="store.item && store.selected === 'metaData'" :meta-data-id="metaDataId" /> -->
			<MetaDataDetails v-if="store.metaDataItem" :meta-data-id="store.metaDataItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import MetaDataList from './MetaDataList.vue'
import MetaDataDetails from './MetaDataDetail.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'

export default {
	name: 'MetaDataIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		MainMenu,
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
	methods: {
		updateMetaDataId(variable) {
			this.metaDataId = variable
		},
	},
}
</script>
