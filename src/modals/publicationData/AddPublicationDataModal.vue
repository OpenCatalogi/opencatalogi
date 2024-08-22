<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationData'"
		ref="modalRef"
		label-id="addPublicationPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="mapMetadataEigenschappen"
					v-model="eigenschappen.value"
					required />

				<div v-if="!!getSelectedMetadataProperty">
					<!-- TYPE : STRING -->
					<div v-if="getSelectedMetadataProperty.type === 'string'">
						<NcDateTimePicker v-if="getSelectedMetadataProperty.format === 'date'"
							v-model="value"
							type="date"
							label="Waarde"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'time'"
							v-model="value"
							type="time"
							label="Waarde"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'date-time'"
							v-model="value"
							type="datetime"
							label="Waarde"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'email'"
							:value.sync="value"
							label="Email"
							type="email"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'idn-email'"
							:value.sync="value"
							label="IDN-Email"
							type="email"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else-if="getSelectedMetadataProperty.format === 'regex'"
							:value.sync="value"
							label="Waarde (regex)"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'password'"
							:value.sync="value"
							type="password"
							label="Wachtwoord"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'telephone'"
							:value.sync="value"
							type="tel"
							label="Telefoon nummer"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else
							:value.sync="value"
							label="Waarde"
							:disabled="loading"
							:loading="loading" />
					</div>

					<!-- TYPE : NUMBER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'number'"
						:disabled="loading"
						type="number"
						step="any"
						label="Nummer"
						:value.sync="value"
						:loading="loading"
						@input="(elem) => verifyInput(elem.target.value)" />

					<!-- TYPE : INTEGER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'integer'"
						:disabled="loading"
						type="number"
						step="1"
						label="Integer"
						:value.sync="value"
						:loading="loading"
						@input="(elem) => verifyInput(elem.target.value)" />

					<!-- TYPE : OBJECT -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'object'"
						:disabled="loading"
						label="Object"
						:value.sync="value"
						:loading="loading"
						@input="(elem) => verifyInput(elem.target.value)" />

					<!-- TYPE : ARRAY -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'array'"
						:disabled="loading"
						label="Waarde lijst (split op ,)"
						:value.sync="value"
						:loading="loading"
						@input="(elem) => verifyInput(elem.target.value)" />

					<!-- TYPE : BOOLEAN -->
					<NcCheckboxRadioSwitch v-else-if="getSelectedMetadataProperty.type === 'boolean'"
						:disabled="loading"
						:checked.sync="value"
						:loading="loading">
						Waarde
					</NcCheckboxRadioSwitch>
				</div>
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="loading
						|| !eigenschappen.value?.id
						|| ( getSelectedMetadataProperty.type !== 'boolean' && !value )
					"
					type="primary"
					@click="AddPublicatieEigenschap()">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
				</NcButton>

				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<!-- eslint-disable no-console -->
<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcDateTimePicker,
	NcCheckboxRadioSwitch,
	NcInputField,
	NcNoteCard,
	NcLoadingIcon,
	NcSelect,
} from '@nextcloud/vue'
import { z } from 'zod'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcDateTimePicker,
		NcCheckboxRadioSwitch,
		NcInputField,
		NcSelect,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			eigenschappen: {},
			metaData: {},
			value: '',
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		// I write documentation to help me understand what I need to do.

		/**
		 * Takes the properties from the metadata in the store and loops through them, returning only the items not in the publication data
		 * @return {Array<object> | []} list of metadata properties NOT in the publication data
		 */
		getFilteredMetadataProperties() {
			if (!publicationStore.publicationMetaData?.properties) return []
			return Object.values(publicationStore.publicationMetaData?.properties)
				.filter((prop) => !Object.keys(publicationStore.publicationItem?.data).includes(prop.title))
		},
		/**
		 * based on the result `getFilteredMetadataProperties` gives AND the selected value in the eigenschappen dropdown,
		 * it will return the full metadata property of the selected property, containing the rules for the data.
		 *
		 * It will return `null` if no property is selected
		 * @see getFilteredMetadataProperties
		 * @return {object | null} A single metadata properties object or null
		 */
		getSelectedMetadataProperty() {
			return this.getFilteredMetadataProperties.filter((prop) => prop?.title === this.eigenschappen.value?.label)[0] || null
		},
		mapMetadataEigenschappen() {
			if (publicationStore.publicationMetaData) {
				const incomingUrl = new URL(publicationStore.publicationMetaData.source)
				if (incomingUrl?.host !== window.location.host) {
					return {
						inputLabel: 'Publicatie type eigenschap',
						options: Object.keys(publicationStore.publicationMetaData?.properties)
							.filter((prop) => !Object.keys(publicationStore.publicationItem?.data).includes(prop))
							.map((prop) => ({
								id: prop,
								label: prop,
							})),
					}
				}
			}

			return {
				inputLabel: 'Publicatie type eigenschap',
				options: this.getFilteredMetadataProperties
					.map((prop) => ({
						id: prop.title,
						label: prop.title,
					})),
			}
		},
	},
	watch: {
		getSelectedMetadataProperty(newVal) {
			console.log('new selected metadata property', newVal)
			this.setDefaultValue(newVal)
		},
	},
	methods: {
		/**
		 * Accepts the selected metadata property or nothing, and changes the value property in `data()` to the default value from the property.
		 *
		 * Depending on the property.type, it will put in specialized data, such as `object` or 'boolean'.
		 *
		 * This function only runs when the selected metadata property changes
		 * @param {object} SelectedMetadataProperty The metadata property Object containing the rules
		 * @see getSelectedMetadataProperty
		 */
		setDefaultValue(SelectedMetadataProperty = null) {
			const prop = SelectedMetadataProperty || this.getSelectedMetadataProperty

			switch (prop.type) {
			case 'string': {
				if (prop.format === 'date' || prop.format === 'time' || prop.format === 'date-time') {
					console.log('Set default value to Date ', prop.default)
					this.value = new Date(prop.default || '')
					break
				} else {
					console.log('Set default value to ', prop.default)
					this.value = prop.default
					break
				}
			}

			case 'object': {
				console.log('Set default value to Object ', prop.default)
				this.value = typeof prop.default === 'object'
					? JSON.stringify(prop.default)
					: prop.default
				break
			}

			case 'array': {
				console.log('Set default value to Array ', prop.default)
				this.value = prop.default.join(', ') || ''
				break
			}

			case 'boolean': {
				console.log('Set default value to Boolean ', prop.default)
				const isTrueSet = typeof prop.default === 'boolean'
					? prop.default
					: prop.default?.toLowerCase() === 'true'
				this.value = isTrueSet
				break
			}

			default:
				console.log('Set default value to ', prop.default)
				this.value = prop.default
				break
			}
		},
		/**
		 * Takes the value of a input element and tests it against various rules from `getSelectedMetadataProperty`.
		 * Which then returns a success boolean and a helper text containing the error message when success is false.
		 *
		 * @param {string} value the value to be verified
		 * @see getSelectedMetadataProperty
		 */
		verifyInput(value) {
			let schema = z.any()

			// TODO: add more validations
			// 'duration' format needs to have a max length of 10
			// and more, all based on the format
			if (this.getSelectedMetadataProperty.pattern) {
				schema = schema.regex(this.getSelectedMetadataProperty.pattern, { message: 'Voldoet niet aan het vereiste patroon' })
			}

			const result = schema.safeParse(value)

			return {
				success: result.success,
				helperText: result?.error || false,
			}
		},
		AddPublicatieEigenschap() {
			this.loading = true

			const bodyData = publicationStore.publicationItem
			bodyData.data[this.eigenschappen.value?.label] = this.value
			delete bodyData.publicationDate

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...bodyData,
						catalogi: bodyData.catalogi.id,
						metaData: bodyData.metaData.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok

					// Lets refresh the publicationList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)

					// reset modal form
					this.eigenschappen = {}
					this.data = ''
				})
				.catch((err) => {
					this.loading = false
					this.error = err
				})
		},
		fetchMetaData(metaDataUrl, loading) {

			if (loading) { this.metaDataLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/metadata?source=${metaDataUrl}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.metadata = data.results[0]
					})
					if (loading) { this.metaDataLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.metaDataLoading = false }
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

.flex-horizontal {
    display: flex;
    gap: 4px;
}
</style>
