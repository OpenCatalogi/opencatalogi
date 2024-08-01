<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'addMetadataDataModal'"
		ref="modalRef"
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
					label="Eigenschap naam"
					required
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

				<NcTextField :disabled="loading"
					label="Defaultwaarde"
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
						label="minimum waarde"
						:value.sync="properties.minimum" />

					<NcInputField :disabled="loading"
						type="number"
						label="maximum waarde"
						:value.sync="properties.maximum" />

					<NcInputField :disabled="loading"
						type="number"
						label="multipleOf"
						:value.sync="properties.multipleOf" />

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="properties.exclusiveMin">
						exclusief minimum
					</NcCheckboxRadioSwitch>

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="properties.exclusiveMax">
						exclusief maximum
					</NcCheckboxRadioSwitch>
				</div>

				<!-- type array only -->
				<div v-if="properties.type === 'array'">
					<h5 class="weightNormal">
						type: array
					</h5>

					<NcInputField :disabled="loading"
						type="number"
						label="minimale items"
						:value.sync="properties.minItems" />

					<NcInputField :disabled="loading"
						type="number"
						label="minimale items"
						:value.sync="properties.maxItems" />
				</div>
			</div>

			<NcButton v-if="!success"
				:disabled="!properties.title || !properties.type || loading"
				type="primary"
				@click="AddMetadata()">
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
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddMetaDataPropertyModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcCheckboxRadioSwitch,
		NcInputField,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
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
			typeOptions: {
				inputLabel: 'Type',
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
	methods: {
		AddMetadata() {
			metadataStore.metaDataItem.properties[this.properties.title] = this.properties

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${metadataStore.metaDataItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(metadataStore.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = true
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					setTimeout(() => {
						// lets reset
						this.properties = {
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
