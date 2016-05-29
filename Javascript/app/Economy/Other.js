import React from 'react'
import BackboneReact from 'backbone-react-component'
import { MasterledgerCollection } from '../models'
import { Link } from 'react-router'
import { BackboneTable } from '../BackboneTable'

var MasterLedgerHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Huvudbok</h2>
				<EconomyAccounts type={MasterledgerCollection} />
			</div>
		);
	},
});

var EconomyAccounts = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 4,
		};
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Konto</th>
				<th className="uk-text-right">Kontobalans</th>
			</tr>
		);
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/economy/account/" + row.account_number}>{row.account_number}</Link></td>
				<td>{row.title}</td>
				<td className="uk-text-right"><Currency value={row.balance} /></td>
			</tr>
		);
	},
});

var EconomyOverviewHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Översikt</h2>
				<ul>
					<li>Samtliga obetalda fakturor + 3 senaste betalda</li>
					<li>5 senaste skapade/ändrade verifikationerna</li>
					<li>Saldo på konton (Bank, Stripe, PayPal, etc)</li>
					<li>Datum för senaste synkroniseringar (Bank, Stripe, PayPal, etc)</li>
				</ul>
			</div>
		);
	},
});

var Currency = React.createClass({
	render: function()
	{
		var formatter = new Intl.NumberFormat('sv-SE', {
			/*
			style: 'currency',
			currency: 'SEK',
			*/
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		});

		var value = formatter.format(this.props.value / 100);
		return (<span>{value} {this.props.currency}</span>);
	},
});

var DateField = React.createClass({
	render: function()
	{
		var str = new Intl.DateTimeFormat('sv-SE').format(Date.parse(this.props.date));
		return (<span>{str}</span>);
	},
});

var Pagination = React.createClass({
	getInitialState: function()
	{
		return {
		};
	},

	render: function ()
	{
//		console.log("Pages:   " + this.state.pages);
//		console.log("Current: " + this.state.selected);
		return (
			<ul className="uk-pagination">
				<li className=""><a onClick={this.props.pagerPrev}><i className="uk-icon-angle-double-left"></i></a></li>
				<li><a href="">1</a></li>
				<li className="uk-active"><span>2</span></li>
				<li><span>...</span></li>
				<li className=""><a onClick={this.props.pagerNext}><i className="uk-icon-angle-double-right"></i></a></li>
			</ul>
		);
	},
});

var EconomyDebugHandler = React.createClass({
	render: function ()
	{

		return (
			<div className="uk-width-1-1">
				<h2>Debug</h2>
			</div>
		);
	}
});

var EconomyAccountingPeriodHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Räkneskapsår</h2>
				<p>Konfiguera räkneskapsår</p>
			</div>
		);
	},
});

module.exports = {
	EconomyOverviewHandler,
	MasterLedgerHandler,
	Currency,
	DateField,
	EconomyDebugHandler,
	EconomyAccountingPeriodHandler,
}
