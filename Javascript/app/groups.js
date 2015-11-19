import React from 'react'

var GroupsHandler = React.createClass({
	render: function()
	{
		console.log("Rendering");
		return (<h3>Grupper</h3>);
	},
});

var GroupHandler = React.createClass({
	render: function()
	{
		console.log("Rendering");
		return (<h3>Visa grupp</h3>);
	},
});

var GroupAddHandler = React.createClass({
	render: function()
	{
		console.log("Rendering");
		return (<h3>Skapa grupp</h3>);
	},
});

module.exports = { GroupsHandler, GroupHandler, GroupAddHandler }