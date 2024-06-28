<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="metaData" />
        <NcAppContent>
            <template #list>
                <MetaDataList @activePublication="updateMetaDataId" @activeMetaDataIdId="updateMetaDataId"/>
            </template>
            <template #default>
                <NcEmptyContent name="Geen Metadata" description="Nog geenmetadata geselecteerd" v-if="activeDirectory !== true" >
                    <template #icon>
                        <ListBoxOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
				<DirectoryDetails v-if="MetaDataId === true" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import MetaDataList from './list.vue';
import MetaDataDetails from './details.vue';
import FileTreeOutline from 'vue-material-design-icons/BriefcaseOutline';

export default {
    name: 'metaDataIndex',
    components: {
        NcContent,
        NcAppContent,
        NcEmptyContent,
        MainMenu,
        MetaDataList,
        MetaDataDetails,
        FileTreeOutline
    },
	data() {
		return {
			activeMetaData: false,
			activeMetaDataId: '',
		}
	},
	methods: {
		updateMetaData(variable) {
			this.activeMetaData = variable
		},
		updateMetaDataId(variable) {
			this.activeMetaDataId = variable
		}
	}
}
</script>
