import React from 'react'
import BackboneReact from 'backbone-react-component'
import { GroupModel } from '../models'
import { Link, browserHistory } from 'react-router'

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
			<div>
				Show content
				<Group model={this.state.model} />
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
					browserHistory.push("/groups");
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

module.exports = {
	GroupHandler,
	GroupAddHandler,
	GroupEditHandler,
	Group
}