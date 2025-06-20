/* eslint-disable @typescript-eslint/no-explicit-any */
/**
 * Type definition for a Page object
 * Represents the structure of a page with content and metadata
 */
export type TPage = {
	id: string
	uuid: string
	title: string
	contents: { type: string; id: string; data: Record<string, any> }[]
	slug: string
	createdAt: string
	updatedAt: string
}
