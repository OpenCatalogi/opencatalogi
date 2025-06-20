/**
 * ThemeDetail.vue
 * Component for displaying theme details
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
				{{ theme.title }}
			</h1>

			<NcActions
				:disabled="objectStore.isLoading('theme')"
				:primary="true"
				:menu-name="objectStore.isLoading('theme') ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze publicatie">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="objectStore.isLoading('theme')"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!objectStore.isLoading('theme')" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over themas"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/themas')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('theme')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('copyObject', { objectType: 'theme', dialogTitle: 'Theme'})">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setDialog('deleteObject', { objectType: 'theme', dialogTitle: 'Theme'})">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="detailDataContainer">
			<div>
				<b>Samenvatting:</b>
				<span>{{ theme.summary }}</span>
			</div>
			<div>
				<b>Beschrijving:</b>
				<span>{{ theme.description }}</span>
			</div>
			<div>
				<b>Afbeelding:</b>
				<span>{{ theme.image }}</span>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'ThemeDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
	},
	computed: {
		theme() {
			return objectStore.getActiveObject('theme')
		},
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
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

.active.themeDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.themeDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.ThemeDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.float-right {
    float: right;
}
</style>
<style scoped>
.detailDataContainer {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
</style>
