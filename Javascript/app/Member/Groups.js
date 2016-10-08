import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	GroupModel,
	GroupCollection
} from '../models'
import { Link, Router, browserHistory } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'

var GroupsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Grupper</h2>
				<p>På denna sida ser du en lista på samtliga grupper.</p>
				<Groups type={GroupCollection} />
				<Link className="uk-button" to="/member/group/add"><i className="uk-icon-plus"></i> Skapa ny grupp</Link>
			</div>
		);
	},
});
GroupsHandler.title = "Visa grupper";

var GroupHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;
		var group = new GroupModel({entity_id: id});
		group.fetch();

		this.title = "Meep";
		return {
			model: group,
		};
	},

	render: function()
	{
		return (
			<Group model={this.state.model} />
		);
	},
});
GroupHandler.title = "Visa grupp";

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
				<td><Link to={"/member/group/" + row.entity_id}>{row.group_id}</Link></td>
				<td><Link to={"/member/group/" + row.entity_id}>{row.title}</Link></td>
				<td><Link to={"/member/group/" + row.entity_id}>{row.description}</Link></td>
				<td className="uk-text-right">{this.removeButton(i)}</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Namn</th>
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},
});

var Group = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	save: function(event)
	{
		var _this = this;

		// Prevent the form from being submitted
		event.preventDefault();

		this.getModel().save([], {
			success: function(model, response)
			{
				if(response.status == "created")
				{
					UIkit.modal.alert("Successfully created");
					browserHistory.push("/member/group/" + response.entity.entity_id);
				}
				else if(response.status == "updated")
				{
					UIkit.modal.alert("Successfully updated");
				}
				else
				{
					_this.error();
				}
			},
			error: function(model, response, options) {
				_this.error();
			},
		});
	},

	error: function()
	{
		UIkit.modal.alert("Error saving model");
	},

	handleChange: function(event)
	{
		// Update the model with new value
		var target = event.target;
		var key = target.getAttribute("name");
		this.state.model[key] = target.value;

		// When we change the value of the model we have to rerender the component
		this.forceUpdate();
	},

	render: function()
	{
		return (
			<div>
				<h2>Skapa/redigera grupp</h2>

				<form className="uk-form uk-form-horizontal" onSubmit={this.save}>
					<div className="uk-grid">
						<div className="uk-width-1-3">
							<label className="uk-form-label">Namn</label>
						</div>
						<div className="uk-width-2-3">
							<div className="uk-form-icon">
								<i className="uk-icon-tag"></i>
								<input type="text" name="title" value={this.state.model.title} onChange={this.handleChange} />
							</div>
						</div>
					</div>

					<div className="uk-grid">
						<div className="uk-width-1-3">
							<label className="uk-form-label">Beskrivning</label>
						</div>
						<div className="uk-width-2-3">
							<textarea name="description" value={this.state.model.description} onChange={this.handleChange}></textarea>
						</div>
					</div>
				</form>

				<div>
					<button className="uk-button" onClick={this.save}><i className="uk-icon-save"></i> Spara</button>
				</div>
			</div>
		);
	},
});

var GroupAddHandler = React.createClass({
	getInitialState: function()
	{
		var newGroup = new GroupModel({
			title: "New group",
			description: "This group is Awesome!"
		});
		return {
			model: newGroup,
		};
	},

	render: function()
	{
		return (
			<div>
				<Group model={this.state.model} />
			</div>
		);
	},
});

module.exports = {
	GroupsHandler,
	GroupHandler,
	GroupAddHandler,
	Groups
}