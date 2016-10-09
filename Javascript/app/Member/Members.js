import React from 'react'
import BackboneReact from 'backbone-react-component'
import { MemberCollection } from '../models'
import { Link } from 'react-router'
import { BackboneTable } from '../BackboneTable'
import { DateField } from '../Common'
import config from '../config'

var MembersHandler = React.createClass({
	getInitialState: function()
	{
		return {
			search: "",
		};
	},

	search: function(event)
	{
		var _this = this;
		event.preventDefault();

		console.log("Searching for " + this.refs.q.value);
		this.setState({
			search: this.refs.q.value
		});
	},

	render: function()
	{
		return (
			<div>
				<h2>Medlemmar</h2>
				<p>På denna sida ser du en lista på samtliga medlemmar.</p>
				<Link className="uk-button" to="/members/add"><i className="uk-icon-plus"></i> Skapa ny medlem</Link>

				<form className="uk-form">
					<div className="uk-form-icon">
						<i className="uk-icon-search"></i>
						<input ref="q" type="text" className="uk-form-width-large" placeholder="Skriv in ett sökord" onChange={this.search} />
					</div>
				</form>

				<Members type={MemberCollection} search={this.state.search} />
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

	componentWillReceiveProps: function(nextProps)
	{
		this.fetch(nextProps.search);
	},

	fetch: function(search)
	{
		if(search !== undefined && search.length > 0)
		{
			console.log("OK, searching");
			this.getCollection().fetch({
				data: {
					search: search
				}
			});
		}
		else
		{
			this.getCollection().fetch();
		}
	},

	componentWillMount: function()
	{
		this.fetch();
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/members/" + row.member_number}>{row.member_number}</Link></td>
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
		console.log("Render header");
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