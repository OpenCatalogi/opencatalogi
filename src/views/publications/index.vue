<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="publications" />
        <NcAppContent>
            <template #list>
                <PublicationList @activePublication="updateActivePublication" @activePublicationId="updateActivePublicationId" />
            </template>
            <template #default>                
                <NcEmptyContent name="Geen zaak" description="Nog geen zaak geselecteerd" v-if="activePublication !== true" >
                    <template #icon>
                        <ListBoxOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
				<PublicationDetails :publicationId="activePublicationId" v-if="activePublication === true && activePublicationId" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import PublicationList from './list.vue';
import PublicationDetails from './details.vue';
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline';

export default {
    name: 'publicationIndex',
    components: {
        NcContent,
        NcAppContent,
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
