<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="catalogi" />
        <NcAppContent>
            <template #list>
                <CatalogiList @catalogId="updateCatalogId" />
            </template>
            <template #default>
                <NcEmptyContent class="detailContainer" name="Geen publicatie"
                    description="Nog geen publicaite geselecteerd" v-if="!catalogId">
                    <template #icon>
                        <DatabaseOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
                <CatalogiDetails v-if="catalogId" :catalogId="catalogId" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import CatalogiList from './list.vue';
import CatalogiDetails from './details.vue';
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline';

export default {
    name: 'catalogiIndex',
    components: {
        NcContent,
        NcAppContent,
        NcEmptyContent,
        MainMenu,
        CatalogiList,
        CatalogiDetails,
        DatabaseOutline
    },
    data() {
        return {
            catalogId: undefined,
        }
    },
    methods: {
        updateCatalogId(variable) {
            this.catalogId = variable
        }
    }
}
</script>
