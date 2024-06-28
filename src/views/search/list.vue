<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					disabled
					:value.sync="search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="clearText">
					<Magnify :size="20" />
				</NcTextField>
			</div>

			<NcListItem v-for="(metadata, i) in metadatas"
				v-if="!loading"
				:key="`${metadata}${i}`"
				:name="metadata?.omschrijving"
				:bold="false"
				:force-display-actions="true"
				:active="activeMetaData === metadata.id"
				:details="'1h'"
				:counter-number="44"
				@click="setActive(metadata.id)">
				<template #icon>
					<BriefcaseOutline :class="activeMetaData === metadata.id && 'selectedZaakIcon'"
						disable-menu
						:size="44"
						user="janedoe"
						display-name="Jane Doe" />
				</template>
				<template #subname>
					{{ metadata?.subname }}
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

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Zaken aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcListItemIcon, NcActionButton, NcAvatar, NcAppContentList, NcTextField, NcLoadingIcon } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify'
import BriefcaseOutline from 'vue-material-design-icons/BriefcaseOutline'

export default {
	name: 'SearchList',
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
			metadatas: [
				{
					id: '0219480938',
					omschrijving: 'metadata1',
					subname: 'subname',
				},
				{
					id: '0219480938',
					omschrijving: 'metadata2',
					subname: 'subname',
				},
			],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		setActive(id) {
			this.activeMetaData = id
			this.$emit('metaData', true)
			this.$emit('metaDataId', id)
		},
		clearText() {
			this.search = ''
		},
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
