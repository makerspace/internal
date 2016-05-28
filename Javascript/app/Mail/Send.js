import React from 'react'

var MailSendHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Skicka mail</h2>
				Mottagare: <input type="text" name="subject" /><br />
				Ämne: <input type="text" name="subject" /><br />
				<textarea></textarea><br />
				<button>Skicka</button>
			</div>
		);
	},
});

module.exports = { MailSendHandler }