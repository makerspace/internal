import React from 'react'

var MemberOverviewHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Medlemmar översikt</h2>
			</div>
		);
	},
});
MemberOverviewHandler.title = "Medlemmar översikt";

module.exports = {
	MemberOverviewHandler,
}