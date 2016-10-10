import React from 'react'
import BackboneReact from 'backbone-react-component'

// Backbone
import MemberCollection from '../Backbone/Collections/Member'

import { Link } from 'react-router'
import BackboneTable from '../BackboneTable'
import DateField from '../Formatters/Date'
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
				<Link to="/members/add" className="uk-button uk-button-primary"><i className="uk-icon-plus-circle"></i> Skapa ny medlem</Link>

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
			// Update the paginator so that is tells us we're on page 1
			this.pagination[1].currentPage = 0;
			this.pagination[2].currentPage = 0;
			this.pagination[1].render();
			this.pagination[2].render();

			// Make sure the Backbone collection will receive page 1
			this.getCollection().state.currentPage = 1;

			// Get data from the server
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