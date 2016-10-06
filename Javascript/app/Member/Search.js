import React from 'react'
import { Link } from 'react-router'
import config from '../config'

class MemberSearchHandler extends React.Component
{
	constructor(props)
	{
		super(props);
		this.state = {
			data: [],
		};
	}

	search(event)
	{
		var _this = this;
		event.preventDefault();

		var input = this.refs.q.value;

		// Clear the search history so there is no result with old data when search text input is empty
		if(!input)
		{
			// TODO: Clear
			_this.setState({
				data: []
			});
			return;
		}

		$.ajax({
			method: "POST",
			url: config.apiBasePath + "/member/search",
			data: JSON.stringify({
				q: input,
			}),
		}).done(function(result) {
			_this.setState({
				data: result.data
			});
		});
	}

	render()
	{
		return (
			<div>
				<h2>Sök medlem</h2>
				<p>Skriv in valfritt sökord för att söka i medlemsregistret.</p>
				<form className="uk-form">
					<div className="uk-form-icon">
						<i className="uk-icon-search"></i>
						<input ref="q" type="text" className="uk-form-width-large" placeholder="Skriv in ett sökord" onChange={this.search.bind(this)} />
					</div>
				</form>
				<Result data={this.state.data} />
			</div>
		);
	}
}
MemberSearchHandler.title = "Sök medlem";

class Result extends React.Component
{
	render()
	{
		var result = this.props.data.map(function(row, i)
		{
			return (
				<li key={i}>
					<Link to={"/member/" + row.member_number}>{row.firstname} {row.lastname} (#{row.member_number})</Link>
				</li>
			);
		});

		return (
			<ul className="uk-list uk-list-striped">
				{result}
			</ul>
		);
	}
}


module.exports = {
	MemberSearchHandler,
}