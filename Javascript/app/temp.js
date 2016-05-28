import React from 'react'

var DashboardHandler = React.createClass({
	render: function ()
	{
		return (
			<div className="uk-width-1-1">
				<div className="uk-panel uk-panel-box uk-panel-box-primary">
					<h1 className="uk-heading-large">Makerspace internal</h1>
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
				<p>Kör en request mot API och plocka ut data med hjälp av samma filtreringsinställnigar som finns i övriga gränssnitt. Konvertera sedan detta till en *.csv, eller annat lämpligt format och låt användaren ladda hem en fil.</p>
			</div>
		);
	},
});

module.exports = {
	DashboardHandler,
	ExportHandler,
}