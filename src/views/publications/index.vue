<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="publications" />
        <NcAppContent>
            <template #list>
                <PublicationList @publicationId="updatePublicationId" />
            </template>
            <template #default>
                <NcEmptyContent class="detailContainer" name="Geen publicatie"
                    description="Nog geen publicaite geselecteerd" v-if="publicationId === undefined">
                    <template #icon>
                        <ListBoxOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
                <PublicationDetails v-if="publicationId" :publicationId="publicationId" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import PublicationList from './list.vue';
import PublicationDetails from './details.vue';
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline';

export default {
    name: 'publicationIndex',
    components: {
        NcContent,
        NcAppContent,
        NcEmptyContent,
        MainMenu,
        ListBoxOutline,
        PublicationList,
        PublicationDetails
    },
    data() {
        return {
            publicationId: undefined,
        }
    },
    methods: {
        updatePublicationId(variable) {
            this.publicationId = variable
        }
    }
}
</script>
