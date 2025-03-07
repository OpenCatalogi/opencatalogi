<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'editPublicationTypeProperty'"
		ref="modalRef"
		label-id="editPublicationTypeProperty"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Eigenschap "{{ publicationTypeStore.publicationTypeDataKey }}" bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Eigenschap succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="success === null && publicationType.properties[publicationTypeStore.publicationTypeDataKey]" class="form-group">
				<NcTextField
					:disabled="loading"
					label="Title*"
					required
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].title" />

				<NcTextField :disabled="loading"
					label="Beschrijving"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].description" />

				<NcSelect v-bind="typeOptions"
					v-model="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type"
					required />

				<NcSelect v-bind="formatOptions"
					v-model="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format" />

				<NcTextField
					:disabled="loading"
					label="Patroon (regex)"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].pattern" />

				<NcTextField v-if="!publicationType.properties[publicationTypeStore.publicationTypeDataKey].type"
					:disabled="loading"
					label="Default waarde"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default" />

				<!-- TYPE : STRING -->
				<div v-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'string'">
					<NcDateTimePicker v-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'date'"
						v-model="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="date"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'time'"
						v-model="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="time"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'date-time'"
						v-model="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="datetime"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'email'"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="email"
						label="Default waarde (Email)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'idn-email'"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="email"
						label="Default waarde (Email)"
						helper-text="email"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'regex'"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						label="Default waarde (Regex)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'password'"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="password"
						label="Default waarde (Wachtwoord)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].format === 'telephone'"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						type="tel"
						label="Default waarde (Telefoonnummer)"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />
				</div>

				<!-- TYPE : NUMBER -->
				<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'number'"
					:disabled="loading"
					type="number"
					step="any"
					label="Default waarde"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
					:loading="loading" />
				<!-- TYPE : INTEGER -->
				<NcInputField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'integer'"
					:disabled="loading"
					type="number"
					step="1"
					label="Default waarde"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
					:loading="loading" />
				<!-- TYPE : OBJECT -->
				<NcTextArea v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'object'"
					:disabled="loading"
					label="Default waarde"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
					:loading="loading" />
				<!-- TYPE : ARRAY -->
				<NcTextArea v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'array'"
					:disabled="loading"
					label="Waarde lijst (split op ,)"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
					:loading="loading" />
				<!-- TYPE : BOOLEAN -->
				<NcCheckboxRadioSwitch v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'boolean'"
					:disabled="loading"
					:checked.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default"
					:loading="loading">
					Default waarde
				</NcCheckboxRadioSwitch>

				<!-- TYPE : DICTIONARY -->
				<NcTextField v-else-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'dictionary'"
					:disabled="loading"
					label="Default waarde"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].default" />

				<NcTextField :disabled="loading"
					label="Gedrag"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].behavior" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].required">
					Verplicht
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].deprecated">
					Verouderd
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					label="Minimum lengte"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].minLength" />

				<NcTextField :disabled="loading"
					label="Maximum lengte"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].maxLength" />

				<NcTextField :disabled="loading"
					label="Voorbeeld"
					:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].example" />

				<!-- type integer and number only -->
				<div v-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'integer' || publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'number'">
					<h5 class="weightNormal">
						type: nummer
					</h5>

					<NcTextField :disabled="loading"
						label="Minimum waarde"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].minimum" />

					<NcTextField :disabled="loading"
						label="Maximum waarde"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].maximum" />

					<NcTextField :disabled="loading"
						label="Deelbaar door"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].multipleOf" />

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].exclusiveMin">
						Exclusief minimum
					</NcCheckboxRadioSwitch>

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].exclusiveMax">
						Exclusief maximum
					</NcCheckboxRadioSwitch>
				</div>

				<!-- type array only -->
				<div v-if="publicationType.properties[publicationTypeStore.publicationTypeDataKey].type === 'array'">
					<h5 class="weightNormal">
						type: array
					</h5>

					<NcTextField :disabled="loading"
						label="Minimale hoeveelheid items"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].minItems" />

					<NcTextField :disabled="loading"
						label="Minimale hoeveelheid items"
						:value.sync="publicationType.properties[publicationTypeStore.publicationTypeDataKey].maxItems" />
				</div>
			</div>

			<NcButton v-if="success === null && publicationType.properties[publicationTypeStore.publicationTypeDataKey]"
				:disabled="!publicationType.properties[publicationTypeStore.publicationTypeDataKey].title || !publicationType.properties[publicationTypeStore.publicationTypeDataKey].type || loading"
				type="primary"
				@click="updatePublicationType(publicationType.id)">
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

import { PublicationType } from '../../entities/index.js'

export default {
	name: 'EditPublicationTypePropertyModal',
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
			publicationType: {
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
		publicationTypeProperty() {
			return Object.assign({}, this.publicationType.properties[publicationTypeStore.publicationTypeDataKey] && this.publicationType.properties[publicationTypeStore.publicationTypeDataKey])
		},
	},
	watch: {
		publicationTypeProperty: {
			deep: true,
			handler(newVal, oldVal) {
				if (newVal.type !== oldVal.type) {

					if (newVal.type === 'boolean' && newVal.default === 'true') this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].default = true
					if (newVal.type === 'boolean' && newVal.default !== 'true') this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].default = false
					if (newVal.type !== 'boolean' && oldVal.type === 'boolean') this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].default = ''
				}
			},
		},
	},
	updated() {
		if (navigationStore.modal === 'editPublicationTypeProperty' && this.hasUpdated) {
			if (this.dataKey !== publicationTypeStore.publicationTypeDataKey) this.hasUpdated = false
		}

		if (navigationStore.modal === 'editPublicationTypeProperty' && !this.hasUpdated) {

			this.publicationType = this.addMissingProperties(publicationTypeStore.publicationTypeItem)

			this.fetchData(publicationTypeStore.publicationTypeItem.id)

			this.dataKey = publicationTypeStore.publicationTypeDataKey
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true

			publicationTypeStore.getOnePublicationType(id)
				.then(({ response }) => {
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		updatePublicationType(id) {
			this.loading = true

			const newPublicationTypeItem = new PublicationType({
				...this.publicationType,
				properties: { // due to bad (no) support for number fields inside nextcloud/vue, parse the text to a number
					...this.publicationType.properties,
					[publicationTypeStore.publicationTypeDataKey]: {
						...this.publicationType.properties[publicationTypeStore.publicationTypeDataKey],
						minLength: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].minLength) || null,
						maxLength: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].maxLength) || null,
						minimum: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].minimum) || null,
						maximum: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].maximum) || null,
						multipleOf: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].multipleOf) || null,
						minItems: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].minItems) || null,
						maxItems: parseFloat(this.publicationType.properties[publicationTypeStore.publicationTypeDataKey].maxItems) || null,
					},
				},
			})

			publicationTypeStore.editPublicationType(newPublicationTypeItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

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
