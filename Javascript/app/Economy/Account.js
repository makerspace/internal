import React from 'react'
import BackboneReact from 'backbone-react-component'
import { AccountModel, AccountCollection, TransactionCollection } from '../models'
import { Link } from 'react-router'
import { Currency, DateField } from './Other'
import { BackboneTable } from '../BackboneTable'

import { EconomyAccountingInstructionList } from './Instruction'

var EconomyAccountsHandler = React.createClass({
	getInitialState: function()
	{
		var accounts = new AccountCollection();
		accounts.fetch();

		return {
			collection: accounts,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Konton</h2>
				<EconomyAccounts collection={this.state.collection} />
				<Link to={"/economy/account/add"}><i className="uk-icon-plus-circle"></i> Skapa konto</Link>
			</div>
		);
	},
});

var EconomyAccountHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;

		var account = new AccountModel({id: id});
		account.fetch();

		// TODO: Filter on account


		var transactions = new TransactionCollection();
		transactions.fetch();

/*
		var _this = this;

		var Instructions = InstructionCollection.extend({
			state:
			{
				pageSize: 15 // TODO
			},

			parseState: function(resp, queryParams, state, options)
			{
				/*
				// If the paginator is already set up we need to update the parameters and rerender it
				if(typeof _this.pagination != "undefined")
				{
					_this.pagination.pages = resp.last_page;
					_this.pagination.render();
				}
				*

				// Otherwise we just save the parameters to be used when initializing the paginator
				_this.setState({
					totalRecords: resp.total,
					totalPages:   resp.last_page,
					pageSize:     resp.per_page,
				});
			},
		});
		var instructions = new Instructions();
		instructions.fetch();
*/

		return {
			account_model: account,
			transaction_collection: transactions,
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Konto</h2>
				<EconomyAccount model={this.state.account_model} />
				<EconomyAccountTransactions collection={this.state.transaction_collection} />

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
			columns: 5,
		};
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Bokföringsdatum</th>
				<th>Verifikation</th>
				<th>Beskrivning</th>
				<th className="uk-text-right">Belopp</th>
			</tr>
		);
	},

	renderRow: function (row, i)
	{
		return (
			<tr key={i}>
				<td><DateField date={row.accounting_date}/></td>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>{row.instruction_number} {row.instruction_title}</Link></td>
				<td>{row.transaction_title}</td>
				<td className="uk-text-right"><Currency value={row.balance} /></td>
			</tr>

			/*
			<tr><td></td></tr>
			<tr key={i}>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>{row.instruction_number}</Link></td>
				<td><DateField date={row.accounting_date}/></td>
				<td>{row.title}</td>
				<td className="uk-text-right"><Currency value={row.balance}/></td>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>Visa</Link></td>
			</tr>
			*/
		);
	},
});

		/*
var EconomyAccountTransactions = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	render: function()
	{
		if(this.state.collection.length == 0)
		{
			var content = <tr><td colSpan="4"><em>Det finns inga verifikationer som bokför något på detta konto</em></td></tr>;
		}
		else
		{
			var content = this.state.collection.map(function (instruction, i)
			{
				if(instruction.title.length == 0)
				{
					instruction.title = <em>Rubrik saknas</em>
				}
				return (
					<tr key={i}>
						<td>{instruction.verification_number}</td>
						<td><DateField date={instruction.accounting_date}/></td>
						<td><Link to={"/economy/instruction/" + instruction.id}>{instruction.title}</Link></td>
						<td>{instruction.description}</td>
						<td className="uk-text-right"><Currency value={instruction.amount} /></td>
					</tr>
				);
			})
		}

		return (
			<table className="uk-table">
				<thead>

				</thead>
				<tbody>
					{content}
				</tbody>
			</table>
		);
		return <p>instructions</p>
	},
});
*/

module.exports = {
	EconomyAccountsHandler,
	EconomyAccountHandler,
	EconomyAccountEditHandler,
	EconomyAccountAddHandler,
}
