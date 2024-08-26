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
			<NcNoteCard v-if="getSelectedMetadataProperty?.deprecated || false" type="warning">
				<p>Deze eigenschap staat gemarkeerd als afgeschaft, hij zal bij een komende versie van het onderliggende publicatie type waarschijnlijk komen te vervallen.</p>
			</NcNoteCard>
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
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'time'"
							v-model="value"
							type="time"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcDateTimePicker v-else-if="getSelectedMetadataProperty.format === 'date-time'"
							v-model="value"
							type="datetime"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'email'"
							:value.sync="value"
							label="Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'idn-email'"
							:value.sync="value"
							label="IDN-Email"
							type="email"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else-if="getSelectedMetadataProperty.format === 'regex'"
							:value.sync="value"
							label="Waarde (regex)"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'password'"
							:value.sync="value"
							type="password"
							label="Wachtwoord"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcInputField v-else-if="getSelectedMetadataProperty.format === 'telephone'"
							:value.sync="value"
							type="tel"
							label="Telefoon nummer"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />

						<NcTextField v-else
							:value.sync="value"
							label="Waarde"
							:error="!verifyInput.success"
							:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
							:disabled="loading"
							:loading="loading" />
					</div>

					<!-- TYPE : NUMBER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'number'"
						:value.sync="value"
						type="number"
						step="any"
						label="Nummer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : INTEGER -->
					<NcInputField v-else-if="getSelectedMetadataProperty.type === 'integer'"
						:value.sync="value"
						type="number"
						step="1"
						label="Integer"
						required
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : OBJECT -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'object'"
						:value.sync="value"
						label="Object"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : ARRAY -->
					<NcTextArea v-else-if="getSelectedMetadataProperty.type === 'array'"
						:value.sync="value"
						label="Waarde lijst (splitst op ,)"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading" />

					<!-- TYPE : BOOLEAN -->
					<NcCheckboxRadioSwitch v-else-if="getSelectedMetadataProperty.type === 'boolean'"
						:checked.sync="value"
						:error="!verifyInput.success"
						:helper-text="!verifyInput.success ? verifyInput.helperText : ''"
						:disabled="loading"
						:loading="loading">
						Waarde
					</NcCheckboxRadioSwitch>
				</div>
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="loading
						|| !eigenschappen.value?.id
						|| !verifyInput.success
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
import validator from 'validator'

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
		/**
		 * Takes the value the user types in and tests it against various rules from `getSelectedMetadataProperty`.
		 * Which then returns a success boolean and a helper text containing the error message when success is false.
		 *
		 * @see getSelectedMetadataProperty
		 */
		verifyInput() {
			const selectedProperty = this.getSelectedMetadataProperty
			if (!selectedProperty) return {}

			let schema = z.any()

			// TYPE
			if (selectedProperty.type === 'string') {
				schema = z.string()
			}
			if (selectedProperty.type === 'number') {
				schema = z.number()
			}
			if (selectedProperty.type === 'integer') {
				schema = z.number()
			}
			if (selectedProperty.type === 'object') {
				schema = z.string().refine((val) => {
					try {
						JSON.parse(val)
						return true
					} catch (error) {
						return false
					}
				}, 'Dit is niet een geldige object')
			}
			if (selectedProperty.type === 'array') {
				schema = z.array(z.string())
			}
			if (selectedProperty.type === 'boolean') {
				schema = z.boolean()
			}
			if (selectedProperty.type === 'dictionary') {
				schema = z.string() // its not known what a dictionary is yet, so this is here as a little failsafe
			}

			// FORMAT - you only want format to be used on strings, this may change in the future
			if (selectedProperty.type === 'string') {
				if (selectedProperty.format === 'date') {
					schema = schema.datetime()
				}
				if (selectedProperty.format === 'time') {
					schema = schema.datetime()
				}
				if (selectedProperty.format === 'date-time') {
					schema = schema.datetime()
				}
				if (selectedProperty.format === 'uuid') {
					// schema = schema.uuid({ message: 'Dit is geen geldige UUID' })
					schema = schema.refine(validator.isUUID, { message: 'Dit is geen geldige UUID' })
				}
				if (selectedProperty.format === 'email') {
					// schema = schema.email({ message: 'Dit is geen geldige Email' })
					schema = schema.refine(validator.isEmail, { message: 'Dit is geen geldige Email' })
				}
				if (selectedProperty.format === 'idn-email') {
					// schema = schema.email({ message: 'Dit is geen geldige Email' })
					schema = schema.refine(validator.isEmail, { message: 'Dit is geen geldige IDN-Email' })
				}
				if (selectedProperty.format === 'hostname') {
					schema = schema.regex(
						/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9])\.)+([A-Za-z]|[A-Za-z][A-Za-z0-9-]*[A-Za-z0-9])$/g,
						{ message: 'Dit is geen geldige hostname' },
					)
				}
				if (selectedProperty.format === 'idn-hostname') {
					schema = schema.regex(
						/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9])\.)+([A-Za-z]|[A-Za-z][A-Za-z0-9-]*[A-Za-z0-9])$/g,
						{ message: 'Dit is geen geldige IDN-hostname' },
					)
				}
				if (selectedProperty.format === 'ipv4') {
					// schema = schema.ip({ version: 'v4', message: 'Dit is geen geldige ipv4' })
					schema = schema.refine((val) => validator.isIP(val, 4), { message: 'Dit is geen geldige ipv4' })
				}
				if (selectedProperty.format === 'ipv6') {
					// schema = schema.ip({ version: 'v6', message: 'Dit is geen geldige ipv6' })
					schema = schema.refine((val) => validator.isIP(val, 6), { message: 'Dit is geen geldige ipv6' })
				}
				if (selectedProperty.format === 'url') {
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige URL' },
					)
				}
				if (selectedProperty.format === 'uri') {
					// schema = schema.url({ message: 'Dit is geen geldige URI' })
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige URI' },
					)
				}
				if (selectedProperty.format === 'uri-reference') {
					// schema = schema.url({ message: 'Dit is geen geldige URI-reference' })
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige URI-reference' },
					)
				}
				if (selectedProperty.format === 'iri') {
					// schema = schema.url({ message: 'Dit is geen geldige IRI' })
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige IRI' },
					)
				}
				if (selectedProperty.format === 'iri-reference') {
					// schema = schema.url({ message: 'Dit is geen geldige IRI-reference' })
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige IRI-reference' },
					)
				}
				if (selectedProperty.format === 'uri-template') {
					// schema = schema.url({ message: 'Dit is geen geldige URI-template' })
					schema = schema.refine(
						(val) => validator.isURL(val, { require_protocol: true }),
						{ message: 'Dit is geen geldige URI-template' },
					)
				}
				if (selectedProperty.format === 'json-pointer') {
					schema = schema.refine((val) => {
						try {
							JSON.parse(val)
							return true
						} catch (error) {
							return false
						}
					}, 'Dit is niet een geldige json-pointer')
				}
				if (selectedProperty.format === 'regex') {
					schema = schema.refine((val) => {
						try {
							RegExp(val)
							return true
						} catch (error) {
							return false
						}
					}, 'Dit is niet een geldige regex')
				}
				if (selectedProperty.format === 'binary') {
					schema = schema.regex(/^[0|1]*$/g, 'Dit is geen geldige binair')
				}
				if (selectedProperty.format === 'byte') {
					schema = schema.regex(/^([0|1]{8})*$/g, 'Dit is geen geldige byte')
				}
				if (selectedProperty.format === 'rsin') {
					schema = schema.regex(/^(\d{9})$/g, 'Dit is geen geldige RSIN-nummer')
				}
				if (selectedProperty.format === 'kvk') {
					schema = schema.regex(/^(\d{8})$/g, 'Dit is geen geldige KVK-nummer')
				}
				if (selectedProperty.format === 'bsn') {
					schema = schema.regex(/^(\d{9})$/g, 'Dit is geen geldige BSN-nummer')
				}
				if (selectedProperty.format === 'oidn') {
					schema = schema.regex(/^\d{8,12}$/g, 'Dit is geen geldige OIDN-nummer')
				}
				if (selectedProperty.format === 'telephone') {
					schema = schema.refine(validator.isMobilePhone, { message: 'Dit is geen geldige telephone-nummer' })
				}
			}

			// GENERIC RULES
			if (selectedProperty.pattern) {
				// check is the regex given is valid to avoid any issues
				let isValidRegex = false
				try {
					RegExp(selectedProperty.pattern)
					isValidRegex = true
				} catch (err) {
					isValidRegex = false
				}

				if (isValidRegex) {
					const regexPattern = new RegExp(selectedProperty.pattern)

					schema = schema.refine((val) => {
						if (Array.isArray(val)) {
							// Validate each string in the array
							return val.every((item) => regexPattern.test(item))
						} else {
							// Validate single string
							return regexPattern.test(val)
						}
					}, { message: 'Voldoet niet aan het vereiste patroon' })
				}
			}
			// number / integer
			if (selectedProperty.type === 'number' || selectedProperty.type === 'integer') {
				// exclusiveMin / exclusiveMax are a boolean, which you can add to a number to add 1 (e.g: 1 + true = 2),
				// this is a stupid simple way to implement what the stoplight is expecting
				// https://conduction.stoplight.io/docs/open-catalogi/5og7tj13bkzj5-create-metadata
				if (selectedProperty.minimum) {
					const minimum = selectedProperty.minimum
					schema = schema.min(minimum + selectedProperty.exclusiveMin, { message: `Minimaal ${minimum + selectedProperty.exclusiveMin}` })
				}
				if (selectedProperty.maximum) {
					const maximum = selectedProperty.maximum
					schema = schema.max(maximum - selectedProperty.exclusiveMax, { message: `Maximaal ${maximum - selectedProperty.exclusiveMax}` })
				}
				if (selectedProperty.multipleOf) {
					const multipleOf = selectedProperty.multipleOf
					schema = schema.refine((val) => val % multipleOf === 0, `${this.value} is niet een veelvoud van ${multipleOf}`)
				}
			} else if (selectedProperty.type === 'array') { // TYPE : ARRAY
				if (selectedProperty.minItems) {
					const minItems = selectedProperty.minItems
					schema = schema.min(minItems, { message: `Minimale hoeveelheid: ${minItems}` })
				}
				if (selectedProperty.maxItems) {
					const maxItems = selectedProperty.maxItems
					schema = schema.max(maxItems, { message: `Maximale hoeveelheid: ${maxItems}` })
				}
			} else { // Anything else
				if (selectedProperty.minLength) {
					const minLength = selectedProperty.minLength
					schema = schema.min(minLength, { message: `Minimale lengte: ${minLength}` })
				}
				if (selectedProperty.maxLength) {
					const maxLength = selectedProperty.maxLength
					schema = schema.max(maxLength, { message: `Maximale lengte: ${maxLength}` })
				}
			}

			// REQUIRED CHECK
			if (selectedProperty.required) {
				if (selectedProperty.type === 'array') {
					schema = schema.and(
						// explanation:
						// if ANY item in the array is not an empty string, it passes
						z.custom((val) => val.some((item) => item.trim() !== ''), { message: 'Deze veld is verplicht' }),
					)
				} else if (selectedProperty.type === 'number') {
					// finite says that ANY number between infinite and -infinite is allowed
					// But NaN is not
					schema = schema.finite({ message: 'Deze veld is verplicht' })
				} else if (selectedProperty.type === 'integer') {
					schema = schema.finite({ message: 'Deze veld is verplicht' })
				} else {
					schema = schema.min // .min() does not exist after .refine(), this has been reported at https://github.com/colinhacks/zod/issues/3725
						? schema.min(1, { message: 'Deze veld is verplicht' })
						: schema.refine(val => val.length >= 1, { message: 'Deze veld is verplicht' })
				}
			}
			if (!selectedProperty.required) {
				// As the value can NEVER be omitted in this code, which is what `.optional()` does
				// I add a `or()` method with a literal empty array / string to act as optional values

				// this array check gives me nightmares, I think it works but please don't touch it
				if (selectedProperty.type === 'array') {
					schema = schema.or( // an empty array is always parsed as ['']
						z.custom((val) => val.length === 1 && val[0].trim() === ''),
					)
				} else if (selectedProperty.type === 'number') {
					schema = schema.or(z.nan())
				} else if (selectedProperty.type === 'integer') {
					schema = schema.or(z.nan())
				} else {
					schema = schema.or(z.literal(''))
				}
			}

			// RUN TESTS
			let result
			switch (selectedProperty.type) {
			case 'string':{
				if (['date', 'time', 'date-time'].includes(selectedProperty.format)) {
					result = schema.safeParse(this.value.toISOString())
				} else result = schema.safeParse(this.value)
				break
			}
			case 'array':{
				result = schema.safeParse(this.value.split(/ *, */g)) // split on , to make an array
				break
			}
			case 'number':
			case 'integer':{
				result = schema.safeParse(parseFloat(this.value))
				break
			}
			default: {
				result = schema.safeParse(this.value)
			}
			}

			return {
				success: result.success,
				helperText: result?.error?.[0]?.message || result?.error?.issues?.[0].message || false,
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
			if (!prop) return

			switch (prop.type) {
			case 'string': {
				if (prop.format === 'date' || prop.format === 'time' || prop.format === 'date-time') {
					const isValidDate = !isNaN(new Date(prop.default))

					console.log('Set default value to Date ', isValidDate ? prop.default : '')
					this.value = new Date(isValidDate ? prop.default : new Date())
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
				this.value = Array.isArray(prop.default) ? (prop.default.join(', ') || '') : prop.default
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

			case 'number':
			case 'integer': {
				console.log('Set default value to Number ', prop.default)
				this.value = prop.default || 0
				break
			}

			default:
				console.log('Set default value to ', prop.default)
				this.value = prop.default
				break
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
