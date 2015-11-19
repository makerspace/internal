import React from 'react'
import BackboneReact from 'backbone-react-component'
import { InvoiceModel, InvoiceCollection } from '../models'
import { Link } from 'react-router'
import { Currency } from './Other'
import { BackboneTable } from '../BackboneTable'

var InvoiceListHandler = React.createClass({
	getInitialState: function()
	{
		var invoices = new InvoiceCollection();
		invoices.fetch();

		return {
			collection: invoices,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Fakturor</h2>
				<p>På denna sida ser du en lista på samtliga fakturor för det valda bokföringsåret.</p>
				<InvoiceList collection={this.state.collection} />
				<Link to={"/economy/invoice/add"}><i className="uk-icon-plus-circle"></i> Skapa faktura</Link>
			</div>
		);
	},
});

var InvoiceHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;
		var invoice = new InvoiceModel({id: id});
		invoice.fetch();

		return {
			model: invoice
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Faktura</h2>
				<Invoice model={this.state.model} />
			</div>
		);
	},
});

var InvoiceAddHandler = React.createClass({
	getInitialState: function()
	{
		var invoice = new InvoiceModel();

		return {
			model: invoice
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Skapa faktura</h2>
				<Invoice model={this.state.model} />
			</div>
		);
	},
});

var InvoiceList = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 7,
		};
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Förfallodatum</th>
				<th>Mottagare</th>
				<th>Er referens</th>
				<th>Vår referens</th>
				<th className="uk-text-right">Belopp</th>
				<th>Status</th>
			</tr>
		);
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/economy/invoice/" + row.invoice_number}>{row.invoice_number}</Link></td>
				<td>{row.date_expiry}</td>
				<td>{row.recipient}</td>
				<td>{row.our_reference}</td>
				<td>{row.your_reference}</td>
				<td className="uk-text-right"><Currency value={row.priceGross} /></td>
				<td>{row.status}</td>
			</tr>
		);
	},
});

var Invoice = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	render: function()
	{
		return (
			<div>
				<dl>
					<dt>#</dt>
					<dd>{this.state.model.invoice_number}</dd>

					<dt>Förfallodatum</dt>
					<dd>{this.state.model.date_expiry}</dd>

					<dt>Mottagare</dt>
					<dd>{this.state.model.recipient}</dd>

					<dt>Er referens</dt>
					<dd>{this.state.model.your_reference}</dd>

					<dt>Vår referens</dt>
					<dd>{this.state.model.our_reference}</dd>

					<dt>Belopp</dt>
					<dd>{this.state.model.amount}</dd>

					<dt>Status</dt>
					<dd>{this.state.model.status}</dd>
				</dl>
			</div>
		);
	},
});

module.exports = {
	InvoiceListHandler,
	InvoiceHandler,
	InvoiceAddHandler,
}