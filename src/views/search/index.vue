<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="search" />
        <NcAppContent>
            <template #list>
                <SearchList />
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
import SearchList from './list.vue';

export default {
    name: 'searchIndex',
    components: {
        NcContent,
        NcAppContent,
        MainMenu,
        SearchList
    },
}
</script>
