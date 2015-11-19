import React from 'react'
import { Loading } from './Common'

/// TODO: This Mixin's should handle auto refresh

/**
 * This is a mixin used with Backbone to provide error handlers.
 */
var BackboneTable = {
	getInitialState: function()
	{
		return {
			status: "loading",
		};
	},

	componentWillMount: function()
	{
		var _this = this;
		this.getCollection().on("request", function()
		{
			_this.setState({
				status: "loading"
			});
		});

		this.getCollection().on("sync", function()
		{
			_this.setState({
				status: "done"
			});
		});

		this.getCollection().on("error", function()
		{
			_this.setState({
				status: "error"
			});
		});
	},

	render: function ()
	{
		if(this.state.status == "loading")
		{
			var loading = (
				<div className="loadingOverlay">
					<div className="loadingWrapper">
						<Loading />
					</div>
				</div>
			);
			var loadingClass = " backboneTableLoading";
		}

		if(this.state.status == "error")
		{
			var content = (
				<tr>
					<td colSpan={this.state.columns} className="uk-text-center">
						<p>
							<em>Hämtning av data misslyckades.</em>&nbsp;&nbsp;<button className="uk-button uk-button-primary uk-button-mini" onClick={this.tryAgain}><i className="uk-icon-refresh"></i> Försök igen</button>
						</p>
					</td>
				</tr>
			);
		}
		else if(this.state.collection.length == 0)
		{
			var content = (
				<tr>
					<td colSpan={this.state.columns} className="uk-text-center">
						<em>Listan är tom</em>
					</td>
				</tr>
			);
		}
		else
		{
			var content = this.state.collection.map(this.renderRow);
		}

		return (
			<div style={{position: "relative"}}>
				<table className={"uk-table uk-table-condensed uk-table-striped uk-table-hover" + loadingClass}>
					<thead>
						{this.renderHeader()}
					</thead>
					<tbody>
						{content}
					</tbody>
				</table>
				{loading}
			</div>
		);
	},
	tryAgain: function()
	{
		this.getCollection().fetch();
	},
};

module.exports = {
	BackboneTable,
};
