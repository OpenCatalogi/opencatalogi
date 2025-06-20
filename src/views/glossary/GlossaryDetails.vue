/**
 * GlossaryDetails.vue
 * Component for displaying glossary details
 * @category Views
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ glossary.title }}
			</h1>

			<NcActions
				:disabled="objectStore.isLoading('glossary')"
				:primary="true"
				:menu-name="objectStore.isLoading('glossary') ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze term">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="objectStore.isLoading('glossary')"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!objectStore.isLoading('glossary')" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over termen"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/termen')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('glossary')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('copyObject', { objectType: 'glossary', dialogTitle: 'Term' })">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('deleteObject', { objectType: 'glossary', dialogTitle: 'Term' })">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div>
					<b>Samenvatting:</b>
					<span>{{ glossary.summary || '-' }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ glossary.description || '-' }}</span>
				</div>
				<div>
					<b>Externe link:</b>
					<span>{{ glossary.externalLink || '-' }}</span>
				</div>
				<div>
					<b>Trefwoorden:</b>
					<span>{{ glossary.keywords?.join(', ') || '-' }}</span>
				</div>
				<div v-if="glossary.relatedTerms?.length">
					<b>Gerelateerde termen:</b>
					<div class="related-terms">
						<NcButton v-for="term in glossary.relatedTerms"
							:key="term.id"
							type="secondary"
							@click="selectTerm(term)">
							{{ term.title }}
						</NcButton>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon, NcButton } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'GlossaryDetails',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
	},
	computed: {
		glossary() {
			return objectStore.getActiveObject('glossary')
		},
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		selectTerm(term) {
			objectStore.setActiveObject('glossary', term)
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold;
}

.head{
	display: flex;
	justify-content: space-between;
}

.button{
	max-height: 10px;
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.dataContent {
  display: flex;
  flex-direction: column;
}

.active.glossaryDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.glossaryDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.GlossaryDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.float-right {
    float: right;
}

.related-terms {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
	margin-top: var(--OC-margin-10);
}
</style>
