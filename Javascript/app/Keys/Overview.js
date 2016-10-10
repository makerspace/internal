import React from 'react'
import {
	RfidCollection
} from '../models'
import { Keys } from './Keys'

var KeysOverviewHandler = React.createClass({
	edit: function(entity)
	{
		console.log("Parent edit");
		console.log(entity);
	},

	render: function()
	{
		return (
			<div>
				<h2>Nycklar</h2>
				<p>Visa lista över samtliga nycklas i systemet</p>
				<Keys type={RfidCollection} edit={this.edit} />
			</div>
		);
	},
});
KeysOverviewHandler.title = "Nycklar";

module.exports = KeysOverviewHandler