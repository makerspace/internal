import React from 'react'
import BackboneReact from 'backbone-react-component'
import { GroupCollection } from '../models'
import { Link } from 'react-router'
import { BackboneTable } from '../BackboneTable'
import TableDropdownMenu from '../TableDropdownMenu'

var GroupsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Grupper</h2>
				<p>På denna sida ser du en lista på samtliga grupper.</p>
				<Link className="uk-button" to="/groups/add"><i className="uk-icon-plus"></i> Skapa ny grupp</Link>
				<Groups type={GroupCollection} />
			</div>
		);
	},
});
GroupsHandler.title = "Visa grupper";

var Groups = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 9,
		};
	},

	componentWillMount: function()
	{
		if(this.props.member_number !== undefined)
		{
			// Load RFID keys related to member
			this.state.collection.fetch({
				data: {
					relation: {
						type: "member",
						member_number: this.props.member_number,
					}
				}
			});
		}
		else
		{
			// Load all RFID keys
			this.state.collection.fetch();
		}
	},

	removeTextMessage: function(entity)
	{
		return "Are you sure you want to remove group \"" + entity.title + "\"?";
	},

	removeErrorMessage: function()
	{
		UIkit.modal.alert("Error deleting group");
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/groups/" + row.entity_id}>{row.title}</Link></td>
				<td><Link to={"/groups/" + row.entity_id}>{row.description}</Link></td>
				<td>
					<TableDropdownMenu>
						<Link to={"/groups/" + row.entity_id + "/edit"}><i className="uk-icon uk-icon-cog"></i> Redigera grupp</Link>
						{this.removeButton(i, "Ta bort grupp")}
					</TableDropdownMenu>
				</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Namn</th>
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},
});

module.exports = {
	GroupsHandler,
	Groups
}