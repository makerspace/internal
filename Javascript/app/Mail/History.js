import React from 'react'

var MailHistoryHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Historik</h2>
				<p>Visa lista över samtliga skickade mail</p>
			</div>
		);
	},
});

module.exports = { MailHistoryHandler }