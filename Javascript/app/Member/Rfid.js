import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	BackboneTable,
} from '../BackboneTable'
import {
	RfidModel,
	RfidCollection
} from '../models'


var RfidStuff = React.createClass(
{
	getInitialState: function()
	{
		return {
			showEditForm: false,
		};
	},

	edit: function(entity)
	{
		console.log("Parent edit");
		console.log(entity);

		// Load the entity into the edit form
		this.setState({
			showEditForm: true,
			rfidModel: entity,
		});
	},

	add: function()
	{
		var newRfid = new RfidModel();

		// Load the entity into the edit form
		this.setState({
			showEditForm: true,
			rfidModel: newRfid,
		});
	},

	rfidClose: function()
	{
		this.setState({
			showEditForm: false,
		});

		this.state.rfidModel.destroy();
	},

	rfidSave: function()
	{
		this.setState({
			showEditForm: false,
		});	
	},

	render: function()
	{
		var edit;
		if(this.state.showEditForm)
		{
			return (
				<RfidEdit model={this.state.rfidModel} ref="edit" member_number={this.props.member_number} close={this.rfidClose} save={this.rfidSave} />
			);
		}
		else
		{
			return (
				<div>
					<MemberKeys type={RfidCollection} member_number={this.props.member_number} edit={this.edit} />
					<button className="uk-button" onClick={this.add}><i className="uk-icon-plus" /> Lägg till RFID-tagg</button>
				</div>
			);
		}
	},
});

var MemberKeys = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 5,
		};
	},

	componentWillMount: function()
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
	},

	removeTextMessage: function(entity)
	{
		return "Are you sure you want to remove key \"" + entity.tagid + "\"?";
	},

	removeErrorMessage: function()
	{
		UIkit.modal.alert("Error deleting key");
	},

	edit: function(row)
	{
		var entity = this.getCollection().at(row);
		this.props.edit(entity);
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td>{row.tagid}</td>
				<td>
					{(() => {
						switch (row.active) {
							case 1: return <span><i className="uk-icon-check key-active"></i>Ja</span>;
							case 0: return <span><i className="uk-icon-cross key-inactive"></i>Nej</span>;
						}
					})()}
				</td>
				<td>{row.title}</td>
				<td>{row.description}</td>
				<td className="uk-text-right">
					<a onClick={this.edit.bind(this, i)} className="uk-icon-hover uk-icon-cog"> Redigera</a>
					{this.removeButton(i)}
				</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>RFID</th>
				<th>Aktiv</th>
				<th>Titel</th>
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},
});

var RfidEdit = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	close: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		// Tell parent to close form
		this.props.close();
	},

	save: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		// Add a relation to the member and save the model
		this.getModel().save({
			relations: [
				{
					type: "member",
					member_number: this.props.member_number
				}
			]
		});

		// Tell parent to save form
		this.props.save();
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
		if(this.state.model.entity_id === undefined)
		{
			var title = "Lägg till ny RFID-tagg";
		}
		else
		{
			var title = "Redigera RFID-tagg";
		}

		return (
			<form className="uk-form uk-form-horizontal">
			<div className="">
				<h3>{title}</h3>

				<div className="uk-form-row">
					<label className="uk-form-label" htmlFor="tagid">
						ID
					</label>
					<div className="uk-form-controls">
						<div className="uk-form-icon">
							<i className="uk-icon-tag"></i>
							<input type="text" id="tagid" name="tagid" value={this.state.model.tagid} className="uk-form-width-large" onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="uk-form-row">
					<label className="uk-form-label" htmlFor="title">
						Titel
					</label>
					<div className="uk-form-controls">
						<div className="uk-form-icon">
							<i className="uk-icon-tag"></i>
							<input type="text" id="title" name="title" value={this.state.model.title} className="uk-form-width-large" onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="uk-form-row">
					<label className="uk-form-label" htmlFor="description">
						Beskrivning
					</label>
					<div className="uk-form-controls">
						<textarea id="description" name="description" value={this.state.model.description} className="uk-form-width-large" onChange={this.handleChange} />
					</div>
				</div>

				<div className="uk-form-row">
					<div className="uk-form-controls">
						<button type="submit" onClick={this.close} className="uk-float-left uk-button uk-button-danger"><i className="uk-icon-close"></i> Avbryt</button>
						<button type="submit" onClick={this.save} className="uk-float-right uk-button uk-button-success"><i className="uk-icon-save"></i> Spara</button>
					</div>
				</div>
			</div>
			</form>
		);
	},
});

module.exports = {
	RfidStuff,
	MemberKeys,
	RfidEdit,
}