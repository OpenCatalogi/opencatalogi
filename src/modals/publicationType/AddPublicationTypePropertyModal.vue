<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'addPublicationTypeProperty'"
		ref="modalRef"
		label-id="addPublicationTypeProperty"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Eigenschap toevoegen</h2>
			<NcNoteCard v-if="success" type="success">
				<p>Eigenschap succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>

			<div v-if="!success" class="form-group">
				<NcTextField :disabled="loading"
					label="Eigenschap naam*"
					:error="checkIfTitleIsUnique(properties.name)"
					:helper-text="checkIfTitleIsUnique(properties.name) ? 'Deze naam is al in gebruik. Kies een andere naam.' : ''"
					:value.sync="properties.name" />

				<NcTextField :disabled="loading"
					label="Titel*"
					:value.sync="properties.title" />

				<NcTextField :disabled="loading"
					label="Beschrijving"
					:value.sync="properties.description" />

				<NcSelect v-bind="typeOptions"
					v-model="properties.type"
					required />

				<NcSelect v-bind="formatOptions"
					v-model="properties.format" />

				<NcTextField :disabled="loading"
					label="Patroon (regex)"
					:value.sync="properties.pattern" />

				<NcTextField v-if="!properties.type"
					:disabled="loading || !properties.type"
					label="Default waarde"
					:value.sync="properties.default" />

				<!-- TYPE : STRING -->
				<div v-if="properties.type === 'string'">
					<NcDateTimePicker v-if="properties.format === 'date'"
						v-model="properties.default"
						type="date"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="properties.format === 'time'"
						v-model="properties.default"
						type="time"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcDateTimePicker v-else-if="properties.format === 'date-time'"
						v-model="properties.default"
						type="datetime"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="properties.format === 'email'"
						:value.sync="properties.default"
						type="email"
						label="Default waarde (Email)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="properties.format === 'idn-email'"
						:value.sync="properties.default"
						type="email"
						label="Default waarde (Email)"
						helper-text="email"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else-if="properties.format === 'regex'"
						:value.sync="properties.default"
						label="Default waarde (Regex)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="properties.format === 'password'"
						:value.sync="properties.default"
						type="password"
						label="Default waarde (Wachtwoord)"
						:disabled="loading"
						:loading="loading" />

					<NcInputField v-else-if="properties.format === 'telephone'"
						:value.sync="properties.default"
						type="tel"
						label="Default waarde (Telefoonnummer)"
						:disabled="loading"
						:loading="loading" />

					<NcTextField v-else
						:value.sync="properties.default"
						label="Default waarde"
						:disabled="loading"
						:loading="loading" />
				</div>

				<!-- TYPE : NUMBER -->
				<NcInputField v-else-if="properties.type === 'number'"
					:disabled="loading"
					type="number"
					step="any"
					label="Default waarde"
					:value.sync="properties.default"
					:loading="loading" />
				<!-- TYPE : INTEGER -->
				<NcInputField v-else-if="properties.type === 'integer'"
					:disabled="loading"
					type="number"
					step="1"
					label="Default waarde"
					:value.sync="properties.default"
					:loading="loading" />
				<!-- TYPE : OBJECT -->
				<NcTextArea v-else-if="properties.type === 'object'"
					:disabled="loading"
					label="Default waarde"
					:value.sync="properties.default"
					:loading="loading" />
				<!-- TYPE : ARRAY -->
				<NcTextArea v-else-if="properties.type === 'array'"
					:disabled="loading"
					label="Waarde lijst (split op ,)"
					:value.sync="properties.default"
					:loading="loading" />
				<!-- TYPE : BOOLEAN -->
				<NcCheckboxRadioSwitch v-else-if="properties.type === 'boolean'"
					:disabled="loading"
					:checked.sync="properties.default"
					:loading="loading">
					Default waarde
				</NcCheckboxRadioSwitch>

				<!-- TYPE : STRING -->
				<NcTextField v-else-if="properties.type === 'dictionary'"
					:disabled="loading || !properties.type"
					label="Default waarde"
					:value.sync="properties.default" />

				<NcTextField :disabled="loading"
					label="Gedrag"
					:value.sync="properties.behavior" />
				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="properties.required">
					Verplicht
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="properties.deprecated">
					Verouderd
				</NcCheckboxRadioSwitch>

				<NcInputField :disabled="loading"
					type="number"
					label="Minimum lengte"
					:value.sync="properties.minLength" />

				<NcInputField :disabled="loading"
					type="number"
					label="Maximum lengte"
					:value.sync="properties.maxLength" />

				<NcTextField :disabled="loading"
					label="Voorbeeld"
					:value.sync="properties.example" />

				<!-- type integer and number only -->
				<div v-if="properties.type === 'integer' || properties.type === 'number'">
					<h5 class="weightNormal">
						type: nummer
					</h5>

					<NcInputField :disabled="loading"
						type="number"
						label="Minimum waarde"
						:value.sync="properties.minimum" />

					<NcInputField :disabled="loading"
						type="number"
						label="Maximum waarde"
						:value.sync="properties.maximum" />

					<NcInputField :disabled="loading"
						type="number"
						label="Deelbaar door"
						:value.sync="properties.multipleOf" />

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="properties.exclusiveMin">
						Exclusief minimum
					</NcCheckboxRadioSwitch>

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="properties.exclusiveMax">
						Exclusief maximum
					</NcCheckboxRadioSwitch>
				</div>

				<!-- type array only -->
				<div v-if="properties.type === 'array'">
					<h5 class="weightNormal">
						type: array
					</h5>

					<NcInputField :disabled="loading"
						type="number"
						label="Minimale hoeveelheid items"
						:value.sync="properties.minItems" />

					<NcInputField :disabled="loading"
						type="number"
						label="Minimale hoeveelheid items"
						:value.sync="properties.maxItems" />
				</div>
			</div>

			<NcButton v-if="!success"
				:disabled="!properties.title || !properties.type || loading || checkIfTitleIsUnique(properties.title)"
				type="primary"
				@click="addPublicationType()">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Plus v-if="!loading" :size="20" />
					</span>
				</template>
				Toevoegen
			</NcButton>

			<NcButton v-if="success"
				type="primary"
				@click="navigationStore.setModal(false)">
				Sluiten
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
	NcInputField,
	NcNoteCard,
	NcLoadingIcon,
	NcDateTimePicker,
	NcTextArea,
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

import { PublicationType } from '../../entities/index.js'

export default {
	name: 'AddPublicationTypePropertyModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcCheckboxRadioSwitch,
		NcInputField,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		NcDateTimePicker,
		NcTextArea,
	},
	data() {
		return {
			properties: {
				name: '',
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
			success: false,
			error: false,
			hasUpdated: false,
		}
	},
	computed: {
		publicationTypeProperty() {
			return Object.assign({}, this.properties)
		},
	},
	watch: {
		publicationTypeProperty: {
			handler(newVal, oldVal) {
				if (newVal.type !== oldVal.type) {
					if (newVal.type === 'boolean') this.properties.default = false
					if (newVal.type !== 'boolean' && oldVal.type === 'boolean') this.properties.default = ''
				}
			},
			deep: true,
		},
	},
	methods: {
		addPublicationType() {
			this.loading = true

			const newPublicationTypeItem = new PublicationType({
				...publicationTypeStore.publicationTypeItem,
				properties: { // due to bad (no) support for number fields inside nextcloud/vue, parse the text to a number
					...publicationTypeStore.publicationTypeItem.properties,
					[this.properties.name]: {
						...this.properties,
						minLength: parseFloat(this.properties.minLength) || null,
						maxLength: parseFloat(this.properties.maxLength) || null,
						minimum: parseFloat(this.properties.minimum) || null,
						maximum: parseFloat(this.properties.maximum) || null,
						multipleOf: parseFloat(this.properties.multipleOf) || null,
						minItems: parseFloat(this.properties.minItems) || null,
						maxItems: parseFloat(this.properties.maxItems) || null,
					},
				},
			})

			publicationTypeStore.editPublicationType(newPublicationTypeItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					setTimeout(() => {
						// lets reset
						this.properties = {
							name: '',
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
						}
						navigationStore.setModal(false)
					    this.success = false
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.success = false
					this.error = err
				})
		},
		checkIfTitleIsUnique(name) {
			const keys = Object.keys(publicationTypeStore.publicationTypeItem.properties)
			if (keys.includes(name)) return true
			return false
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

.weightNormal {
    font-weight: normal;
}
</style>
