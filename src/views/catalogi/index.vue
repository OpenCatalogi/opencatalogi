<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="catalogi" />
        <NcAppContent>
            <template #list>
                <CatalogiList @activeCatalog="updateActiveCatalog" @activeCatalogId="updateActiveCatalogId"/>
            </template>
            <template #default>
                <NcEmptyContent name="Geen publicatie" description="Nog geen publicaite geselecteerd" v-if="activePublication !== true" >
                    <template #icon>
                        <DatabaseOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
				<DirectoryDetails v-if="activeCatalog === true" />
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
			activeCatalog: false,
			activeCatalogId: '',
		}
	},
	methods: {
		updateCatalog(variable) {
			this.activeCatalog = variable
		},
		updateCatalogId(variable) {
			this.activeCatalogId = variable
		}
	}
}
</script>
