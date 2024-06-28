<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="directory" />
        <NcAppContent>
            <template #list>
                <DirectoryList @activeDirectory="updateActiveDirectory" @activePDirectoryId="updateActiveDirectoryId"/>
            </template>
            <template #default>
                <NcEmptyContent name="Geen Directory" description="Nog geen directory geselecteerd" v-if="activeDirectory !== true" >
                    <template #icon>
                        <LayersOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
				<DirectoryDetails v-if="activeDirectory === true" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import DirectoryList from './list.vue';
import DirectoryDetails from './details.vue';
import LayersOutline from 'vue-material-design-icons/LayersOutline';

export default {
    name: 'directoryIndex',
    components: {
        NcContent,
        NcAppContent,
        NcEmptyContent,
        MainMenu,
        DirectoryList,
        DirectoryDetails,
        LayersOutline
    },
	data() {
		return {
			activeDirectory: false,
			activeDirectoryId: '',
		}
	},
	methods: {
		updateDirectory(variable) {
			this.activeDirectory = variable
		},
		updateDirectoryId(variable) {
			this.activeDirectoryId = variable
		}
	}
}
</script>
