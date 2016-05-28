import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	LabAccessModel,
	LabAccessCollection
} from '../models'
import { Link } from 'react-router'

var keyHeaders = new Backbone.Model({
	headers: ["Member #", "End date", "Description"],
	blurb: "Wherein the keys are edited and henceforth accessed.",
	caption: "Change keys"
});

var SalesSubscriptionsHandler = React.createClass({
	getInitialState: function()
	{
		var keys = new LabAccessCollection([
			new LabAccessModel({member_id: 1023, date_end: new Date()}),
			new LabAccessModel({member_id: 1653, date_end: new Date(), description: "Temp"})
		]);

		//keys.fetch();

		return {
			collection: keys
		};
	},

	render: function () {
		return (
			<LabAccessTable model={keyHeaders} collection={this.state.collection} />
		);
	}
});

var LabAccessTable = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	renderHeader: function (header, i)
	{
		return (
			<th key={i}>{header}</th>
		);
	},

	renderRow: function (row, i)
	{
		return (
			<tr key={i}>
				<td>{row.member_id}</td>
				<td>{row.date_end.toJSON()}</td>
				<td>{row.description}</td>
			</tr>
		);
	},

	render: function ()
	{
		return (
			<div className="uk-width-1-1">
				<h2>Prenumerationer</h2>
				<p>{this.state.model.blurb}</p>
				<table className="uk-table uk-table-striped uk-table-hover">
					<caption>{this.state.model.caption}</caption>
					<thead>
						<tr>
							{this.state.model.headers.map(this.renderHeader)}
						</tr>
					</thead>
					<tbody>
						{this.state.collection.map(this.renderRow)}
					</tbody>
				</table>
			</div>
		);
	}
});

module.exports = { SalesSubscriptionsHandler }