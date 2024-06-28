<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="metaData" />
        <NcAppContent>
            <template #list>
                <MetaDataList @metaDataId="updateMetaDataId" />
            </template>
            <template #default>
                <NcEmptyContent class="detailContainer" name="Geen Metadata" description="Nog geenmetadata geselecteerd"
                    v-if="metaDataId === undefined">
                    <template #icon>
                        <FileTreeOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
                <MetaDataDetails v-if="metaDataId" :metaDataId="metaDataId" />
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
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline';

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
            metaDataId: undefined,
        }
    },
    methods: {
        updateMetaDataId(variable) {
            this.metaDataId = variable
        },
    }
}
</script>
