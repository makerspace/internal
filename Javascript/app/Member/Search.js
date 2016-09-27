import React from 'react'

var MemberSearchHandler = React.createClass({
	search: function()
	{
//		UIkit.modal.alert("Searching");
		return true;
	},

	render: function()
	{
		return (
			<div>
				<h2>Sök medlem</h2>
				<p></p>
				<form onSubmit={this.search.bind()}>
					<div className="uk-form-icon">
						<i className="uk-icon-search"></i>
						<input type="text" placeholder="Sökord" />
					</div>
					<button type="submit" className="uk-button" onClick={this.search}>Sök</button>
				</form>
			</div>
		);
	},
});
MemberSearchHandler.title = "Sök medlem";

module.exports = {
	MemberSearchHandler,
}