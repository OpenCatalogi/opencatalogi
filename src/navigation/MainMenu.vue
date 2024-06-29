<template>
	<NcAppNavigation>
		<NcActions>
			<NcActionButton @click="showModal(&quot;addPublicationModal&quot;)">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Publicatie
			</NcActionButton>
			<NcActionButton :disabled="publication === &quot;&quot;" @click="showModal(&quot;editPublicationModal&quot;)">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Publicatie
			</NcActionButton>
			<NcActionButton @click="showModal(&quot;addMetaDataModal&quot;)">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Metadata
			</NcActionButton>
			<NcActionButton :disabled="metaData === &quot;&quot;" @click="showModal(&quot;editMetaDataModal&quot;)">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Metadata
			</NcActionButton>
			<NcActionButton @click="showModal(&quot;addCatalogModal&quot;)">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add Catalog
			</NcActionButton>
			<NcActionButton :disabled="catalog === &quot;&quot;" @click="showModal(&quot;editCatalogModal&quot;)">
				<template #icon>
					<CogOutline :size="20" />
				</template>
				Edit Catalog
			</NcActionButton>
			<NcActionButton @click="showModal(&quot;addExternalCatalogModal&quot;)">
				<template #icon>
					<Plus :size="20" />
				</template>
				Add External Catalog
			</NcActionButton>
			<NcActionButton :disabled="externalCatalog === &quot;&quot;" @click="showModal(&quot;editExternalCatalogModal&quot;)">
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
			<NcAppNavigationItem :active="store.selected === ''" name="Dashboard" >
				<template #icon>
					<Finance :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem v-for="(catalogus, i) in catalogi.results"
				v-if="!loading"
				:key="`${catalogus}${i}`"
				:name="catalogus?.name"
				:active="selected === catalogus?.id"
				:href="'/index.php/apps/opencatalog/catalog/' + catalogus?.id">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="store.selected === 'search'" name="Search">
				<template #icon>
					<LayersSearchOutline :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>

		<NcAppNavigationSettings>
			<NcAppNavigationItem :active="selected === 'catalogi'"
				name="Catalogi"
				href=" /index.php/apps/opencatalog/catalogi">
				<template #icon>
					<DatabaseCogOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="selected === 'directory'"
				name="Directory"
				href=" /index.php/apps/opencatalog/directory">
				<template #icon>
					<LayersOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<NcAppNavigationItem :active="selected === 'metaData'"
				name="MetaData"
				href=" /index.php/apps/opencatalog/metadata">
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
										<NcTextField id="drc_location"
											:value.sync="drc_location"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="drc_key"
											:value.sync="drc_key"
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
										<NcTextField id="orc_location"
											:value.sync="orc_location"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="orc_key"
											:value.sync="orc_key"
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
										<NcTextField id="elastic_location"
											:value.sync="elastic_location"
											:label-outside="true"
											placeholder="https://" />
									</td>
									<td>Key</td>
									<td>
										<NcTextField id="elastic_key"
											:value.sync="elastic_key"
											:label-outside="true"
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
				<NcAppSettingsSection id="sharing" name="Organisation" doc-url="zaakafhandel.app">
					<template #icon>
						<Connection :size="20" />
					</template>

					<p>
						Here you can set the details for your organisation
					</p>

					<NcTextField id="organisation_name" :value.sync="organisation_name" />
					<NcTextField id="organisation_oin" :value.sync="organisation_oin" />
					<NcTextArea id="organisation_pki" :value.sync="organisation_pki" />

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
	NcTextArea,
} from '@nextcloud/vue'
import { store } from '../store.js'

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
		store,
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
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		// We use the catalogi in the menu so lets fetch those
		fetchData(newPage) {
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
		},
		save() {
			this.zrc_location = ''
			this.zrc_key = ''
		},
		setModal(modal) {
			store.modal = modal
		},			
		setSelected(selected) {
			store.selected = selected
		},
		setItem(item) {
			store.item = item
		}
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
