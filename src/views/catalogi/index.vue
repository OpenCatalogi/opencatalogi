<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="catalogi" />
        <NcAppContent>
            <template #list>
                <CatalogiList />
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
import CatalogiList from './listist.vue';

export default {
    name: 'CatalogiIndex',
    components: {
        NcContent,
        NcAppContent,
        MainMenu,
        CatalogiList
    },
}
</script>
