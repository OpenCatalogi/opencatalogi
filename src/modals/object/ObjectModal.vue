<script setup>
import { objectStore, navigationStore, catalogStore } from '../../store/store.js'
</script>

<template>
	<div>
		<NcDialog
			id="objectModal"
			:name="dialogTitle"
			size="large"
			:can-close="false">
			<div class="dialog-content">
				<NcNoteCard v-if="success" type="success" class="note-card">
					<p>Publication successfully {{ isNewObject ? 'created' : 'modified' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error" class="note-card">
					<p>{{ error }}</p>
				</NcNoteCard>

				<div v-if="!success">
					<!-- Register and Schema Info with card style -->
					<div class="detail-grid">
						<div class="detail-item" :class="{ 'empty-value': !selectedCatalogus }">
							<span class="detail-label">Catalogus:</span>
							<NcButton v-if="selectedCatalogus"
								class="pencil-button"
								@click="() => {
									selectedCatalogus = null;
									selectedRegister = null;
									selectedSchema = null
								}">
								<Pencil :size="20" />
							</NcButton>
							<div class="detail-value-container">
								<span v-if="selectedCatalogus" class="detail-value">{{ selectedCatalogus?.label || 'Not selected' }}</span>
								<span v-if="selectedCatalogus" class="sub-detail-value">{{ selectedCatalogus?.id }}</span>
								<NcSelect v-if="!selectedCatalogus"
									v-model="selectedCatalogus"
									:options="catalogOptions"
									label-outside
									:disabled="objectStore.isLoading('object')"
									required
									:loading="loading" />
							</div>
						</div>
						<div class="detail-item" :class="{ 'empty-value': !selectedRegister }">
							<span class="detail-label">Register:</span>
							<NcButton v-if="selectedRegister"
								class="pencil-button"
								@click="() => {
									selectedRegister = null;
									selectedSchema = null
								}">
								<Pencil :size="20" />
							</NcButton>
							<div class="detail-value-container">
								<span v-if="selectedRegister" class="detail-value">{{ selectedRegister?.label || 'Not selected' }}</span>
								<span v-if="selectedRegister" class="sub-detail-value">{{ selectedRegister?.id }}</span>
								<NcSelect v-if="!selectedRegister"
									v-model="selectedRegister"
									:options="registerOptions"
									label-outside
									:disabled="objectStore.isLoading('object') || !selectedCatalogus"
									required
									:loading="loading" />
							</div>
						</div>
						<div class="detail-item" :class="{ 'empty-value': !selectedSchema }">
							<span class="detail-label">Schema:</span>
							<NcButton v-if="selectedSchema"
								class="pencil-button"
								@click="() => {
									selectedSchema = null
								}">
								<Pencil :size="20" />
							</NcButton>
							<div class="detail-value-container">
								<span v-if="selectedSchema" class="detail-value">{{ selectedSchema?.label || 'Not selected' }}</span>
								<span v-if="selectedSchema" class="sub-detail-value">{{ selectedSchema?.id }}</span>
								<NcSelect v-if="!selectedSchema"
									v-model="selectedSchema"
									:options="schemaOptions"
									label-outside
									:disabled="objectStore.isLoading('object') || !selectedRegister"
									required
									:loading="loading" />
							</div>
						</div>
					</div>

					<!-- Upload Files Button (only for existing objects) -->
					<!-- <NcButton v-if="!isNewObject"
						type="secondary"
						class="upload-files-btn"
						@click="openUploadFilesModal">
						<template #icon>
							<Plus :size="20" />
						</template>
						Upload Files
					</NcButton> -->

					<!-- Edit Tabs -->
					<div class="tabContainer">
						<BTabs v-model="activeTab" content-class="mt-3" justified>
							<BTab title="Form Editor" active>
								<div v-if="fullSelectedSchema" class="form-editor">
									<div v-for="(prop, key) in schemaProperties" :key="key" class="form-field">
										<template v-if="prop.type === 'string'">
											<NcTextField
												:label="prop.title || key"
												:value="getFieldValue(key)"
												:placeholder="prop.example"
												:helper-text="prop.description"
												:required="prop.required"
												@update:value="value => setFieldValue(key, value)" />
										</template>
										<template v-else-if="prop.type === 'boolean'">
											<NcCheckboxRadioSwitch
												:checked.sync="formData[key]"
												:helper-text="prop.description"
												type="switch">
												{{ prop.title || key }}
											</NcCheckboxRadioSwitch>
										</template>
										<template v-else-if="prop.type === 'number' || prop.type === 'integer'">
											<NcTextField
												:label="prop.title || key"
												:value="getFieldValue(key)"
												:placeholder="prop.example"
												:helper-text="prop.description"
												:required="prop.required"
												type="number"
												:min="prop.minimum"
												:max="prop.maximum"
												:step="prop.type === 'integer' ? '1' : 'any'"
												@update:value="value => setFieldValue(key, value)" />
										</template>
										<template v-else>
											{{ prop.type }}
										</template>
									</div>
								</div>
								<NcEmptyContent v-else>
									Please select a schema to edit the publication
								</NcEmptyContent>
							</BTab>

							<BTab title="JSON Editor">
								<div class="json-editor">
									<div :class="`codeMirrorContainer ${getTheme()}`">
										<CodeMirror
											v-model="jsonData"
											:basic="true"
											:disabled="!fullSelectedSchema"
											placeholder="{ &quot;key&quot;: &quot;value&quot; }"
											:dark="getTheme() === 'dark'"
											:linter="jsonParseLinter()"
											:lang="json()"
											:extensions="[json()]"
											:tab-size="2"
											style="height: 400px" />
										<NcButton
											class="format-json-button"
											type="secondary"
											size="small"
											:disabled="!fullSelectedSchema"
											@click="formatJSON">
											Format JSON
										</NcButton>
									</div>
									<span v-if="!isValidJson(jsonData)" class="error-message">
										Invalid JSON format
									</span>
								</div>
							</BTab>
						</BTabs>
					</div>
				</div>
			</div>

			<template #actions>
				<NcButton @click="closeModal">
					<template #icon>
						<Cancel :size="20" />
					</template>
					{{ success ? 'Close' : 'Cancel' }}
				</NcButton>

				<NcButton
					:disabled="loading || (activeTab === 1 && !isValidJson(jsonData))"
					type="primary"
					@click="saveObject">
					<template #icon>
						<NcLoadingIcon v-if="loading" :size="20" />
						<ContentSaveOutline v-else-if="!isNewObject" :size="20" />
						<Plus v-else :size="20" />
					</template>
					{{ isNewObject ? 'Add' : 'Save' }}
				</NcButton>
			</template>
		</NcDialog>
		<!-- Add the UploadFiles modal for file uploads -->
		<!-- <UploadFiles /> -->
	</div>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcTextField,
	NcCheckboxRadioSwitch,
	NcEmptyContent,
	NcLoadingIcon,
	NcNoteCard,
	NcSelect,
} from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'
import { getTheme } from '../../services/getTheme.js'
import { json, jsonParseLinter } from '@codemirror/lang-json'

import CodeMirror from 'vue-codemirror6'
import _ from 'lodash'

// Icons
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'ObjectModal',
	components: {
		NcButton,
		NcDialog,
		NcTextField,
		NcSelect,
		NcCheckboxRadioSwitch,
		NcEmptyContent,
		BTabs,
		BTab,
		CodeMirror,
	},
	data() {
		return {
			activeTab: 0,
			isNewObject: false,
			loading: false,
			error: null,
			success: null,
			closeModalTimeout: null,

			selectedCatalogus: null,
			selectedRegister: null,
			selectedSchema: null,

			formData: {},
			jsonData: '',
		}
	},
	computed: {
		catalogOptions() {
			return objectStore.getCollection('catalog').results.map(catalog => ({
				id: catalog.id,
				label: catalog.title,
			}))
		},
		registerOptions() {
			if (!this.selectedCatalogus) {
				return []
			}

			const fullCatalog = objectStore.getCollection('catalog').results.find(catalog => catalog.id === this.selectedCatalogus.id)
			const selectedCatalogRegistersIds = fullCatalog.registers

			return objectStore.availableRegisters
				.filter(register => selectedCatalogRegistersIds.includes(register.id))
				.map(register => ({
					id: register.id,
					label: register.title,
				}))
		},
		schemaOptions() {
			if (!this.selectedRegister || !this.selectedCatalogus) {
				return []
			}

			const register = objectStore.availableRegisters.find(register => register.id === this.selectedRegister.id)
			const catalog = objectStore.getCollection('catalog').results.find(catalog => catalog.id === this.selectedCatalogus.id)

			const registerSchemaIds = register.schemas.map(schema => schema.id)
			const catalogSchemaIds = catalog.schemas

			// only get schema ids where the id is in both registerSchemaIds and catalogSchemaIds
			const validSchemaIds = registerSchemaIds.filter(id => catalogSchemaIds.includes(id))

			return objectStore.availableSchemas
				.filter(schema => validSchemaIds.includes(schema.id))
				.map(schema => ({
					id: schema.id,
					label: schema.title,
				}))
		},
		fullSelectedSchema() {
			return objectStore.availableSchemas.find(schema => schema.id === this.selectedSchema?.id)
		},
		schemaProperties() {
			return this.fullSelectedSchema?.properties || {}
		},
		dialogTitle() {
			return this.isNewObject ? 'Add Publication' : 'Edit Publication'
		},
	},
	watch: {
		objectStore: {
			handler(newValue) {
				if (newValue) {
					this.initializeData()
				}
			},
			deep: true,
		},
		jsonData: {
			handler(newValue) {
				if (this.activeTab === 1 && this.isValidJson(newValue)) {
					this.updateFormFromJson()
				}
			},
		},
		formData: {
			handler(newValue) {
				if (this.activeTab === 0) {
					this.updateJsonFromForm()
				}
			},
			deep: true,
		},
	},
	mounted() {
		this.initializeData()
	},
	methods: {
		initializeData() {
			const activeCatalog = objectStore.getActiveObject('catalog')

			// Set selected catalog based on active catalog
			const catalogMatch = objectStore.getCollection('catalog').results
				.find(catalog => catalog.id === activeCatalog.id)

			this.selectedCatalogus = catalogMatch
				? {
					id: catalogMatch.id,
					label: catalogMatch.title,
				}
				: null

			const activeObject = objectStore.getActiveObject('publication')
			this.isNewObject = !activeObject?.['@self']?.id

			if (!this.isNewObject) { // is edit modal
				// Initialize form with existing object data

				this.formData = _.cloneDeep(activeObject)
				this.jsonData = JSON.stringify(activeObject, null, 2)

				// Set register and schema from existing object
				const register = objectStore.availableRegisters
					.find(register => String(register.id) === String(activeObject['@self'].register))
				this.selectedRegister = register && {
					id: register.id,
					label: register.title,
				}

				const schema = objectStore.availableSchemas
					.find(schema => String(schema.id) === String(activeObject['@self'].schema))
				this.selectedSchema = schema && {
					id: schema.id,
					label: schema.title,
				}
			} else {
				// For new objects, auto-select register/schema if catalog only has one
				if (activeCatalog.registers.length === 1) {
					const register = objectStore.availableRegisters
						.find(register => register.id === activeCatalog.registers[0])
					this.selectedRegister = register && {
						id: register.id,
						label: register.title,
					}

					if (activeCatalog.schemas.length === 1) {
						const schema = objectStore.availableSchemas
							.find(schema => schema.id === activeCatalog.schemas[0])
						this.selectedSchema = schema && {
							id: schema.id,
							label: schema.title,
						}
					}
				}
			}
		},
		async saveObject() {
			if (!this.selectedRegister || !this.selectedSchema) {
				this.error = 'Register and schema are required'
				return
			}

			this.loading = true
			this.error = null

			const method = this.isNewObject ? 'POST' : 'PUT'
			const BASE_URL = `/index.php/apps/openregister/api/objects/${this.selectedRegister.id}/${this.selectedSchema.id}`
			const FETCH_URL = `${BASE_URL}${this.isNewObject ? '' : `/${this.selectedSchema.id}`}`
			try {
				let dataToSave
				if (this.activeTab === 1) {
					if (!this.jsonData.trim()) {
						throw new Error('JSON data cannot be empty')
					}
					try {
						dataToSave = JSON.parse(this.jsonData)
					} catch (e) {
						throw new Error('Invalid JSON format: ' + e.message)
					}
				} else {
					dataToSave = this.formData
				}

				const response = await fetch(FETCH_URL, {
					method,
					body: JSON.stringify(dataToSave),
					headers: {
						'Content-Type': 'application/json',
					},
				})

				this.success = response.ok
				if (response.ok) {
					const newPublication = await response.json()
					this.closeModalTimeout = setTimeout(this.closeModal, 2000)
					catalogStore.refreshPublications()
					objectStore.setActiveObject('publication', newPublication)
				}
				catalogStore.fetchPublications()
				response.json().then((data) => {
					objectStore.setActiveObject('publication', { ...data, id: data.id || data['@self'].id })
				})
			} catch (e) {
				this.error = e.message || 'Failed to save object'
				this.success = false
			} finally {
				this.loading = false

			}
		},
		updateFormFromJson() {
			try {
				const parsed = JSON.parse(this.jsonData)
				this.formData = parsed
			} catch (e) {
				this.error = 'Invalid JSON format'
			}
		},

		updateJsonFromForm() {
			try {
				this.jsonData = JSON.stringify(this.formData, null, 2)
			} catch (e) {
				console.error('Error updating JSON:', e)
			}
		},

		isValidJson(str) {
			if (!str || !str.trim()) {
				return false
			}
			try {
				JSON.parse(str)
				return true
			} catch (e) {
				return false
			}
		},

		formatJSON() {
			try {
				if (this.jsonData) {
					const parsed = JSON.parse(this.jsonData)
					this.jsonData = JSON.stringify(parsed, null, 2)
				}
			} catch (e) {
				// Keep invalid JSON as-is
			}
		},

		closeModal() {
			navigationStore.setModal(false)
			clearTimeout(this.closeModalTimeout)
			this.success = null
			this.loading = false
			this.error = null
			this.formData = {}
			this.jsonData = ''
		},

		getFieldValue(key) {
			return this.formData[key] ?? ''
		},

		setFieldValue(key, value) {
			if (this.formData[key] === value) return
			this.$set(this.formData, key, value)
		},

		openUploadFilesModal() {
			// Set the navigationStore modal to 'uploadFiles' to show the UploadFiles modal
			navigationStore.setModal('uploadFiles')
		},
	},
}
</script>

<style scoped>
:deep(.modal-container) {
    width: 937px !important;
}

/* Add consistent dialog content spacing */
.dialog-content {
	padding: 0 20px;
}

/* Update note card margins */
:deep(.note-card) {
	margin: 20px 0;
}

/* Update detail grid margins */
.detail-grid {
	display: grid;
	grid-template-columns: 1fr 1fr 1fr;
	gap: 12px;
	margin: 20px 0;
	max-width: 100%;
}

.detail-item {
    display: grid;
    grid-template-columns: 1fr auto;
    grid-template-areas:
        "label button"
        "value value";
    gap: 8px;
    padding: 12px;
    background-color: var(--color-background-hover);
    border-radius: 4px;
    border-left: 3px solid var(--color-primary);
}

.detail-label {
    grid-area: label;
    font-weight: bold;
    color: var(--color-text-maxcontrast);
}

.pencil-button {
    grid-area: button;
}

.detail-value-container {
    grid-area: value;
    display: flex;
    flex-direction: column;
}

.detail-value {
    word-break: break-word;
}
.sub-detail-value {
    word-break: break-word;
    font-size: 0.8rem;
    color: var(--color-text-maxcontrast);
}

.detail-item.empty-value {
    border-left-color: var(--color-warning);
}

.edit-tabs {
	margin-top: 20px;
}

.form-field {
	margin-bottom: 16px;
}

/* JSON Editor styles */
.json-editor {
	position: relative;
	margin-bottom: 2.5rem;
}

.codeMirrorContainer {
	margin-block-start: 6px;
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius);
	position: relative;
}

.codeMirrorContainer :deep(.cm-editor) {
	height: 100%;
}

.codeMirrorContainer :deep(.cm-scroller) {
	overflow: auto;
}

.format-json-button {
	position: absolute;
	bottom: 0;
	right: 0;
	transform: translateY(100%);
}

.error-message {
	position: absolute;
	bottom: 0;
	right: 50%;
	transform: translateY(100%) translateX(50%);
	color: var(--color-error);
	font-size: 0.8rem;
	padding-top: 0.25rem;
}

/* Dark mode specific styles */
.codeMirrorContainer.dark :deep(.cm-editor) {
	background-color: var(--color-background-darker);
}

.codeMirrorContainer.light :deep(.cm-editor) {
	background-color: var(--color-background-hover);
}

/* Add tab container styles */
.tabContainer {
	margin-top: 20px;
}

/* Style the tabs to match ViewObject */
:deep(.nav-tabs) {
	border-bottom: 1px solid var(--color-border);
	margin-bottom: 15px;
}

:deep(.nav-tabs .nav-link) {
	border: none;
	border-bottom: 2px solid transparent;
	color: var(--color-text-maxcontrast);
	padding: 8px 16px;
}

:deep(.nav-tabs .nav-link.active) {
	color: var(--color-main-text);
	border-bottom: 2px solid var(--color-primary);
	background-color: transparent;
}

:deep(.nav-tabs .nav-link:hover) {
	border-bottom: 2px solid var(--color-border);
}

:deep(.tab-content) {
	padding: 16px;
	background-color: var(--color-main-background);
}

/* Form editor specific styles */
.form-editor {
	display: flex;
	flex-direction: column;
	padding: 16px;
}

.form-field {
	margin-bottom: 16px;
}

/* CodeMirror */
.codeMirrorContainer {
	margin-block-start: 6px;
}

.codeMirrorContainer :deep(.cm-content) {
	border-radius: 0 !important;
	border: none !important;
}
.codeMirrorContainer :deep(.cm-editor) {
	outline: none !important;
}
.codeMirrorContainer.light > .vue-codemirror {
	border: 1px dotted silver;
}
.codeMirrorContainer.dark > .vue-codemirror {
	border: 1px dotted grey;
}

/* value text color */
/* string */
.codeMirrorContainer.light :deep(.ͼe) {
	color: #448c27;
}
.codeMirrorContainer.dark :deep(.ͼe) {
	color: #88c379;
}

/* boolean */
.codeMirrorContainer.light :deep(.ͼc) {
	color: #221199;
}
.codeMirrorContainer.dark :deep(.ͼc) {
	color: #8d64f7;
}

/* null */
.codeMirrorContainer.light :deep(.ͼb) {
	color: #770088;
}
.codeMirrorContainer.dark :deep(.ͼb) {
	color: #be55cd;
}

/* number */
.codeMirrorContainer.light :deep(.ͼd) {
	color: #d19a66;
}
.codeMirrorContainer.dark :deep(.ͼd) {
	color: #9d6c3a;
}

/* text cursor */
.codeMirrorContainer :deep(.cm-content) * {
	cursor: text !important;
}

/* selection color */
.codeMirrorContainer.light :deep(.cm-line)::selection,
.codeMirrorContainer.light :deep(.cm-line) ::selection {
	background-color: #d7eaff !important;
    color: black;
}
.codeMirrorContainer.dark :deep(.cm-line)::selection,
.codeMirrorContainer.dark :deep(.cm-line) ::selection {
	background-color: #8fb3e6 !important;
    color: black;
}

/* string */
.codeMirrorContainer.light :deep(.cm-line .ͼe)::selection {
    color: #2d770f;
}
.codeMirrorContainer.dark :deep(.cm-line .ͼe)::selection {
    color: #104e0c;
}

/* boolean */
.codeMirrorContainer.light :deep(.cm-line .ͼc)::selection {
	color: #221199;
}
.codeMirrorContainer.dark :deep(.cm-line .ͼc)::selection {
	color: #4026af;
}

/* null */
.codeMirrorContainer.light :deep(.cm-line .ͼb)::selection {
	color: #770088;
}
.codeMirrorContainer.dark :deep(.cm-line .ͼb)::selection {
	color: #770088;
}

/* number */
.codeMirrorContainer.light :deep(.cm-line .ͼd)::selection {
	color: #8c5c2c;
}
.codeMirrorContainer.dark :deep(.cm-line .ͼd)::selection {
	color: #623907;
}
</style>
