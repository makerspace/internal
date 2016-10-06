import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	SalesHistoryModel,
	SalesHistoryCollection
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Common'

var SalesHistoryHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Försäljningshistorik</h2>
				<p>På denna sida ser du en lista på samtliga sålda produkter.</p>
				<History type={SalesHistoryCollection} />
			</div>
		);
	},
});
SalesHistoryHandler.title = "Visa försäljning";

var History = React.createClass({
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
	SalesHistoryHandler
}