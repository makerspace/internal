import React from 'react'
import BackboneReact from 'backbone-react-component'

// Backbone
import AccountModel from '../Backbone/Models/Account'
import AccountCollection from '../Backbone/Collections/Account'
import TransactionCollection from '../Backbone/Collections/Transaction'

import { Link } from 'react-router'
import Currency from '../Formatters/Currency'
import DateField from '../Formatters/Date'
import BackboneTable from '../BackboneTable'

import { EconomyAccountingInstructionList } from './Instruction'
import EconomyTransactions from './Transactions'

var EconomyAccountsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Konton</h2>
				<Link to={"/economy/account/add"} className="uk-button uk-button-success"><i className="uk-icon-plus-circle"></i> Skapa nytt konto</Link>
				<EconomyAccounts type={AccountCollection} />
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
				<Transactions type={TransactionCollection} params={{id: this.state.account_model.id}} />
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

	componentWillMount: function()
	{
		this.state.collection.fetch();
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

module.exports = {
	EconomyAccountsHandler,
	EconomyAccountHandler,
	EconomyAccountEditHandler,
	EconomyAccountAddHandler,
}