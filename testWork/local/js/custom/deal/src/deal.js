import {Type} from 'main.core';

export class Deal
{
	constructor(options = {name: 'Deal'})
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
