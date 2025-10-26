import {Type} from 'main.core';

export class CrmDeal
{
	constructor(options = {name: 'CrmDeal'})
	{
		this.name = options.name;
	}

	setName(name)
	{
		if (Type.isString(name))
		{
			this.name = name;
		}
	}

	getName()
	{
		return this.name;
	}
}
