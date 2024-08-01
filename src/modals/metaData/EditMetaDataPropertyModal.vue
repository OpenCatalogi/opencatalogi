<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'editMetadataDataModal'"
		ref="modalRef"
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

			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					required
					:value.sync="metadata.properties[metadataStore.metadataDataKey].title" />

				<NcTextField :disabled="loading"
					label="description"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].description" />

				<NcSelect v-bind="typeOptions"
					v-model="metadata.properties[metadataStore.metadataDataKey].type"
					required />

				<NcSelect v-bind="formatOptions"
					v-model="metadata.properties[metadataStore.metadataDataKey].format" />

				<NcTextField :disabled="loading"
					label="patroon (regex)"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].pattern" />

				<NcTextField :disabled="loading"
					label="default"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].default" />

				<NcTextField :disabled="loading"
					label="behavior"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].behavior" />

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].required">
					Required
				</NcCheckboxRadioSwitch>

				<NcCheckboxRadioSwitch
					:disabled="loading"
					:checked.sync="metadata.properties[metadataStore.metadataDataKey].deprecated">
					Deprecated
				</NcCheckboxRadioSwitch>

				<NcTextField :disabled="loading"
					label="minimum lengte"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].minLength" />

				<NcTextField :disabled="loading"
					label="maximum lengte"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].maxLength" />

				<NcTextField :disabled="loading"
					label="exemplaar"
					:value.sync="metadata.properties[metadataStore.metadataDataKey].example" />

				<!-- type integer and number only -->
				<div v-if="metadata.properties[metadataStore.metadataDataKey].type === 'integer' || metadata.properties[metadataStore.metadataDataKey].type === 'number'">
					<h5 class="weightNormal">
						type: nummer
					</h5>

					<NcTextField :disabled="loading"
						label="minimum waarde"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].minimum" />

					<NcTextField :disabled="loading"
						label="maximum waarde"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].maximum" />

					<NcTextField :disabled="loading"
						label="multipleOf"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].multipleOf" />

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="metadata.properties[metadataStore.metadataDataKey].exclusiveMin">
						exclusief minimum
					</NcCheckboxRadioSwitch>

					<NcCheckboxRadioSwitch
						:disabled="loading"
						:checked.sync="metadata.properties[metadataStore.metadataDataKey].exclusiveMax">
						exclusief maximum
					</NcCheckboxRadioSwitch>
				</div>

				<!-- type array only -->
				<div v-if="metadata.properties[metadataStore.metadataDataKey].type === 'array'">
					<h5 class="weightNormal">
						type: array
					</h5>

					<NcTextField :disabled="loading"
						label="minimale items"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].minItems" />

					<NcTextField :disabled="loading"
						label="minimale items"
						:value.sync="metadata.properties[metadataStore.metadataDataKey].maxItems" />
				</div>
			</div>

			<NcButton v-if="success === null"
				:disabled="loading"
				type="primary"
				@click="updateMetadata(metadata.id)">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Plus v-if="!loading" :size="20" />
					</span>
				</template>
				Toevoegen
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
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

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
			success: null,
			successMessage: '',
			hasUpdated: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'editMetadataDataModal' && this.hasUpdated) {
			if (this.dataKey !== metadataStore.metadataDataKey) this.hasUpdated = false
		}
		if (navigationStore.modal === 'editMetadataDataModal' && !this.hasUpdated) {
			this.metadata = metadataStore.metaDataItem
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
						metadataStore.metaDataItem = data

						this.metadata = {
							...metadataStore.metaDataItem,
							properties: JSON.parse(metadataStore.metaDataItem.properties),
						}
						this.metadata.properties[metadataStore.metadataDataKey] = metadataStore.getMetadataPropertyKeys(metadataStore.metadataDataKey)
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
								pattern: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].pattern) || 0,
								minLength: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minLength) || 0,
								maxLength: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maxLength) || 0,
								minimum: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minimum) || 0,
								maximum: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maximum) || 0,
								multipleOf: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].multipleOf) || 0,
								minItems: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].minItems) || 0,
								maxItems: parseFloat(this.metadata.properties[metadataStore.metadataDataKey].maxItems) || 0,
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
