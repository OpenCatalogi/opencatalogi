export class Listing {

	constructor(
		id,
		title,
		summary = '',
		description = '',
		search = '',
		directory = '',
		metadata = '',
		status = '',
		lastSync = '',
		_default = '', // 'default' is already taken by JS itself
		available = '',
		_schema = '',
		_id = '',
	) {
		this.id = id
		this.title = title
		this.summary = summary
		this.description = description
		this.search = search
		this.directory = directory
		this.metadata = metadata
		this.status = status
		this.lastSync = lastSync
		this.default = _default
		this.available = available
		this._schema = _schema
		this._id = _id
	}

}
