import React from 'react'
import BackboneTable from '../BackboneTable'
import TableDropdownMenu from '../TableDropdownMenu'

var Keys = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 5,
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
				<td>
					{(() => {
						switch (row.active) {
							case 1: return <span><i className="uk-icon-check key-active"></i>Ja</span>;
							case 0: return <span><i className="uk-icon-cross key-inactive"></i>Nej</span>;
						}
					})()}
				</td>
				<td>{row.tagid}</td>
				<td>{row.description}</td>
				<td>
					<TableDropdownMenu>
						<a onClick={this.edit.bind(this, i)}><i className="uk-icon uk-icon-cog" /> Redigera</a>
						{this.removeButton(i)}
					</TableDropdownMenu>
				</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Aktiv</th>
				<th>RFID</th>
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},
});

module.exports = Keys