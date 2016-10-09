import React from 'react'
import {
	MailModel,
	MailCollection
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Common'

var MailHistoryHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Historik</h2>
				<p>Visa lista över samtliga E-post och SMS-utskick</p>
				<Link className="uk-button" to="/mail/send"><i className="uk-icon-plus"></i> Skapa utskick</Link>
				<MailHistory type={MailCollection} />
			</div>
		);
	},
});
MailHistoryHandler.title = "Utskickshistorik";

var MailHistory = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 6,
		};
	},

	componentWillMount: function()
	{
		if(this.props.member_number !== undefined)
		{
			// Load RFID keys related to member
			this.state.collection.fetch({
				data: {
					relation: {
						type: "member",
						member_number: this.props.member_number,
					}
				}
			});
		}
		else
		{
			// Load all RFID keys
			this.state.collection.fetch();
		}
	},
	
	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/mail/" + row.entity_id}>{row.entity_id}</Link></td>
				<td>
					{(() => {
						switch (row.status) {
							case "queued": return <span>Köad <DateField date={row.created_at} /></span>;
							case "failed": return "Sändning misslyckades";
							case "sent":   return <span>Skickad <DateField date={row.date_sent} /></span>;
							default:       return "Okänt";
						}
					})()}
				</td>
				<td>{ row.type == "email" ? <i className="uk-icon-envelope" title="E-mail"></i> : <i className="uk-icon-commenting" title="SMS"></i> } {row.recipient}</td>
				<td>{ row.type == "email" ? row.title : row.description }</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Id</th>
				<th>Status</th>
				<th>Mottagare</th>
				<th>Meddelande</th>
			</tr>
		);
	},
});

module.exports = {
	MailHistoryHandler,
	MailHistory
}