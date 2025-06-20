<script setup>
import { objectStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog v-if="navigationStore.modal === 'viewCatalogi'"
		:name="`View Catalog: ${activeCatalog?.title || 'Unknown'}`"
		size="large"
		:can-close="false">
		<div class="formContainer viewCatalogiDialog">
			<!-- Catalog Details -->
			<div class="catalogDetailsGrid">
				<div class="catalogMainInfo">
					<h2>{{ activeCatalog?.title }}</h2>
					<p v-if="activeCatalog?.summary" class="catalogSummary">
						{{ activeCatalog.summary }}
					</p>
				</div>

				<div class="catalogProperties">
					<div class="propertyItem">
						<strong>{{ t('opencatalogi', 'Status') }}:</strong>
						<span>{{ activeCatalog?.listed ? 'Publiek vindbaar' : 'Niet publiek vindbaar' }}</span>
					</div>
					<div v-if="activeCatalog?.description" class="propertyItem">
						<strong>{{ t('opencatalogi', 'Description') }}:</strong>
						<span>{{ activeCatalog.description }}</span>
					</div>
					<div class="propertyItem">
						<strong>{{ t('opencatalogi', 'Registers') }}:</strong>
						<span>{{ activeCatalog?.registers?.length || 0 }}</span>
					</div>
					<div class="propertyItem">
						<strong>{{ t('opencatalogi', 'Schemas') }}:</strong>
						<span>{{ activeCatalog?.schemas?.length || 0 }}</span>
					</div>
				</div>
			</div>

			<!-- Tabs for additional information -->
			<div class="tabContainer">
				<div class="tabHeaders">
					<button
						v-for="(tab, index) in tabs"
						:key="tab"
						class="tabHeader"
						:class="{ active: activeTab === index }"
						@click="activeTab = index">
						{{ tab }}
					</button>
				</div>

				<div class="tabContent">
					<!-- Registers Tab -->
					<div v-if="activeTab === 0" class="tabPanel">
						<div v-if="activeCatalog?.registers?.length > 0" class="itemsGrid">
							<div v-for="registerId in activeCatalog.registers"
								:key="registerId"
								class="itemCard">
								<div class="itemHeader">
									<h3>{{ getRegisterById(registerId)?.title || 'Unknown Register' }}</h3>
								</div>
								<p v-if="getRegisterById(registerId)?.description" class="itemDescription">
									{{ getRegisterById(registerId).description }}
								</p>
							</div>
						</div>
						<div v-else class="emptyTabContent">
							<NcEmptyContent
								:name="t('opencatalogi', 'No registers found')"
								:description="t('opencatalogi', 'This catalog has no associated registers.')">
								<template #icon>
									<DatabaseOutline :size="64" />
								</template>
							</NcEmptyContent>
						</div>
					</div>

					<!-- Schemas Tab -->
					<div v-if="activeTab === 1" class="tabPanel">
						<div v-if="activeCatalog?.schemas?.length > 0" class="itemsGrid">
							<div v-for="schemaId in activeCatalog.schemas"
								:key="schemaId"
								class="itemCard">
								<div class="itemHeader">
									<h3>{{ getSchemaById(schemaId)?.title || 'Unknown Schema' }}</h3>
								</div>
								<p v-if="getSchemaById(schemaId)?.description" class="itemDescription">
									{{ getSchemaById(schemaId).description }}
								</p>
							</div>
						</div>
						<div v-else class="emptyTabContent">
							<NcEmptyContent
								:name="t('opencatalogi', 'No schemas found')"
								:description="t('opencatalogi', 'This catalog has no associated schemas.')">
								<template #icon>
									<FileTreeOutline :size="64" />
								</template>
							</NcEmptyContent>
						</div>
					</div>

					<!-- Organization Tab -->
					<div v-if="activeTab === 2" class="tabPanel">
						<div v-if="organization" class="organizationInfo">
							<h3>{{ organization.name }}</h3>
							<p v-if="organization.description">
								{{ organization.description }}
							</p>
							<div class="organizationActions">
								<NcActions>
									<NcActionButton close-after-click @click="goToOrganization()">
										<template #icon>
											<OpenInApp :size="20" />
										</template>
										View Organization
									</NcActionButton>
								</NcActions>
							</div>
						</div>
						<div v-else class="emptyTabContent">
							<NcEmptyContent
								:name="t('opencatalogi', 'No organization')"
								:description="t('opencatalogi', 'This catalog has no associated organization.')">
								<template #icon>
									<OfficeBuilding :size="64" />
								</template>
							</NcEmptyContent>
						</div>
					</div>
				</div>
			</div>
		</div>

		<template #actions>
			<NcActionButton close-after-click @click="editCatalog">
				<template #icon>
					<Pencil :size="20" />
				</template>
				Edit Catalog
			</NcActionButton>
			<NcActionButton close-after-click @click="viewCatalog">
				<template #icon>
					<OpenInApp :size="20" />
				</template>
				View Catalog
			</NcActionButton>
			<NcActionButton close-after-click @click="deleteCatalog">
				<template #icon>
					<TrashCanOutline :size="20" />
				</template>
				Delete Catalog
			</NcActionButton>
			<NcButton type="primary" @click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Close
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import {
	NcDialog,
	NcButton,
	NcActions,
	NcActionButton,
	NcEmptyContent,
} from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import OfficeBuilding from 'vue-material-design-icons/OfficeBuilding.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'

export default {
	name: 'ViewCatalogi',
	components: {
		NcDialog,
		NcButton,
		NcActions,
		NcActionButton,
		NcEmptyContent,
		Cancel,
		Pencil,
		TrashCanOutline,
		DatabaseOutline,
		FileTreeOutline,
		OfficeBuilding,
		OpenInApp,
	},
	data() {
		return {
			activeTab: 0,
			tabs: [
				this.t('opencatalogi', 'Registers'),
				this.t('opencatalogi', 'Schemas'),
				this.t('opencatalogi', 'Organization'),
			],
		}
	},
	computed: {
		activeCatalog() {
			return objectStore.getActiveObject('catalog')
		},
		organization() {
			return this.activeCatalog?.organization ? objectStore.getObject('organization', this.activeCatalog.organization) : null
		},
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
		},
		editCatalog() {
			navigationStore.setModal('catalog')
		},
		viewCatalog() {
			navigationStore.setModal(false)
			navigationStore.setSelected('publication')
			navigationStore.setSelectedCatalogus(this.activeCatalog?.id)
		},
		deleteCatalog() {
			navigationStore.setModal(false)
			navigationStore.setDialog('deleteObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })
		},
		getRegisterById(id) {
			const availableRegisters = objectStore.availableRegisters
			return availableRegisters.find(register => register.id === id)
		},
		getSchemaById(id) {
			const availableSchemas = objectStore.availableSchemas
			return availableSchemas.find(schema => schema.id === id)
		},
		goToOrganization() {
			if (this.organization) {
				objectStore.setActiveObject('organization', this.organization)
				navigationStore.setModal(false)
				navigationStore.setSelected('organizations')
			}
		},
	},
}
</script>

<style scoped>
.viewCatalogiDialog {
	display: flex;
	flex-direction: column;
	gap: 1.5rem;
}

.catalogDetailsGrid {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.catalogMainInfo h2 {
	margin: 0 0 0.5rem 0;
	color: var(--color-main-text);
}

.catalogSummary {
	color: var(--color-text-lighter);
	margin: 0;
}

.catalogProperties {
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
	padding: 1rem;
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
}

.propertyItem {
	display: flex;
	gap: 0.5rem;
}

.propertyItem strong {
	min-width: 120px;
	color: var(--color-text-lighter);
}

.tabContainer {
	margin-top: 1rem;
}

.tabHeaders {
	display: flex;
	border-bottom: 1px solid var(--color-border);
	margin-bottom: 1rem;
}

.tabHeader {
	padding: 0.75rem 1rem;
	background: none;
	border: none;
	cursor: pointer;
	color: var(--color-text-lighter);
	border-bottom: 2px solid transparent;
	transition: all 0.2s ease;
}

.tabHeader:hover {
	color: var(--color-main-text);
	background-color: var(--color-background-hover);
}

.tabHeader.active {
	color: var(--color-primary);
	border-bottom-color: var(--color-primary);
}

.tabContent {
	min-height: 200px;
}

.tabPanel {
	padding: 1rem 0;
}

.itemsGrid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	gap: 1rem;
}

.itemCard {
	padding: 1rem;
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
	border: 1px solid var(--color-border);
}

.itemHeader {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 0.5rem;
}

.itemHeader h3 {
	margin: 0;
	color: var(--color-main-text);
}

.itemDescription {
	color: var(--color-text-lighter);
	margin: 0;
}

.organizationInfo {
	padding: 1rem;
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
	border: 1px solid var(--color-border);
}

.organizationInfo h3 {
	margin: 0 0 0.5rem 0;
	color: var(--color-main-text);
}

.organizationActions {
	margin-top: 1rem;
}

.emptyTabContent {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 200px;
}
</style>
