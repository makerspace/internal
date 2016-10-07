import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	MemberCollection,
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Common'

var MembersHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Medlemmar</h2>
				<p>På denna sida ser du en lista på samtliga medlemmar.</p>
				<Members type={MemberCollection} />
			</div>
		);
	},
});
MembersHandler.title = "Visa medlemmar";

var Members = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 6,
		};
	},

	componentWillMount: function()
	{
		this.state.collection.fetch();
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/member/" + row.member_number}>{row.member_number}</Link></td>
				<td>-</td>
				<td>{row.firstname}</td>
				<td>{row.lastname}</td>
				<td>{row.email}</td>
				<td><DateField date={row.created_at} /></td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Kön</th>
				<th>Förnamn</th>
				<th>Efternamn</th>
				<th>E-post</th>
				<th>Blev medlem</th>
			</tr>
		);
	},
});

module.exports = {
	MembersHandler,
}