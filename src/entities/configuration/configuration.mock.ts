import { Configuration } from './configuration'
import { TConfiguration } from './configuration.types'

export const mockConfigurationData = (): TConfiguration[] => [
	{ // full data
		useElastic: true,
		useMongo: true,
	},
	// @ts-expect-error -- useMongo doesn't exist
	{ // partial data
		useElastic: true,
	},
	{ // invalid data
		// @ts-expect-error -- useElastic is supposed to be a boolean
		useElastic: 'string',
		useMongo: false,
	},
]

export const mockConfiguration = (data: TConfiguration[] = mockConfigurationData()): TConfiguration[] => data.map(item => new Configuration(item))
