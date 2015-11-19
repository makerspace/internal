import React from 'react'

var MailHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Mail</h2>
				<ul>
					<li>Påminnelser för betalningar</li>
					<li>Nyhetsbrev</li>
					<li>Skicka mail till medlem</li>
				</ul>
			</div>
			);
	},
});

module.exports = {MailHandler}