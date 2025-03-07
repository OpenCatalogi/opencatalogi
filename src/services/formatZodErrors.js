export function createZodErrorHandler(result) {
	const issues = result?.error?.issues || []

	const normalizePath = (path) => {
		if (typeof path === 'string') {
			return path.split('.')
		}
		return path
	}

	/**
	 * Get a single error message for a specific path
	 *
	 * @example
	 * ```js
	 * const error = getError('items.0.name')
	 * console.log(error) // "naam is verplicht"
	 * ```
	 *
	 * @param {string} path - The path to get the error message for
	 * @return {string | undefined} The error message or undefined if no error is found
	 */
	const getError = (path) => {
		const normalizedPath = normalizePath(path)
		const error = issues.find((issue) => issue.path.join('.') === normalizedPath.join('.'))
		return error?.message
	}

	/**
	 * Get all error messages for a specific path
	 *
	 * @example
	 * ```js
	 * const errors = getErrors('items.0')
	 * console.log(errors) // ["naam is verplicht", "summary is verplicht", "link is verplicht"]
	 * ```
	 *
	 * @param {string} path - The path to get the error message for
	 * @return {string[]} The error messages or undefined if no error is found
	 */
	const getErrors = (path) => {
		const normalizedPath = normalizePath(path)
		const errors = issues.filter((issue) => issue.path.join('.') === normalizedPath.join('.'))
		return errors.map((error) => error.message)
	}

	return {
		// methods
		getError,
		getErrors,

		// properties
		success: result.success,

		/**
		 * A simple list of flattened errors.
		 *
		 * @example
		 * ```json
		 * [
		 *  "items.0.name: naam is verplicht",
		 *  "items.0.summary: summary is verplicht",
		 *  "items.0.link: link is verplicht"
		 * ]
		 * ```
		 *
		 * @type {string[]}
		 */
		flatErrorMessages: result?.error ? getFlatErrorMessages(result.error.issues) : [],

		/**
		 * A grouped list of errors by path.
		 *
		 * @example
		 * ```json
		 * {
		 *  "items.0.name": ["naam is verplicht"],
		 *  "items.0.summary": ["summary is verplicht"],
		 *  "items.0.link": ["link is verplicht"]
		 * }
		 * ```
		 *
		 * @type {object}
		 */
		groupedErrorsByPath: result?.error ? getGroupedErrorsByPath(result.error.issues) : {},

		/**
		 * A nested list of errors by path.
		 *
		 * @example
		 * ```json
		 * {
		 *  items: {
		 *    0: {
		 *      name: ["naam is verplicht"],
		 *      summary: ["summary is verplicht"],
		 *      link: ["link is verplicht"]
		 *    }
		 *  }
		 * }
		 * ```
		 *
		 * @type {object}
		 */
		nestedFieldErrors: result?.error ? getNestedFieldErrors(result.error.issues) : {},

		/**
		 * A list of errors with path, message, code and type.
		 *
		 * @example
		 * ```json
		 * [
		 *  {
		 *    path: "items.0.name",
		 *    message: "naam is verplicht",
		 *    code: "too_small",
		 *    type: "string"
		 *  },
		 *  {
		 *    path: "items.0.summary",
		 *    message: "summary is verplicht",
		 *    code: "too_small",
		 *    type: "string"
		 *  }
		 * ]
		 * ```
		 *
		 * @type {object}
		 */
		fieldSpecificErrors: result?.error ? getFieldSpecificErrors(result.error.issues) : [],

		/**
		 * A summary of the errors.
		 *
		 * @example
		 * ```json
		 * {
		 *  totalErrors: 3,
		 *  errorsByField: {
		 *    "items.0.name": 1,
		 *    "items.0.summary": 1,
		 *    "items.0.link": 1
		 *  },
		 *  errorsByType: {
		 *    "too_small": 3
		 *  }
		 * }
		 * ```
		 *
		 * @type {object}
		 */
		errorSummary: result?.error ? getErrorSummary(result.error.issues) : {},
	}
}

// BASE FUNCTIONS
const joinPath = (path) => path.join('.')

// ERROR MAPPERS

// Function to convert the issues array into error messages
const getFlatErrorMessages = (issues) => {
	return issues.map((issue) => `${joinPath(issue.path)}: ${issue.message}`) || []
}

// Function to get a grouped errors by path
const getGroupedErrorsByPath = (issues) => {
	const groupedErrors = {}

	issues.forEach((issue) => {
		const path = joinPath(issue.path)
		groupedErrors[path] = groupedErrors[path] || []
		groupedErrors[path].push(issue.message)
	})

	return groupedErrors
}

// Function to convert the issues array into field errors
const getNestedFieldErrors = (issues) => {
	const fieldErrors = {}

	issues.forEach((issue) => {
		let currentLevel = fieldErrors
		const path = issue.path

		// Traverse the path to build the nested structure
		for (let i = 0; i < path.length; i++) {
			const key = path[i]

			if (i === path.length - 1) {
				// If it's the last element in the path, set the error message
				currentLevel[key] = currentLevel[key] || []
				currentLevel[key].push(issue.message)
			} else {
				// Otherwise, continue traversing the nested structure
				currentLevel[key] = currentLevel[key] || {}
				currentLevel = currentLevel[key]
			}
		}
	})

	return fieldErrors
}

// Function to get a list of errors with path, message, code and type
const getFieldSpecificErrors = (issues) => {
	return issues.map((issue) => ({
		path: joinPath(issue.path),
		message: issue.message,
		code: issue.code,
		type: issue.type,
	}))
}

// Function to get a summary of the errors as a number
const getErrorSummary = (issues) => {
	const errorsByField = {}
	const errorsByType = {}

	issues.forEach((issue) => {
		const path = joinPath(issue.path)
		errorsByField[path] = (errorsByField[path] || 0) + 1
		errorsByType[issue.code] = (errorsByType[issue.code] || 0) + 1
	})

	return {
		totalErrors: issues.length,
		errorsByField,
		errorsByType,
	}
}
