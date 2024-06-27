<template>
    <NcAppContentList>
        <ul>
            <div class="listHeader">
                <NcTextField class="searchField" disabled :value.sync="search" label="Search"
                    trailing-button-icon="close" :show-trailing-button="search !== ''"
                    @trailing-button-click="clearText">
                    <Magnify :size="20" />
                </NcTextField>
            </div>

            <NcListItem v-if="!loading" v-for="(publication, i) in publications.results" 
                :key="`${publication}${i}`"
                :name="publication?.name" 
                :bold="false" :force-display-actions="true"
                :active="activePublication === publication.id" 
                :details="'1h'" :counter-number="44"
                @click="setActive(publication.id)">

                <template #icon>
                    <BriefcaseOutline :class="activePublication === publication.id && 'selectedZaakIcon'" disable-menu
                        :size="44" user="janedoe" display-name="Jane Doe" />
                </template>
                <template #subname>
                    {{ publication?.summary }}
                </template>
                <template #actions>
                    <NcActionButton>
                        Button one
                    </NcActionButton>
                    <NcActionButton>
                        Button two
                    </NcActionButton>
                    <NcActionButton>
                        Button three
                    </NcActionButton>
                </template>
            </NcListItem>

            <NcLoadingIcon v-if="loading" class="loadingIcon" :size="64" appearance="dark" name="Zaken aan het laden" />
        </ul>
    </NcAppContentList>
</template>
<script>
import { NcListItem, NcListItemIcon, NcActionButton, NcAvatar, NcAppContentList, NcTextField, NcLoadingIcon } from '@nextcloud/vue';
import Magnify from 'vue-material-design-icons/Magnify';
import BriefcaseOutline from 'vue-material-design-icons/BriefcaseOutline';

export default {
    name: "PublicationList",
    components: {
        NcListItem,
        NcListItemIcon,
        NcActionButton,
        NcAvatar,
        NcAppContentList,
        NcTextField,
        BriefcaseOutline,
        Magnify,
        NcLoadingIcon,
    },
    data() {
        return {
            search: '',
            loading: false,
            activeMetaData: '',
            publications: [],
        }
    },
    mounted() {
        this.fetchData()
    },
    methods: {
		fetchData(newPage) {
			this.loading = true,
			fetch(
				'/index.php/apps/opencatalog/publications/api',
			{
				method: 'GET'
			},
			)
			.then((response) => {
				response.json().then((data) => {
				this.publications = data
				})
				this.loading = false
			})
			.catch((err) => {
				console.error(err)
				this.loading = false
			})
		},
        setActive(id) {
            this.activePublication = id
            this.$emit('publication', true)
            this.$emit('publicationId', id)
        },
        clearText() {
            this.search = ''
        }
    },
}
</script>
<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--zaa-margin-20);
}
</style>