<script setup>
import { navigationStore, catalogiStore, publicationStore } from '../store/store.js'
</script>

<template>
	<NcAppNavigation>
		<NcAppNavigationList>
			<NcAppNavigationNew text="Publicatie Aanmaken" @click="navigationStore.setModal('publicationAdd')">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcAppNavigationNew>
			<NcAppNavigationItem :active="navigationStore.selected === 'dashboard'" name="Dashboard" @click="navigationStore.setSelected('dashboard')">
				<template #icon>
					<Finance :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem v-for="(catalogus, i) in catalogi.results"
				:key="`${catalogus}${i}`"
				:name="catalogus?.title"
				:active="catalogus.id === navigationStore.selectedCatalogus && navigationStore.selected === 'publication'"
				@click="switchCatalogus(catalogus)">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'search'" name="Search" @click="navigationStore.setSelected('search')">
				<template #icon>
					<LayersSearchOutline :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>

		<NcAppNavigationSettings>
			<NcAppNavigationItem :active="navigationStore.selected === 'catalogi'" name="Catalogi" @click="navigationStore.setSelected('catalogi')">
				<template #icon>
					<DatabaseCogOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'directory'" name="Directory" @click="navigationStore.setSelected('directory')">
				<template #icon>
					<LayersOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'metaData'" name="MetaData" @click="navigationStore.setSelected('metaData')">
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
					<NcCheckboxRadioSwitch :checked.sync="configuration.mongoStorage" type="switch">
						{{ t('forms', 'Use external storage (e.g. MongoDb) instead of Next Cloud internal storage') }}
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch :checked.sync="configuration.cloudStorage" type="switch">
						{{ t('forms', 'Use VNG APIs instead of MongoDB') }}
					</NcCheckboxRadioSwitch>
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
									<td>Index</td>
									<td>
										<NcTextField id="elasticIndex"
											:value.sync="configuration.elasticIndex"
											:label-outside="true"
											placeholder="objects" />
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
						@click="saveConfig(); feedbackPosition = 'top'">
						<template #icon>
							<ContentSave :size="20" />
						</template>
						Save
					</NcButton>
					<div v-if="feedbackPosition === 'top' && configurationSuccess !== -1">
						<NcNoteCard :type="configurationSuccess ? 'success' : 'error'">
							<p>
								{{ configurationSuccess ?
									'Success saving configuration' :
									'Failed saving configuration'
								}}
							</p>
						</NcNoteCard>
					</div>
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
						@click="saveConfig(); feedbackPosition = 'bottom'">
						<template #icon>
							<ContentSave :size="20" />
						</template>
						Save
					</NcButton>
					<div v-if="feedbackPosition === 'bottom' && configurationSuccess !== -1">
						<NcNoteCard :type="configurationSuccess ? 'success' : 'error'">
							<p>
								{{ configurationSuccess ?
									'Success saving configuration' :
									'Failed saving configuration'
								}}
							</p>
						</NcNoteCard>
					</div>
				</NcAppSettingsSection>
			</NcAppSettingsDialog>
		</NcAppNavigationSettings>
	</NcAppNavigation>
</template>
<script>

import {
	NcAppNavigation,
	NcAppNavigationList,
	NcAppNavigationItem,
	NcAppNavigationNew,
	NcAppNavigationSettings,
	NcAppSettingsDialog,
	NcAppSettingsSection,
	NcButton,
	NcTextField,
	NcTextArea,
	NcNoteCard,
	NcCheckboxRadioSwitch,
} from '@nextcloud/vue'

import Connection from 'vue-material-design-icons/Connection.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import DatabaseEyeOutline from 'vue-material-design-icons/DatabaseEyeOutline.vue'
import DatabaseCogOutline from 'vue-material-design-icons/DatabaseCogOutline.vue'
import LayersSearchOutline from 'vue-material-design-icons/LayersSearchOutline.vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import ContentSave from 'vue-material-design-icons/ContentSave.vue'
import Finance from 'vue-material-design-icons/Finance.vue'

export default {
	name: 'MainMenu',
	components: {
		// components
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
		NcAppNavigationNew,
		NcAppNavigationSettings,
		NcAppSettingsDialog,
		NcAppSettingsSection,
		NcTextField,
		NcTextArea,
		NcButton,
		NcNoteCard,
		NcCheckboxRadioSwitch,
		// icons
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
				external: false,
				drcLocation: '',
				drcKey: '',
				orcLocation: '',
				orcKey: '',
				elasticLocation: '',
				elasticKey: '',
				elasticIndex: '',
				mongodbLocation: '',
				mongodbKey: '',
				mongodbCluster: '',
				organisationName: '',
				organisationOin: '',
				organisationPki: '',
			},
			configurationSuccess: -1,
			feedbackPosition: '',
			debounceTimeout: false,
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		// We use the catalogi in the menu so lets fetch those
		fetchData(newPage) {
			this.loading = true
			// Catalogi details
			fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
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
				'/index.php/apps/opencatalogi/configuration',
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

			const debounceNotification = (status) => {
				this.configurationSuccess = status

				if (this.debounceTimeout) {
					clearTimeout(this.debounceTimeout)
				}

				this.debounceTimeout = setTimeout(() => {
					this.feedbackPosition = undefined
					this.configurationSuccess = -1
				}, 1500)
			}

			fetch('/index.php/apps/opencatalogi/configuration', requestOptions)
				.then((response) => {
					debounceNotification(response.ok)

					response.json().then((data) => {
						this.configuration = data
					})
				})
				.catch((err) => {
					debounceNotification(false)
					console.error(err)
				})
		},
		switchCatalogus(catalogus) {
			if (catalogus.id !== navigationStore.selectedCatalogus) publicationStore.setPublicationItem(false) // for when you switch catalogus
			navigationStore.setSelected('publication')
			navigationStore.setSelectedCatalogus(catalogus.id)
			catalogiStore.setCatalogiItem(catalogus)
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
