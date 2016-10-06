import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	SubscriptionModel,
	SubscriptionCollection
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Common'

var SalesSubscriptionsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Prenumerationer</h2>
				<p>På denna sida ser du en lista på samtliga prenumerationer.</p>
				<Subscriptions type={SubscriptionCollection} />
			</div>
		);
	},
});
SalesSubscriptionsHandler.title = "Visa prenumerationer";

var Subscriptions = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 5,
		};
	},

	renderRow: function (row, i)
	{
		return (
			<tr key={i}>
				<td>{row.member_id}</td>
				<td>{row.date_start}</td>
				<td>{row.title}</td>
				<td>{row.product_id}</td>
			</tr>
		);
	},

	renderHeader: function ()
	{
		return (
			<tr>
				<th>Member</th>
				<th>Startdatum</th>
				<th>Beskrivning</th>
				<th>Skapad</th>
				<th>Produkt</th>
			</tr>
		);
	}
});

module.exports = {
	SalesSubscriptionsHandler
}