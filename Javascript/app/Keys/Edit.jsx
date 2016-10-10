import React from 'react'
import BackboneReact from 'backbone-react-component'

var Edit = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	cancel: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		// Tell parent to close form
		this.props.close();
	},

	remove: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		UIkit.modal.alert("TODO: Remove");
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
						<button className="uk-button uk-button-danger uk-float-left" onClick={this.cancel}><i className="uk-icon-close"></i> Avbryt</button>

						{this.state.model.entity_id ? <button className="uk-button uk-button-danger uk-float-left" onClick={this.remove}><i className="uk-icon-trash"></i> Ta bort nyckel</button> : ""}

						<button className="uk-button uk-button-success uk-float-right" onClick={this.save}><i className="uk-icon-save"></i> Spara nyckel</button>
					</div>
				</div>
			</div>
			</form>
		);
	},
});

module.exports = Edit