<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="metaData" />
        <NcAppContent>
            <template #list>
                <MetaDataList />
            </template>
            <template #default>
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import MetaDataList from './metaDataList.vue';

export default {
    name: 'metaDataIndex',
    components: {
        NcContent,
        NcAppContent,
        MainMenu,
        MetaDataList
    },
}
</script>
