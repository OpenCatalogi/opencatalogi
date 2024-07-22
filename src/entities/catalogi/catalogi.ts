interface TCatalogi {
    id: string;
    name: string;
    summary: string;
    _schema: string;
    _id: string;
}

export class Catalogi implements TCatalogi {

	id: string
	name: string
	summary: string
	_schema: string
	_id: string

	constructor(id: string, name: string, summary: string, _schema: string, _id: string) {
		this.id = id
		this.name = name
		this.summary = summary
		this._schema = _schema
		this._id = _id
	}

}
