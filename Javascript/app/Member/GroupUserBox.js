import React from 'react'
import { Groups } from './Groups'
import {
	GroupCollection
} from '../models'
import { Async } from 'react-select';
import config from '../config'

var GroupUserBox = React.createClass(
{
	getInitialState: function()
	{
		return {
			showEditForm: false,
			addGroups: "",
		};
	},

	add: function()
	{
		this.setState({
			showEditForm: true,
		});
	},

	cancel: function()
	{
		this.setState({
			showEditForm: false,
			addGroups: "",
		});
	},

	changeValue: function(value)
	{
		this.setState({
			addGroups: value
		});

		// Clear the search history so there is no drop down with old data after adding a recipient
		this.refs.addgroups.setState({options: []});
	},

	// Disable client side filtering
	filter: function(option, filterString)
	{
		return option;
	},

	search: function(input, callback)
	{
		// Clear the search history so there is no drop down with old data when search text input is empty
		if(!input)
		{
			return Promise.resolve({ options: [] });
		}

		$.ajax({
			method: "POST",
			url: config.apiBasePath + "/member/search",
			data: JSON.stringify({
				q: input,
			}),
		}).done(function(data) {
			setTimeout(function() {
				var autoComplete = [];

				data.data.forEach(function(element, index, array){
					autoComplete.push({
						label: element.firstname + " " + element.lastname + " (#" + element.member_number + ")",
						value: element.member_number,
					});
				});

				callback(null, {
					options: autoComplete,
				});
			}, 100);

		});
	},

	// Send an API request and queue the message to be sent
	send: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		var groups = this.state.addGroups;
		console.log("OK! Add to groups: ");
		console.log(groups);

/*
		// Send API request
		$.ajax({
			method: "POST",
			url: config.apiBasePath + "/mail",
			data: JSON.stringify({
				type,
				recipients,
				subject,
				body
			}),
		}).done(function (){
			// TODO: Falhantering
			browserHistory.push("/mail/history");
		});
*/
	},

	gotoGroup: function(value, event)
	{
		UIkit.modal.alert("TODO: Go to member " + value.label);
	},

	render: function()
	{
		if(this.state.showEditForm)
		{
			return (
				<div>
					<form className="uk-form uk-form-horizontal" onSubmit={this.send}>
						<div className="uk-form-row">
							<label className="uk-form-label" htmlFor="groups">
								Lägg till användaren i följande grupper
							</label>
							<div className="uk-form-controls">
								<Async ref="addgroups" multi cache={false} name="groups" value={this.state.addGroups} filterOption={this.filter} loadOptions={this.search} onChange={this.changeValue} onValueClick={this.gotoGroup} />
							</div>
						</div>

						<div className="uk-form-row">
							<div className="uk-form-controls">
								<button className="uk-float-left uk-button uk-button-danger" onClick={this.cancel}><i className="uk-icon-close" /> Avbryt</button>
								<button className="uk-float-right uk-button uk-button-success" onClick={this.save}><i className="uk-icon-close" /> Spara</button>
							</div>
						</div>

					</form>
				</div>
			);
		}
		else
		{
			return (
				<div>
					<Groups type={GroupCollection} member_number={this.props.member_number} />
					<button className="uk-button" onClick={this.add}><i className="uk-icon-plus" /> Lägg till grupp</button>
				</div>
			);
		}
	},
});

module.exports = {
	GroupUserBox
}