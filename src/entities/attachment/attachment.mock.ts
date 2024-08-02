/* eslint-disable @typescript-eslint/ban-ts-comment */
// @ts-nocheck -- ERRORS EXPECTED, third data is invalid, this disables any TS checking
import { TAttachment } from './attachment.types'

export const mockAttachmentsData = (): TAttachment[] => [
	{
		id: '12345',
		title: 'Comprehensive Guide to TypeScript',
		summary: 'A detailed guide covering all aspects of TypeScript.',
		description: 'This document covers everything from basic syntax to advanced features of TypeScript, including type annotations, interfaces, and generics.',
		reference: 'TS-Guide-001',
		labels: ['typescript', 'guide', 'programming', 'reference'],
		accessURL: 'https://example.com/ts-guide',
		downloadURL: 'https://example.com/downloads/ts-guide.pdf',
		type: 'document',
		extension: 'pdf',
		size: 2048000, // size in bytes (2MB)
		anonymization: {
			anonymized: true,
			results: 'Names and personal identifiers have been removed.',
		},
		language: {
			code: 'en',
			level: 'advanced',
		},
		versionOf: 'v1.0',
		hash: 'abc123def456ghi789jkl012mno345pqr678stu',
		published: '2023-01-15T08:00:00Z',
		modified: '2023-07-15T08:00:00Z',
		license: 'CC BY-SA 4.0',
	},
	{
		id: '67890',
		title: 'JavaScript ES6 Features',
		summary: 'An overview of new features introduced in ES6.',
		description: 'This document provides an overview of the new features introduced in ECMAScript 2015 (ES6), including let/const, arrow functions, and classes.',
		reference: 'JS-ES6-Overview',
		labels: ['javascript', 'ES6', 'overview'],
		accessURL: '',
		downloadURL: '',
		type: 'document',
		extension: 'pdf',
		size: 0, // size not specified
		anonymization: {
			anonymized: false,
			results: '',
		},
		language: {
			code: 'en',
			level: 'intermediate',
		},
		versionOf: '',
		hash: '',
		published: '',
		modified: '',
		license: '',
	},
	{
		id: 54321, // should be a string
		title: 'Invalid Data Example',
		summary: 12345, // should be a string
		description: true, // should be a string
		reference: null, // should be a string
		labels: 'invalid,label', // should be an array of strings
		accessURL: 'htp://invalid-url', // invalid URL
		downloadURL: 'https://example.com/downloads/invalid', // URL but missing file extension
		type: 'unknownType',
		extension: 123, // should be a string
		size: '2048000', // should be a number
		anonymization: {
			anonymized: 'yes', // should be a boolean
			results: 100, // should be a string
		},
		language: {
			code: 123, // should be a string
			level: {}, // should be a string
		},
		versionOf: 1, // should be a string
		hash: {}, // should be a string
		published: 'invalid-date', // invalid date format
		modified: [], // should be a string
		license: 123, // should be a string
	},
]

export const mockAttachments = (data: TAttachment[] = mockArticlesData()): TAttachment[] => data.map(item => new Article(item))
