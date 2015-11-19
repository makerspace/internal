import React from 'react'

var DashboardHandler = React.createClass({
	render: function ()
	{
		return (
			<div className="uk-width-1-1">
				<div className="uk-panel uk-panel-box uk-panel-box-primary">
					<h1 className="uk-heading-large">Makerspace internal systems thingamajig</h1>
				</div>
			</div>
		);
	}
});

var ExportHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Export</h2>
			</div>
		);
	},
});

module.exports = {
	DashboardHandler,
	ExportHandler,
}