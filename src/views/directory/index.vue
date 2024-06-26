<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="directory" />
        <NcAppContent>
            <template #list>
                <DirectoryList />
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
import DirectoryList from './listist.vue';

export default {
    name: 'directoryIndex',
    components: {
        NcContent,
        NcAppContent,
        MainMenu,
        DirectoryList
    },
}
</script>
