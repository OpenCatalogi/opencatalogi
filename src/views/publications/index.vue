<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="publications" />
        <NcAppContent>
            <template #list>
                <PublicationList @activePublication="updateActivePublication" @activePublicationId="updateActivePublicationId" />
            </template>
            <template #default>                
                <NcEmptyContent name="Geen publicatie" description="Nog geen publicaite geselecteerd" v-if="activePublication !== true" >
                    <template #icon>
                        <ListBoxOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
                <div v-if="activePublication === true">
                    Test
                </div>
				<PublicationDetails :publicationId="activePublicationId" v-if="activePublication === true && activePublicationId" />
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
			activePublication: false,
			activePublicationId: '',
		}
	},
	methods: {
		updateActivePublication(variable) {
			this.activePublication = variable
		},
		updateActivePublicationId(variable) {
			this.activePublicationId = variable
		}
	}
}
</script>
