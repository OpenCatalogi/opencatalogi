/**
 * Accepts the selected publication type property and value, and changes the value to the default value from the property.
 *
 * Depending on the property.type, it will put in specialized data, such as `object` or `boolean`.
 * @param {object} SelectedPublicationTypeProperty The publication type property Object containing the rules
 * @param {any} value the value to check if it should be used, default to SelectedPublicationTypeProperty.default
 * @see getActivePublicationTypeProperty
 */
export const setDefaultValue = (SelectedPublicationTypeProperty, value = null) => {
	const prop = SelectedPublicationTypeProperty
	if (!prop) return value
	!value && (value = prop.default)

	let returnValue

	switch (prop.type) {
	case 'string': {
		if (prop.format === 'date' || prop.format === 'time' || prop.format === 'date-time') {
			const isValidDate = !isNaN(new Date(value))

			returnValue = new Date(isValidDate ? value : new Date())
			break
		}
		returnValue = value
		break
	}

	case 'object': {
		returnValue = typeof value === 'object'
			? JSON.stringify(value)
			: value
		break
	}

	case 'array': {
		returnValue = Array.isArray(value) ? (value.join(', ') || '') : value
		break
	}

	case 'boolean': {
		const isTrueSet = typeof value === 'boolean'
			? value
			: value?.toLowerCase() === 'true'
		returnValue = isTrueSet
		break
	}

	case 'number':
	case 'integer': {
		returnValue = value || 0
		break
	}

	default:
		returnValue = value || ''
	}

	return returnValue
}
