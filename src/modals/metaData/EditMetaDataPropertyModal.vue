<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'editMetadataDataModal'"
		ref="modalRef"
		label-id="editMetaDataPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Eigenschap "{{ metadataStore.metadataDataKey }}" bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Eigenschap succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="success === null && metadata.properties[metadataStore.metadataDataKey]" class="form-group">
				<NcTextField
					:disabled="loading"
					label="Title*"
					required
					:value.sync="metadata.properties[metadataStore.metadataDataKey].title" />

				<NcTextField :disabled="loading"
					label="Beschrijving"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].description" />

				<NcSelect v-bind="typeOptions"
					v-model="metadata.properties[metadataStore.metadataDataKey].type"
					required />

				<NcSelect v-bind="formatOptions"
					v-model="metadata.properties[metadataStore.metadataDataKey].format" />

				<NcTextField
					:disabled="loading"
					label="Patroon (regex)"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].pattern" />

				<NcTextField v-if="!metadata.properties[metadataStore.metadataDataKey].type"
					:disabled="loading"
					label="Default waarde"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default" />

				<!-- TYPE : STRING -->
				<div v-if="metadata.properties[metadataStore.metadataDataKey].type === 'string'">
					<NcDateTimePicker v-if="metadata.properties[metadataStore.metadataDataKey].format === 'date'"
						v-model="metadata.properties[metadataStore.metadataDataKey].default"
						type="date"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'time'"
						v-model="metadata.properties[metadataStore.metadataDataKey].default"
						type="time"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'date-time'"
						v-model="metadata.properties[metadataStore.metadataDataKey].default"
						type="datetime"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'email'"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						type="email"
						label="Default waarde (Email)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'idn-email'"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						type="email"
						label="Default waarde (Email)"
						helper-text="email"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'regex'"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						label="Default waarde (Regex)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'password'"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						type="password"
						label="Default waarde (Wachtwoord)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].format === 'telephone'"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						type="tel"
						label="Default waarde (Telefoonnummer)"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else
						:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />
				</div>

				<!-- TYPE : NUMBER -->
				<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'number'"
					:disabled="loading"
					type="number"
					step="any"
					label="Default waarde"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
					:loading="loading" />
				<!-- TYPE : INTEGER -->
				<NcInputField v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'integer'"
					:disabled="loading"
					type="number"
					step="1"
					label="Default waarde"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
					:loading="loading" />
				<!-- TYPE : OBJECT -->
				<NcTextArea v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'object'"
					:disabled="loading"
					label="Default waarde"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
					:loading="loading" />
				<!-- TYPE : ARRAY -->
				<NcTextArea v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'array'"
					:disabled="loading"
					label="Waarde lijst (split op ,)"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default"
					:loading="loading" />
				<!-- TYPE : BOOLEAN -->
				<NcCheckboxRadioSwitch v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'boolean'"
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].default"
					:loading="loading">
					Default waarde
				</NcCheckboxRadioSwitch>

				<!-- TYPE : DICTIONARY -->
				<NcTextField v-else-if="metadata.properties[metadataStore.metadataDataKey].type === 'dictionary'"
					:disabled="loading"
					label="Default waarde"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default" />

				<NcTextField :disabled="loading"
					label="Gedrag"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].behavior" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].required">
					Verplicht
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].deprecated">
					Verouderd
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					label="Minimum lengte"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].minLength" />

				<NcTextField :disabled="loading"
					label="Maximum lengte"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].maxLength" />

				<NcTextField :disabled="loading"
					label="Voorbeeld"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].example" />

				<!-- type integer and number only -->
				<div v-if="metadata.properties[metadataStore.metadataDataKey].type === 'integer' || metadata.properties[metadataStore.metadataDataKey].type === 'number'">
					<h5 class="weightNormal">
						type: nummer
					</h5>

					<NcTextField :disabled="loading"
						label="Minimum waarde"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].minimum" />

					<NcTextField :disabled="loading"
						label="Maximum waarde"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].maximum" />

					<NcTextField :disabled="loading"
						label="Deelbaar door"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].multipleOf" />

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="metadata.properties[metadataStore.metadataDataKey].exclusiveMin">
						Exclusief minimum
					</NcCheckboxRadioSwitch>

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="metadata.properties[metadataStore.metadataDataKey].exclusiveMax">
						Exclusief maximum
					</NcCheckboxRadioSwitch>
				</div>

				<!-- type array only -->
				<div v-if="metadata.properties[metadataStore.metadataDataKey].type === 'array'">
					<h5 class="weightNormal">
						type: array
					</h5>

					<NcTextField :disabled="loading"
						label="Minimale hoeveelheid items"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].minItems" />

					<NcTextField :disabled="loading"
						label="Minimale hoeveelheid items"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].maxItems" />
				</div>
			</div>

			<NcButton v-if="success === null && metadata.properties[metadataStore.metadataDataKey]"
				:disabled="!metadata.properties[metadataStore.metadataDataKey].title || !metadata.properties[metadataStore.metadataDataKey].type || loading"
				type="primary"
				@click="updateMetadata(metadata.id)">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<ContentSaveOutline v-if="!loading" :size="20" />
					</span>
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcSelect,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcLoadingIcon,
	NcDateTimePicker,
	NcInputField,
	NcTextArea,
} from '@nextcloud/vue'

// icons
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditMetaDataPropertyModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcCheckboxRadioSwitch,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		NcDateTimePicker,
		NcInputField,
		NcTextArea,
	},
	data() {
		return {
			metadata: {
				properties: {
					title: '',
					description: '',
					type: '',
					format: '',
					pattern: '',
					default: '',
					behavior: '',
					required: false,
					deprecated: false,
					minLength: 0,
					maxLength: 0,
					example: '',
					minimum: 0,
					maximum: 0,
					multipleOf: 0,
					exclusiveMin: false,
					exclusiveMax: false,
					minItems: 0,
					maxItems: 0,
				},
			},
			dataKey: '',
			typeOptions: {
				inputLabel: 'Type*',
				multiple: false,
				options: ['string', 'number', 'integer', 'object', 'array', 'boolean', 'dictionary'],
			},
			formatOptions: {
				inputLabel: 'Format',
				multiple: false,
				options: ['date', 'time', 'duration', 'date-time', 'url', 'uri', 'uuid', 'email', 'idn-email', 'hostname', 'idn-hostname', 'ipv4', 'ipv6', 'uri-reference', 'iri', 'iri-reference', 'uri-template', 'json-pointer', 'regex', 'binary', 'byte', 'password', 'rsin', 'kvk', 'bsn', 'oidn', 'telephone'],
			},
			loading: false,
			error: false,
			success: null,
			successMessage: '',
			hasUpdated: false,
		}
	},
	computed: {
		metadataProperty() {
			return Object.assign({}, this.metadata.properties[metadataStore.metadataDataKey] && this.metadata.properties[metadataStore.metadataDataKey])
		},
	},
	watch: {
		metadataProperty: {
			deep: true,
			handler(newVal, oldVal) {
				if (newVal.type !== oldVal.type) {

					if (newVal.type === 'boolean' && newVal.default === 'true') this.metadata.properties[metadataStore.metadataDataKey].default = true
					if (newVal.type === 'boolean' && newVal.default !== 'true') this.metadata.properties[metadataStore.metadataDataKey].default = false
					if (newVal.type !== 'boolean' && oldVal.type === 'boolean') this.metadata.properties[metadataStore.metadataDataKey].default = ''
				}
			},
		},
	},
	updated() {
		if (navigationStore.modal === 'editMetadataDataModal' && this.hasUpdated) {
			if (this.dataKey !== metadataStore.metadataDataKey) this.hasUpdated = false
		}
		if (navigationStore.modal === 'editMetadataDataModal' && !this.hasUpdated) {

			this.metadata = this.addMissingProperties(metadataStore.metaDataItem)

			this.fetchData(metadataStore.metaDataItem.id)

			this.dataKey = metadataStore.metadataDataKey
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		updateMetadata(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.metadata,
						properties: { // due to bad (no) support for number fields inside nextcloud/vue, parse the text to a number
							...this.metadata.properties,
							[metadataStore.metadataDataKey]: {
								...this.metadata.properties[metadataStore.metadataDataKey],
								minLength: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minLength) || null,
								maxLength: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maxLength) || null,
								minimum: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minimum) || null,
								maximum: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maximum) || null,
								multipleOf: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].multipleOf) || null,
								minItems: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minItems) || null,
								maxItems: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maxItems) || null,
							},
						},
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					setTimeout(() => {
						navigationStore.setModal(false)
					    this.success = null
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.success = null
					this.error = err
				})
		},
		addMissingProperties(data) {
			Object.entries(data.properties).forEach(function(property) {
				data.properties[property[0]] = {
					title: property[1].title ?? property[0] ?? '',
					description: property[1].description ?? '',
					type: property[1].type ?? '',
					format: property[1].format ?? '',
					pattern: property[1].pattern ?? '',
					default: property[1].default.toString() ?? '',
					behavior: property[1].behavior ?? '',
					required: property[1].required ?? false,
					deprecated: property[1].deprecated ?? false,
					minLength: property[1].minLength ?? 0,
					maxLength: property[1].maxLength ?? 0,
					example: property[1].example ?? '',
					minimum: property[1].minimum ?? 0,
					maximum: property[1].maximum ?? 0,
					multipleOf: property[1].multipleOf ?? 0,
					exclusiveMin: property[1].exclusiveMin ?? false,
					exclusiveMax: property[1].exclusiveMax ?? false,
					minItems: property[1].minItems ?? 0,
					maxItems: property[1].maxItems ?? 0,
				}
			})

			return {
				...data,
			}
		},
	},
}
</script>

<style>
.modal__content {
  margin: var(--OC-margin-50);
  text-align: center;
}

.form-group .group {
    margin-block-end: 2rem;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}
</style>
