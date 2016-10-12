import React from 'react'
import BackboneReact from 'backbone-react-component'

// Backbone
import GroupModel from '../Backbone/Models/Group'
import MemberCollection from '../Backbone/Collections/Member'

import { Link, browserHistory } from 'react-router'
import GroupMembers from './Members'

var GroupHandler = React.createClass({
	getInitialState: function()
	{
		var group = new GroupModel({
			entity_id: this.props.params.id
		});
		group.fetch();

		this.title = "Meep";
		return {
			model: group,
		};
	},

	render: function()
	{
		return (
			<div>
				<Group model={this.state.model} />
				<GroupMembers type={MemberCollection}
					filters={{
						relations:
						[
							{
								type: "member",
								member_number: this.props.member_number,
							}
						]
					}}
				/>
			</div>
		);
	},
});
GroupHandler.title = "Visa grupp";

var GroupEditHandler = React.createClass({
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
GroupEditHandler.title = "Visa grupp";

var GroupAddHandler = React.createClass({
	getInitialState: function()
	{
		var newGroup = new GroupModel();
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

var Group = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	cancel: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		UIkit.modal.alert("TODO: Cancel");
	},

	remove: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		UIkit.modal.alert("TODO: Remove");
	},

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
					browserHistory.push("/groups");
					UIkit.modal.alert("Successfully created");
				}
				else if(response.status == "updated")
				{
					browserHistory.push("/groups");
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
				<h2>{this.state.model.entity_id ? "Redigera grupp" : "Skapa grupp"}</h2>

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

					<div className="uk-form-row">
						<button className="uk-button uk-button-danger uk-float-left" onClick={this.cancel}><i className="uk-icon-close"></i> Avbryt</button>

						{this.state.model.entity_id ? <button className="uk-button uk-button-danger uk-float-left" onClick={this.remove}><i className="uk-icon-trash"></i> Ta bort grupp</button> : ""}

						<button className="uk-button uk-button-success uk-float-right" onClick={this.save}><i className="uk-icon-save"></i> Spara grupp</button>
					</div>
				</form>
			</div>
		);
	},
});

module.exports = {
	GroupHandler,
	GroupAddHandler,
	GroupEditHandler,
	Group
}