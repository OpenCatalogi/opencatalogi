/* eslint-disable no-console */
import { Catalogi } from './catalogi'
import { TCatalogi } from './catalogi.types'

describe('Catalogi Entity', () => {
	it('create Catalogi entity with full data, image = base64', () => {
		const catalogi = new Catalogi(testData[0])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(testData[0])

		expect(catalogi.validate()).toBe(true)
	})

	it('create Catalogi entity with full data, image = url', () => {
		const catalogi = new Catalogi(testData[1])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(testData[1])

		expect(catalogi.validate()).toBe(true)
	})

	it('create Catalogi entity with partial data', () => {
		const catalogi = new Catalogi(testData[2])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi.id).toBe(testData[2].id)
		expect(catalogi.title).toBe(testData[2].title)
		expect(catalogi.summary).toBe(testData[2].summary)
		expect(catalogi.description).toBe(testData[2].description)
		expect(catalogi.image).toBe('')
		expect(catalogi.search).toBe('')

		expect(catalogi.validate()).toBe(true)
	})

	it('create Catalogi entity with falsy data', () => {
		const catalogi = new Catalogi(testData[3])

		expect(catalogi).toBeInstanceOf(Catalogi)
		expect(catalogi).toEqual(testData[3])

		expect(catalogi.validate()).toBe(false)
	})
})

const testData: TCatalogi[] = [
	{ // full data - base64
		id: '1',
		title: 'Exquisite Collections of Timeless Elegance',
		summary: 'Explore an array of elegant and timeless pieces designed to enhance any space with sophistication and style.',
		description: 'a really really long description about this catalogus',
		image: 'data:image/jpeg;base64,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',
		search: 'string',
	},
	{ // full data - image url
		id: '1',
		title: 'Exquisite Collections of Timeless Elegance',
		summary: 'Explore an array of elegant and timeless pieces designed to enhance any space with sophistication and style.',
		description: 'a really really long description about this catalogus',
		image: 'https://images.pexels.com/photos/17266857/pexels-photo-17266857/free-photo-of-vase-with-flowers-and-cup-of-coffee.jpeg',
		search: 'string',
	},
	{ // partial data
		id: '2',
		title: 'Comprehensive Guide to Luxury Home Furnishings',
		summary: 'Discover a comprehensive collection of luxury home furnishings that combine functionality with exquisite design.',
		description: 'a really really long description about this catalogus',
	},
	{ // invalid data
		id: '3',
		title: '',
		summary: 'Unveil the ultimate assortment of artistic masterpieces, perfect for adding a touch of creativity and beauty to your surroundings.',
		description: 'a really really long description about this catalogus',
		image: 'https://images.pexels.com/photos/17266857/pexels-photo-17266857/free-photo-of-vase-with-flowers-and-cup-of-coffee.jpeg',
		search: 'string',
	},
]
