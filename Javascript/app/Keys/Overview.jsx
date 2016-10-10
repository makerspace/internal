import React from 'react'

// Backbone
import RfidCollection from '../Backbone/Collections/Rfid'

import Keys from './Keys'

var KeysOverviewHandler = React.createClass({
	edit: function(entity)
	{
		UIkit.modal.alert("TODO: Parent edit" + entity);
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