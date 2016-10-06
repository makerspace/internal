import React from 'react'

var Loading = React.createClass({
	render: function()
	{
		return (
			<span><i className="uk-icon-refresh uk-icon-spin"></i> Hämtar data...</span>
		);
	},
});

var Currency = React.createClass({
	render: function()
	{
		var formatter = new Intl.NumberFormat('sv-SE', {
			/*
			style: 'currency',
			currency: 'SEK',
			*/
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		});

		var value = formatter.format(this.props.value / 100);
		return (<span>{value} {this.props.currency}</span>);
	},
});

var DateField = React.createClass({
	render: function()
	{
		var options = {
			year: 'numeric', month: 'numeric', day: 'numeric',
			hour: 'numeric', minute: 'numeric', second: 'numeric',
			hour12: false
		};

//		console.log(Date.parse(this.props.date));
		var str = new Intl.DateTimeFormat('sv-SE', options).format(Date.parse(this.props.date));
//		console.log(str);
		return (<span>{str}</span>);
	},
});

module.exports = {
	Loading,
	Currency,
	DateField,
}