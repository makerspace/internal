import React from 'react'

var TableFilterBox = React.createClass({
	getInitialState: function()
	{
		this.filters = {};
		return {};
	},

	buildNewFilterObject: function()
	{
		var newFilter = {};

		// Filters
		for(var key in this.filters)
		{
			var value = this.filters[key];
			console.log(key + ": " + value);
			newFilter[key] = value;
		}

		// Search
		if(this.refs.search.value != "")
		{
			newFilter["search"] = this.refs.search.value;
		}

		// Debugging
		console.log(newFilter);

		this.props.onChange(newFilter);
	},

	changeFilterValue: function(event)
	{
		var target = event.target;
		var key = target.getAttribute("name");
		this.filters[key] = target.value;

		this.buildNewFilterObject();
	},

	render: function()
	{
		return (
			<div>
				<label htmlFor="filter_active">
					Aktiv
				</label>

				<select ref="filter_active" id="filter_active" name="filter_active" onChange={this.changeFilterValue}>
					<option value="yes">Ja</option>
					<option value="no">Nej</option>
					<option value="auto">Auto</option>
				</select>

				<form className="uk-form">
					<div className="uk-form-icon">
						<i className="uk-icon-search"></i>
						<input ref="search" type="text" className="uk-form-width-large" placeholder="Skriv in ett sökord" onChange={this.buildNewFilterObject} />
					</div>
				</form>
			</div>
		);
	},
});

module.exports = TableFilterBox