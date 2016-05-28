import React from 'react'

var MailListsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Listor</h2>
				<p>Visa lista över samtliga mailinglistor</p>
			</div>
		);
	},
});

module.exports = { MailListsHandler }