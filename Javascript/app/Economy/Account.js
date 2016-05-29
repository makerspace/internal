import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	AccountModel,
	AccountCollection,
	TransactionCollection
} from '../models'
import { Link } from 'react-router'
import { Currency, DateField } from './Other'
import {
	BackboneTable,
} from '../BackboneTable'

import { EconomyAccountingInstructionList } from './Instruction'

var EconomyAccountsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Konton</h2>
				<EconomyAccounts type={AccountCollection} />
				<Link to={"/economy/account/add"}><i className="uk-icon-plus-circle"></i> Skapa konto</Link>
			</div>
		);
	},
});

var EconomyAccountHandler = React.createClass({
	getInitialState: function()
	{
		// Get account id
		var id = this.props.params.id;

		// Load account model
		var account = new AccountModel({id: id});
		account.fetch();

		return {
			account_model: account,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Konto</h2>
				<EconomyAccount model={this.state.account_model} />
				<EconomyAccountTransactions type={TransactionCollection} params={{id: this.state.account_model.id}} />
			</div>
		);
	},
});

var EconomyAccountEditHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;

		var account = new AccountModel({id: id});
		account.fetch();

		return {
			model: account,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Redigera konto</h2>
				<EconomyAccount model={this.state.model} />
			</div>
		);
	},
});

var EconomyAccountAddHandler = React.createClass({
	getInitialState: function()
	{
		var account = new AccountModel();

		return {
			model: account,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Skapa konto</h2>
				<EconomyAccount model={this.state.model} />
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
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/economy/account/" + row.account_number + "/edit"}>{row.account_number}</Link></td>
				<td>{row.title}</td>
				<td>{row.description}</td>
				<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
			</tr>
		);
	},
});

var EconomyAccount = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	render: function()
	{
		return (
			<div>
				<form className="uk-form uk-form-horizontal">
					<div className="uk-form-row">
						<label className="uk-form-label">Kontonummer</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-database"></i>
								<input type="text" value={this.state.model.account_number} />
							</div>
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Titel</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-database"></i>
								<input type="text" value={this.state.model.title} />
							</div>
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Beskrivning</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-database"></i>
								<input type="text" value={this.state.model.description} />
							</div>
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Balans</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-usd"></i>
								<input type="text" value={this.state.model.balance} disabled />
							</div>
						</div>
					</div>
				</form>
			</div>
		);
	},
});

var EconomyAccountTransactions = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 6,
		};
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Bokföringsdatum</th>
				<th>Verifikation</th>
				<th>Transaktion</th>
				<th className="uk-text-right">Belopp</th>
				<th className="uk-text-right">Saldo</th>
				<th></th>
			</tr>
		);
	},

	renderRow: function (row, i)
	{
		if(typeof row.files != "undefined")
		{
			var icon = <i className="uk-icon-file"></i>;
		}
		else
		{
			var icon = "";
		}

		return (
			<tr key={i}>
				<td><DateField date={row.accounting_date}/></td>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>{row.instruction_number} {row.instruction_title}</Link></td>
				<td>{row.transaction_title}</td>
				<td className="uk-text-right"><Currency value={row.amount} currency="SEK" /></td>
				<td className="uk-text-right"><Currency value={row.balance} currency="SEK" /></td>
				<td>{icon}</td>
			</tr>
		);
	},
});

module.exports = {
	EconomyAccountsHandler,
	EconomyAccountHandler,
	EconomyAccountEditHandler,
	EconomyAccountAddHandler,
}
