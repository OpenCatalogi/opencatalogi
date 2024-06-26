<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="publications" />
        <NcAppContent>
            <template #list>
                <PublicationList />
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
import PublicationList from './list.vue';

export default {
    name: 'metaDataIndex',
    components: {
        NcContent,
        NcAppContent,
        MainMenu,
        PublicationList
    },
}
</script>
