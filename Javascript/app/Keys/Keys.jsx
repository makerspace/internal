import React from 'react'
import BackboneTable from '../BackboneTable'
import TableDropdownMenu from '../TableDropdownMenu'

// Backbone
import RfidModel from '../Backbone/Models/Rfid'

var Keys = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 4,
		};
	},

	componentWillMount: function()
	{
		this.fetch();
	},

	componentWillReceiveProps: function(nextProps)
	{
		if(nextProps.filters != this.state.filters)
		{
			this.setState({
				filters: nextProps.filters
			});

			// TODO: setState() has a delay so we need to wait a moment
			var _this = this;
			setTimeout(function() {
				_this.fetch();
			}, 100);
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
		// We need to load a new model because the model can not belong to two different components at the same time.
		this.props.edit(this.getCollection().at(row).clone());
	},

	renderHeader: function()
	{
		return [
			{
				title: "Aktiv",
				sort: "active",
			},
			{
				title: "RFID",
				sort: "tagid",
			},
			{
				title: "Beskrivning",
				sort: "description",
			},
			{
				title: "",
			},
		];
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
});

module.exports = Keys