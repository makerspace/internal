import React from 'react'
import BackboneReact from 'backbone-react-component'
import { InstructionModel, InstructionCollection } from '../models'
import { Link } from 'react-router'
import { Currency, DateField } from './Other'
import {
	BackboneTable,
	PaginatedDataTable,
} from '../BackboneTable'

class EconomyAccountingInstructionsHandler extends PaginatedDataTable
{
	constructor(props)
	{
		super(props);
		this.state = {
			collection: this.createPaginatedCollection(InstructionCollection),
		};
	}

	render()
	{
		return (
			<div className="uk-width-1-1">
				<h2>Verifikationer</h2>
				<p>Lista över samtliga verifikationer i bokföringen</p>
				<EconomyAccountingInstructionList collection={this.state.collection} />
				{this.renderPaginator()}
			</div>
		);
	}
}
EconomyAccountingInstructionsHandler.title = "Visa verifikationer";

var EconomyAccountingInstructionHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;

		var instruction = new InstructionModel({id: id});
		instruction.fetch();

		return {
			model: instruction
		};
	},

	render: function()
	{
		return (<EconomyAccountingInstruction model={this.state.model} />);
	}
});

var EconomyAccountingInstructionImportHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;

		var instruction = new InstructionModel({id: id});
		instruction.fetch();

		return {
			model: instruction
		};
	},

	render: function()
	{
		return <EconomyAccountingInstructionImport model={this.state.model} />
	}
});

var EconomyAccountingInstructionList = React.createClass({
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
				<th>#</th>
				<th>Bokföringsdatum</th>
				<th>Beskrivning</th>
				<th className="uk-text-right">Belopp</th>
				<th></th>
			</tr>
		);
	},

	renderRow: function (row, i)
	{
//		if(typeof row.files != "undefined")
		if(row.has_vouchers)
		{
			var icon = <i className="uk-icon-file"></i>;
		}
		else
		{
			var icon = "";
		}

		return (
			<tr key={i}>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>{row.instruction_number}</Link></td>
				<td><DateField date={row.accounting_date}/></td>
				<td>{row.title}</td>
				<td className="uk-text-right"><Currency value={row.balance}/></td>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>Visa</Link></td>
				<td>{icon}</td>
			</tr>
		);
	},
});

var EconomyAccountingInstruction = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	render: function()
	{
		if(this.state.model.transactions.length == 0)
		{
			var content = <tr><td colSpan="4"><em>Denna verifikation saknar bokförda poster</em></td></tr>;
		}
		else
		{
			var content = this.state.model.transactions.map(function (row, i)
			{
				return (
					<tr key={i}>
						<td>
							<Link to={"/economy/account/" + row.account_number}>{row.account_number} {row.account_title}</Link>
						</td>
						<td>{row.title}</td>
						<td className="uk-text-right"><Currency value={row.balance} /></td>
					</tr>
				);
			})
		}

		var title = this.state.model.instruction_number === null ? 'Preliminär verifikation' : 'Verifikation ' + this.state.model.instruction_number;

		if(this.state.model.files.length == 0)
		{
			var files = <tr><td colSpan="4"><em>Det finns inga filer kopplade till denna verifikation</em></td></tr>;
		}
		else
		{
			var _this = this;
			var files = this.state.model.files.map(function (file, i)
			{
				return (
					<tr key={i}>
						<td><a href={"/api/v2/economy/2015/file/" + _this.state.model.external_id + "/" + file}>{file}</a></td>
					</tr>
				);
			})
		}

		return (
			<div>
				<h2>{title} - {this.state.model.title}</h2>

				<form className="uk-form uk-form-horizontal">

					<div className="uk-grid">
						<div className="uk-width-1-6">
							<label className="uk-form-label">Verifikationsnr</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-tag"></i>
								<input type="text" value={this.state.model.instruction_number} />
							</div>
						</div>
						<div className="uk-width-1-6">
							<label className="uk-form-label">Skapad</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-calendar"></i>
								<input type="text" value={this.state.model.created_at} disabled />
							</div>
						</div>
					</div>
					<div className="uk-grid">
						<div className="uk-width-1-6">
							<label className="uk-form-label">Bokföringsdatum</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-calendar"></i>
								<input type="text" value={this.state.model.accounting_date} />
							</div>
						</div>
						<div className="uk-width-1-6">
							<label className="uk-form-label">Ändrad</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-calendar"></i>
								<input type="text" value={this.state.model.updated_at} disabled />
							</div>
						</div>
					</div>

					<div className="uk-grid">
						<div className="uk-width-1-6">
							<label className="uk-form-label">Belopp</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-usd"></i>
								<input type="text" value={this.state.model.balance} disabled />
							</div>
						</div>
						<div className="uk-width-1-6">
							<label className="uk-form-label">Importerad från</label>
						</div>
						<div className="uk-width-2-6">
							<div className="uk-form-icon">
								<i className="uk-icon-institution"></i>
								<input type="text" value={this.state.model.importer} disabled />
							</div>
							<p><em><Link to={"/economy/instruction/" + this.state.model.id + "/import"}>Visa data från import</Link></em></p>
						</div>
					</div>

					<div className="uk-grid">
						<div className="uk-width-1-6">
							<label className="uk-form-label">Kommentar</label>
						</div>
						<div className="uk-width-3-6">
							<p>{this.state.model.description}</p>
						</div>
					</div>
				</form>

				<table className="uk-table">
					<thead>
						<tr>
							<th>Konto</th>
							<th>Kommentar</th>
							<th className="uk-text-right">Belopp</th>
						</tr>
					</thead>
					<tbody>
						{content}
					</tbody>
				</table>

				<table className="uk-table">
					<thead>
						<tr>
							<th>Filnamn</th>
						</tr>
					</thead>
					<tbody>
						{files}
					</tbody>
				</table>
			</div>
		);
	},
});

var EconomyAccountingInstructionImport = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	render: function()
	{
		return (
				<div>
					<h3>Data från import</h3>
					<Link to={"/economy/instruction/" + this.state.model.id}>Tillbaka till verifikation</Link>
					<form className="uk-form uk-form-horizontal">
					<div className="uk-grid">
						<div className="uk-width-1-2">
							<div className="uk-form-row">
								<label className="uk-form-label">Importerad från</label>
								<div className="uk-form-controls">
									<div className="uk-form-icon">
										<i className="uk-icon-institution"></i>
										<input type="text" value={this.state.model.importer} disabled />
									</div>
								</div>
							</div>

							<div className="uk-form-row">
								<label className="uk-form-label">Externt id</label>
								<div className="uk-form-controls">
									<div className="uk-form-icon">
										<i className="uk-icon-database"></i>
										<input type="text" value={this.state.model.external_id} disabled />
									</div>
								</div>
							</div>

							<div className="uk-form-row">
								<label className="uk-form-label">Externt datum</label>
								<div className="uk-form-controls">
									<div className="uk-form-icon">
										<i className="uk-icon-database"></i>
										<input type="text" value={this.state.model.external_date} disabled />
									</div>
								</div>
							</div>

							<div className="uk-form-row">
								<label className="uk-form-label">Data</label>
								<div className="uk-form-controls">
									<textarea value={this.state.model.external_data} />
								</div>
							</div>
						</div>
					</div>
					</form>
				</div>
		);
	},
});

module.exports = {
	EconomyAccountingInstructionsHandler,
	EconomyAccountingInstructionHandler,
	EconomyAccountingInstructionImportHandler,
	EconomyAccountingInstructionList,
}