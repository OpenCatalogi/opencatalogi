/**
 * Type definitions for Catalogi entity
 * @module Entities
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */

export type TCatalogi = {
    id: string
    title: string
    summary: string
    description: string
    image: string
    listed: boolean
    organization: string // it is supposed to be TOrganization according to the stoplight, but reality is a bit different
    registers: string[]
    schemas: string[]
    filters: Record<string, unknown>
}
