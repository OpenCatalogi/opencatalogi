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
			<NcAppNavigationItem   v-if="!loading" v-for="(catalogus, i) in catalogi.results" :key="`${catalogus}${i}`" :name="catalogus?.name" :active="selected === catalogus?.id" 
				 :href="'/index.php/apps/opencatalog/catalog/' + catalogus?.id">
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
						Here you can set the details for varius Connections
					</p>

					<p>
					<table>
						<tbody>
							<tr>
								<td class="row-name">DRC</td>
								<td>Location</td>
								<td>
									<NcTextField :value.sync="drc_location" id="drc_location" :label-outside="true"
										placeholder="https://" />
								</td>
								<td>Key</td>
								<td>
									<NcTextField :value.sync="drc_key" id="drc_key" :label-outside="true"
										placeholder="***" />
								</td>
							</tr>
							<tr>
								<td class="row-name">ORC</td>
								<td>Location</td>
								<td>
									<NcTextField :value.sync="orc_location" id="orc_location" :label-outside="true"
										placeholder="https://" />
								</td>
								<td>Key</td>
								<td>
									<NcTextField :value.sync="orc_key" id="orc_key" :label-outside="true"
										placeholder="***" />
								</td>
							</tr>
							<tr>
								<td class="row-name">Elastic</td>
								<td>Location</td>
								<td>
									<NcTextField :value.sync="elastic_location" id="elastic_location" :label-outside="true"
										placeholder="https://" />
								</td>
								<td>Key</td>
								<td>
									<NcTextField :value.sync="elastic_key" id="elastic_key" :label-outside="true"
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
				<NcAppSettingsSection id="sharing" name="Organisation" docUrl="zaakafhandel.app">
					<template #icon>
						<Connection :size="20" />
					</template>
					
					<p>
						Here you can set the details for your organisation
					</p>

				   <NcTextField :value.sync="organisation_name" id="organisation_name" />
				   <NcTextField :value.sync="organisation_oin" id="organisation_oin" />
				   <NcTextArea :value.sync="organisation_pki" id="organisation_pki" />

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
	NcTextField,
	NcTextArea
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
		NcTextArea,
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
      		loading: true,
			catalogi: []
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(newPage) {
			this.loading = true,
			fetch(
				'/index.php/apps/opencatalog/catalogi/api',
			{
				method: 'GET'
			},
			)
			.then((response) => {
				response.json().then((data) => {
				this.catalogi = data
				})
				this.loading = false
			})
			.catch((err) => {
				console.error(err)
				this.loading = false
			})
		},
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
