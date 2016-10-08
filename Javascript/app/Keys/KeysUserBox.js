import React from 'react'
import {
	RfidModel,
	RfidCollection
} from '../models'
import { Keys } from './Keys'
import { Edit } from './Edit'

var KeysUserBox = React.createClass(
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
		// TODO: Something here gives us an error message
//		this.state.rfidModel.trigger("destroy", this.state.rfidModel);

		this.setState({
			showEditForm: false,
		});
	},

	rfidSave: function()
	{
		this.setState({
			showEditForm: false,
		});	
	},

	render: function()
	{
		if(this.state.showEditForm)
		{
			return (
				<Edit model={this.state.rfidModel} ref="edit" member_number={this.props.member_number} close={this.rfidClose} save={this.rfidSave} />
			);
		}
		else
		{
			return (
				<div>
					<Keys type={RfidCollection} member_number={this.props.member_number} edit={this.edit} />
					<button className="uk-button" onClick={this.add}><i className="uk-icon-plus" /> Lägg till RFID-tagg</button>
				</div>
			);
		}
	},
});

module.exports = {
	KeysUserBox,
}