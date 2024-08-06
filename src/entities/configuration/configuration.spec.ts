/* eslint-disable no-console */
import { Configuration } from './configuration'
import { TConfiguration } from './configuration.types'

describe('Configuration Store', () => {
	it('create Configuration entity with full data', () => {
		const configuration = new Configuration(testData[0])

		expect(configuration).toBeInstanceOf(Configuration)
		expect(configuration).toEqual(testData[0])

		expect(configuration.validate()).toBe(true)
	})

	it('create Configuration entity with partial data', () => {
		const configuration = new Configuration(testData[1])

		expect(configuration).toBeInstanceOf(Configuration)
		expect(configuration.useElastic).toBe(testData[1].useElastic)

		expect(configuration.validate()).toBe(true)
	})

	it('create Configuration entity with falsy data', () => {
		const configuration = new Configuration(testData[2])

		expect(configuration).toBeInstanceOf(Configuration)
		expect(configuration).toEqual(testData[2])

		expect(configuration.validate()).toBe(false)
	})
})

const testData: TConfiguration[] = [
	{ // full data
		useElastic: true,
		useMongo: true,
	},
	{ // partial data
		useElastic: true,
	},
	{ // invalid data
		useElastic: false,
	},
]
