import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	GroupModel,
	GroupCollection
} from '../models'
import { Link, Router } from 'react-router'
import { browserHistory } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Economy/Other'

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
		var group = new GroupModel({id: id});
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

	error: function()
	{
		UIkit.modal.alert("Error deleting entry");
	},

	remove: function(row, e)
	{
		var _this = this;
		var entity = this.getCollection().at(row);
		var a = entity.attributes;
		console.log(a);

		UIkit.modal.confirm("Are you sure you want to remove group " + a.group_id + " \"" + a.title + "\"?", function() {
			entity.destroy({
				wait: true,
				success: function(model, response) {
					if(response.status == "deleted")
					{
						UIkit.modal.alert("Successfully deleted");
						// TODO: Reload?
					}
					else
					{
						_this.error();
					}
				},
				error: function() {
					_this.error();
				},
			});
		});
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/member/group/" + row.group_id}>{row.group_id}</Link></td>
				<td><Link to={"/member/group/" + row.group_id}>{row.title}</Link></td>
				<td><Link to={"/member/group/" + row.group_id}>{row.description}</Link></td>
				<td><DateField date={row.created_at} /></td>
				<td className="uk-text-right"><a href="#" onClick={this.remove.bind(this, i)} className="uk-icon-remove uk-icon-hover"> Ta bort</a></td>
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
				<th>Skapad</th>
				<th></th>
			</tr>
		);
	},
});

var Group = React.createClass({
	mixins: [Backbone.React.Component.mixin],
	contextTypes: {
        router: React.PropTypes.func
    },

	cancel: function()
	{
		console.log("TODO: Cancel");
	},

	edit: function()
	{
		console.log("TODO: Edit group");
	},

	save: function()
	{
		var _this = this;

		this.getModel().save([], {
			success: function(model, response)
			{
				if(response.status == "created")
				{
					UIkit.modal.alert("Successfully created");
					browserHistory.push("/member/group/" + response.entity.group_id)
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

	render: function()
	{
		return (
			<div>
				<h2>Skapa grupp</h2>

				<form className="uk-form uk-form-horizontal">
					<div className="uk-grid">
						<div className="uk-width-1-3">
							<label className="uk-form-label">Gruppnummer</label>
						</div>
						<div className="uk-width-2-3">
							<div className="uk-form-icon">
								<i className="uk-icon-hashtag"></i>
								{this.state.model.group_id}
								
							</div>
						</div>
					</div>

					<div className="uk-grid">
						<div className="uk-width-1-3">
							<label className="uk-form-label">Namn</label>
						</div>
						<div className="uk-width-2-3">
							<div className="uk-form-icon">
								<i className="uk-icon-tag"></i>
								<input type="text" defaultValue={this.state.model.title} />
							</div>
						</div>
					</div>

					<div className="uk-grid">
						<div className="uk-width-1-3">
							<label className="uk-form-label">Beskrivning</label>
						</div>
						<div className="uk-width-2-3">
							<textarea defaultValue={this.state.model.description}></textarea>
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
	GroupAddHandler
}