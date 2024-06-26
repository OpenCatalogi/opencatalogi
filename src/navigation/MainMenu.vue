<template>

	<NcAppNavigation>

		<NcActions>
			<NcActionButton @click='showModal("addPublicationModal")'>
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Publicatie
			</NcActionButton>
			<NcActionButton @click='showModal("editPublicationModal")' :disabled='publication === ""'>
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Publicatie
			</NcActionButton>
			<NcActionButton @click='showModal("addMetaDataModal")'>
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Metadata
			</NcActionButton>
			<NcActionButton @click='showModal("editMetaDataModal")' :disabled='metaData === ""'>
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Metadata
			</NcActionButton>
			<NcActionButton @click='showModal("addCatalogModal")'>
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Catalog
			</NcActionButton>
			<NcActionButton @click='showModal("editCatalogModal")' :disabled='catalog === ""'>
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Catalog
			</NcActionButton>
			<NcActionButton @click='showModal("addExternalCatalogModal")'>
				<template #icon>
					<Plus :size="20" />
				</template>
				Add External Catalog
			</NcActionButton>
			<NcActionButton @click='showModal("editExternalCatalogModal")' :disabled='externalCatalog === ""'>
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit External Catalog
			</NcActionButton>
		</NcActions>

		<NcAppNavigationList>
			<NcAppNavigationNewItem name="Publicatie Aanmaken" @new-item="showModal('addPublicationModal')">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcAppNavigationNewItem>
			<NcAppNavigationItem :active="selected === 'dashboard'"  name="Dashboard"
				href="/index.php/apps/opencatalog/Dashboard">
				<template #icon>
					<Finance :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="selected === '5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f'" 
				name="Catalouge 1" href="/index.php/apps/opencatalog/catalog/5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="selected === '4c3edd34-a90d-4d2a-8894-adb5836ecde8'" 
				name="Catalouge 2" href="/index.php/apps/opencatalog/catalog/4c3edd34-a90d-4d2a-8894-adb5836ecde8">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="selected === 'search'" name="Search"
				href="/index.php/apps/opencatalog/search">
				<template #icon>
					<LayersSearchOutline :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>

		<NcAppNavigationSettings>
			<NcAppNavigationItem :active="selected === 'catalogi'" name="Catalogi"
				href=" /index.php/apps/opencatalog/catalogi">
				<template #icon>
					<DatabaseCogOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="selected === 'directory'" name="Directory"
				href=" /index.php/apps/opencatalog/directory">
				<template #icon>
					<LayersOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppNavigationItem :active="selected === 'metaData'" name="MetaData"
				href=" /index.php/apps/opencatalog/metadata">
				<template #icon>
					<FileTreeOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppNavigationItem @click="settingsOpen = true" name="Configuration">
				<template #icon>
					<CogOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppSettingsDialog :open.sync="settingsOpen" :show-navigation="true" name="Application settings">
				<NcAppSettingsSection id="sharing" name="Connections" docUrl="zaakafhandel.app">
					<template #icon>
						<Connection :size="20" />
					</template>

					<p>
						Here you can set the details for varius connection
					</p>

					<p>
					<table>
						<tbody>
							<tr>
								<td class="row-name">ZRC</td>
								<td>Location</td>
								<td>
									<NcTextField :value.sync="zrc_location" id="zrc_location" :label-outside="true"
										placeholder="https://" />
								</td>
								<td>Key</td>
								<td>
									<NcTextField :value.sync="zrc_key" id="zrc_key" :label-outside="true"
										placeholder="***" />
								</td>
							</tr>
						</tbody>
					</table>
					</p>
					<NcButton aria-label="Save" type="primary" wide>
						<template #icon>
							<ContentSave :size="20" />
						</template>
						Save
					</NcButton>

				</NcAppSettingsSection>

			</NcAppSettingsDialog>


		</NcAppNavigationSettings>
	</NcAppNavigation>
</template>
<script>
import {
	NcActions,
	NcActionButton,
	NcAppNavigation,
	NcAppNavigationList,
	NcAppNavigationItem,
	NcAppNavigationNewItem,
	NcAppNavigationSettings,
	NcAppSettingsDialog,
	NcAppSettingsSection,
	NcButton,
	NcTextField
} from '@nextcloud/vue';
import { isModalOpen } from '../modals/modalContext.js';

import Connection from 'vue-material-design-icons/Connection'
import Delete from 'vue-material-design-icons/Delete.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import DatabaseEyeOutline from 'vue-material-design-icons/DatabaseEyeOutline'
import DatabaseCogOutline from 'vue-material-design-icons/DatabaseCogOutline'
import LayersSearchOutline from 'vue-material-design-icons/LayersSearchOutline'
import LayersOutline from 'vue-material-design-icons/LayersOutline'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'
import CogOutline from 'vue-material-design-icons/CogOutline'
import ContentSave from 'vue-material-design-icons/ContentSave'
import Finance from 'vue-material-design-icons/Finance'

export default {
	name: "MainMenu",
	props: [
		'selected',
		'publication',
		'metaData',
		'catalog',
		'externalCatalog',
	],
	components: {
		NcActions,
		NcActionButton,
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
		NcAppNavigationNewItem,
		NcAppNavigationSettings,
		NcAppSettingsDialog,
		NcAppSettingsSection,
		NcTextField,
		NcButton,
		Delete,
		Plus,
		Connection,
		DatabaseEyeOutline,
		DatabaseCogOutline,
		LayersSearchOutline,
		LayersOutline,
		FileTreeOutline,
		CogOutline,
		ContentSave,
		Finance
	},
	data() {
		return {
			settingsOpen: false,
			zrc_location: "",
			zrc_key: "",
			ztc_location: "",
			ztc_key: "",
			drc_location: "",
			drc_key: "",
		}
	},
	methods: {
		save() {
			this.zrc_location = ''
			this.zrc_key = ''
		},
		showModal(modalName) {
			isModalOpen[modalName] = true
		},

	}
}
</script>
<style>
table {
	table-layout: fixed;
}

td.row-name {
	padding-inline-start: 16px;
}

td.row-size {
	text-align: right;
	padding-inline-end: 16px;
}

.table-header {
	font-weight: normal;
	color: var(--color-text-maxcontrast);
}

.sort-icon {
	color: var(--color-text-maxcontrast);
	position: relative;
	inset-inline: -10px;
}

.row-size .sort-icon {
	inset-inline: 10px;
}
</style>
