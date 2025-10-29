import {Type} from 'main.core';

export class MrmDeal
{
	constructor(options = {name: 'MrmDeal'})
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
