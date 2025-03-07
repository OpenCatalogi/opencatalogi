import { z } from 'zod'
import validator from 'validator'

/**
 * Takes the value the user types in and tests it against various rules from `selectedPublicationTypeProperty`.
 * Which then returns a success boolean and a helper text containing the error message when success is false.
 *
 * @param {object} selectedPublicationTypeProperty - the rules to verify the input with
 * @param {string} value - the value to test
 */
export const verifyInput = (selectedPublicationTypeProperty, value) => {
	const selectedProperty = selectedPublicationTypeProperty
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
		// https://conduction.stoplight.io/docs/open-catalogi/5og7tj13bkzj5-create-publication-type
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
			schema = schema.refine((val) => val % multipleOf === 0, `${value} is niet een veelvoud van ${multipleOf}`)
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
			if (value instanceof Date) result = schema.safeParse(value.toISOString())
			else result = schema.safeParse(value)
		} else {
			result = schema.safeParse(value)
		}
		break
	}
	case 'array':{
		result = schema.safeParse(value.split(/ *, */g)) // split on , to make an array
		break
	}
	case 'number':
	case 'integer':{
		result = schema.safeParse(parseFloat(value))
		break
	}
	default: {
		result = schema.safeParse(value)
	}
	}

	return {
		success: result.success,
		helperText: result?.error?.[0]?.message || result?.error?.issues?.[0].message || false,
	}
}
