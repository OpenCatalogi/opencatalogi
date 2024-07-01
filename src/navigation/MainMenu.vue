<script setup>
import { store } from '../store.js'
</script>

<template>
	<NcAppNavigation>
		<NcActions>
			<NcActionButton @click="store.setModal('publicationAdd')">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Publicatie
			</NcActionButton>
			<NcActionButton @click="store.setModal('publicationEdit')">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Publicatie
			</NcActionButton>
			<NcActionButton @click="store.setModal('metaDataAdd')">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Metadata
			</NcActionButton>
			<NcActionButton @click="store.setModal('metaDataEdit')">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Metadata
			</NcActionButton>
			<NcActionButton @click="store.setModal('catalogAdd')">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Catalog
			</NcActionButton>
			<NcActionButton @click="store.setModal('catalogEdit')">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Catalog
			</NcActionButton>
			<NcActionButton @click="store.setModal('directoryAdd')">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add External Catalog
			</NcActionButton>
			<NcActionButton @click="store.setModal('directoryEdit')">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit External Catalog
			</NcActionButton>
		</NcActions>

		<NcAppNavigationList>
			<NcAppNavigationNewItem name="Publicatie Aanmaken" @new-item="store.modal = 'publicationAdd'">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcAppNavigationNewItem>
			<NcAppNavigationItem :active="store.selected === 'dashboard'" name="Dashboard" @click="store.setSelected('dashboard')">
				<template #icon>
					<Finance :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem v-for="(catalogus, i) in catalogi.results"
				:key="`${catalogus}${i}`"
				:name="catalogus?.name"
				:active="store.selected === 'catalogus' && store.item === catalogus?.id"
				@click="store.setSelected('catalogus'); store.setItem(catalogus?.id)">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="store.selected === 'search'" name="Search" @click="store.setSelected('search')">
				<template #icon>
					<LayersSearchOutline :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>

		<NcAppNavigationSettings>
			<NcAppNavigationItem :active="store.selected === 'catalogi'" name="Catalogi" @click="store.setSelected('catalogi')">
				<template #icon>
					<DatabaseCogOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="store.selected === 'directory'" name="Directory" @click="store.setSelected('directory')">
				<template #icon>
					<LayersOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="store.selected === 'metaData'" name="MetaData" @click="store.setSelected('metaData')">
				<template #icon>
					<FileTreeOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppNavigationItem name="Configuration" @click="settingsOpen = true">
				<template #icon>
					<CogOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppSettingsDialog :open.sync="settingsOpen" :show-navigation="true" name="Application settings">
				<NcAppSettingsSection id="sharing" name="Connections" doc-url="zaakafhandel.app">
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
									<td class="row-name">
										DRC
									</td>
									<td>Location</td>
									<td>
										<NcTextField id="drcLocation"
											:value.sync="configuration.drcLocation"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="drcKey"
											:value.sync="configuration.drcKey"
											:label-outside="true"
											placeholder="***" />
									</td>
								</tr>
								<tr>
									<td class="row-name">
										ORC
									</td>
									<td>Location</td>
									<td>
										<NcTextField id="orcLocation"
											:value.sync="configuration.orcLocation"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="orcKey"
											:value.sync="configuration.orcKey"
											:label-outside="true"
											placeholder="***" />
									</td>
								</tr>
								<tr>
									<td class="row-name">
										Elastic
									</td>
									<td>Location</td>
									<td>
										<NcTextField id="elasticLocation"
											:value.sync="configuration.elasticLocation"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="elasticKey"
											:value.sync="configuration.elasticKey"
											:label-outside="true"
											placeholder="***" />
									</td>
								</tr>
								<tr>
									<td class="row-name">
										Mongo DB
									</td>
									<td>Location</td>
									<td>
										<NcTextField id="mongodbLocation"
											:value.sync="configuration.mongodbLocation"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="mongodbKey"
											:value.sync="configuration.mongodbKey"
											:label-outside="true"
											placeholder="***" />
									</td>
									<td>Cluster name</td>
									<td>
										<NcTextField id="mongodbCluster"
											 :value.sync="configuration.mongodbCluster"
											 :label-outside="true"
											 placeholder="***" />
									</td>
								</tr>
							</tbody>
						</table>
					</p>
					<NcButton aria-label="Save"
						type="primary"
						wide
						@click="saveConfig()">
						<template #icon>
							<ContentSave :size="20" />
						</template>
						Save
					</NcButton>
				</NcAppSettingsSection>
				<NcAppSettingsSection id="organisation" name="Organisation" doc-url="zaakafhandel.app">
					<template #icon>
						<Connection :size="20" />
					</template>

					<p>
						Here you can set the details for your organisation
					</p>

					<NcTextField id="organisationName" :value.sync="configuration.organisationName" />
					<NcTextField id="organisationOin" :value.sync="configuration.organisationOin" />
					<NcTextArea id="organisationPki" :value.sync="configuration.organisationPki" />

					<NcButton aria-label="Save"
						type="primary"
						wide
						@click="saveConfig()">
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
	NcTextArea,
} from '@nextcloud/vue'

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
	name: 'MainMenu',
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
		Finance,
	},
	data() {
		return {
			// all of this is settings and should be moved
			settingsOpen: false,
			orc_location: '',
			orc_key: '',
			drc_location: '',
			drc_key: '',
			elastic_location: '',
			elastic_key: '',
			loading: true,
			organisation_name: '',
			organisation_oin: '',
			organisation_pki: '',
			catalogi: [],
			configuration: {
				drcLocation: '',
				drcKey: '',
				orcLocation: '',
				orcKey: '',
				elasticLocation: '',
				elasticKey: '',
				mongodbLocation: '',
				mongodbKey: '',
				mongodbCluster: '',
				organisationName: '',
				organisationOin: '',
				organisationPki: '',
			},
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		// We use the catalogi in the menu so lets fetch those
		fetchData(newPage) {
			// Catalogi details
			this.loading = true,
			fetch(
				'/index.php/apps/opencatalog/catalogi/api',
				{
					method: 'GET',
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

			fetch(
				'/index.php/apps/opencatalog/configuration',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.configuration = data
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
		saveConfig() {
			 // Simple POST request with a JSON body using fetch
			const requestOptions = {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(this.configuration),
			}

			fetch('/index.php/apps/opencatalog/configuration', requestOptions)
				.then((response) => {
					response.json().then((data) => {
						this.configuration = data
					})
				})
				.catch((err) => {
					console.error(err)
				})
		},
	},
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
