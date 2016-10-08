import React from 'react'
import { MailHistory } from '../Mail/History'
import {
	MailCollection
} from '../models'
import { Link } from 'react-router'

var MailUserBox = React.createClass(
{
	render: function()
	{
		return (
			<div>
				<MailHistory type={MailCollection} member_number={this.props.member_number} />
				<Link to="/mail/send" className="uk-button"><i className="uk-icon-envelope" /> Skicka meddelande</Link>
			</div>
		);
	},
});

module.exports = {
	MailUserBox,
}