<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="metaData" />
		<NcAppContent>
			<template #list>
				<MetaDataList @metaDataId="updateMetaDataId" />
			</template>
			<template #default>
				<NcEmptyContent v-if="!metaDataId"
					class="detailContainer"
					name="Geen Metadata"
					description="Nog geenmetadata geselecteerd">
					<template #icon>
						<FileTreeOutline />
					</template>
					<template #action />
				</NcEmptyContent>
				<MetaDataDetails v-if="metaDataId" :meta-data-id="metaDataId" />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import MetaDataList from './list.vue'
import MetaDataDetails from './details.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'

export default {
	name: 'MetaDataIndex',
	components: {
		NcContent,
		NcAppContent,
		NcEmptyContent,
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
