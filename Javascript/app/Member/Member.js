import React from 'react'
import BackboneReact from 'backbone-react-component'
import { MemberModel } from '../models'
import { Link, browserHistory } from 'react-router'
import { BackboneTable } from '../BackboneTable'
import { DateField } from '../Common'

// Import functions from other modules
import { KeysUserBox } from '../Keys/KeysUserBox'
import { MailUserBox } from '../Mail/MailUserBox'
import { GroupUserBox } from '../Group/GroupUserBox'
import { SubscriptionUserBox } from '../Sales/SubscriptionUserBox'
import { TransactionUserBox } from '../Economy/TransactionUserBox'

import CountryDropdown from '../CountryDropdown'

var MemberHandler = React.createClass({
	getInitialState: function()
	{
		var member = new MemberModel({
			member_number: this.props.params.id
		});
		console.log(member);

		var _this = this;
		member.fetch({
			success: function() {
				console.log("success");
				_this.forceUpdate();
			}
		});

//		this.title = "Meep";
		return {
			model: member,
		};
	},

	componentDidMount: function()
	{
		// Ugly way to get the switcher javascript working
		$.UIkit.init();
/*
		var _this = this;
		$("[data-uk-switcher]").on("show.uk.switcher", function(event, area) {
			if(area.context.id == "member_keys")
			{
				if(!this.keys_synced)
				{
					// Get the RFID keys associated with the member
					_this.state.model.keys = 
					_this.state.collection = _this.state.model.keys;

					_this.state.model.keys.fetch();
					this.keys_synced = true;
				}
			}
		});
*/
	},

	render: function()
	{
		return (
			<div>
				<h2>Medlem #{this.state.model.get("member_number")}: {this.state.model.get("firstname")} {this.state.model.get("lastname")}</h2>

				<ul className="uk-tab" data-uk-switcher="{connect:'#user-tabs'}">
					<li id="member_info"><a>Personuppgifter</a></li>
					<li id="member_keys"><a>Nycklar</a></li>
					<li id="member_transactions"><a>Transaktioner</a></li>
					<li id="member_labaccess"><a>Prenumerationer</a></li>
					<li id="member_groups"><a>Grupper</a></li>
					<li id="member_groups"><a>Utskick</a></li>
				</ul>

				<ul id="user-tabs" className="uk-switcher">
					<li>
						<MemberForm model={this.state.model} />
					</li>
					<li>
						<KeysUserBox member_number={this.state.model.get("member_number")} />
					</li>
					<li>
						<TransactionUserBox member_number={this.state.model.get("member_number")} />
					</li>
					<li>
						<SubscriptionUserBox member_number={this.state.model.get("member_number")} />
					</li>
					<li>
						<GroupUserBox member_number={this.state.model.get("member_number")} />
					</li>
					<li>
						<MailUserBox member_number={this.state.model.get("member_number")} />
					</li>
				</ul>
			</div>
		);
	},
});
MemberHandler.title = "Visa medlem";

var MemberForm = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	cancel: function(event)
	{
		// Prevent the form from being submitted
		event.preventDefault();

		console.log("TODO: Clear");
		console.log(this.getModel());
	},

	save: function(event)
	{
		console.log("TODO: Save member");

		var _this = this;

		// Prevent the form from being submitted
		event.preventDefault();

		this.getModel().save([], {
			success: function(model, response)
			{
				if(response.status == "created")
				{
					UIkit.modal.alert("Successfully created");
					browserHistory.push("/members/" + response.entity.entity_id);
				}
				else if(response.status == "updated")
				{
					UIkit.modal.alert("Successfully updated");
				}
				else
				{
					_this.error();
				}
			},
			error: function(model, response, options) {
				_this.error();
			},
		});
	},

	error: function()
	{
		UIkit.modal.alert("Error saving model");
	},

	handleChange: function(event)
	{
		// Update the model with new value
		var target = event.target;
		var key = target.getAttribute("name");
		this.state.model[key] = target.value;

		// When we change the value of the model we have to rerender the component
		this.forceUpdate();
	},

	render: function()
	{
		return (
			<div className="meep">
			<form className="uk-form uk-form-horizontal">
				<fieldset >
					<legend><i className="uk-icon-user"></i> Personuppgifter</legend>

					<div className="uk-form-row">
						<label className="uk-form-label">Personnummer</label>
						<div className="uk-form-controls">
							<input type="text" name="civicregno" value={this.state.model.civicregno} onChange={this.handleChange} className="uk-form-width-large" />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Förnamn</label>
						<div className="uk-form-controls">
							<input type="text" name="firstname" value={this.state.model.firstname} onChange={this.handleChange} className="uk-form-width-large" />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Efternamn</label>
						<div className="uk-form-controls">
							<input type="text" name="lastname" value={this.state.model.lastname} onChange={this.handleChange} className="uk-form-width-large" />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">E-post</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-envelope"></i>
								<input type="text" name="email" value={this.state.model.email} onChange={this.handleChange} className="uk-form-width-large" />
							</div>
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Telefonnummer</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-phone"></i>
								<input type="text" name="phone" value={this.state.model.phone} onChange={this.handleChange} className="uk-form-width-large" />
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset data-uk-margin>
					<legend><i className="uk-icon-home"></i> Adress</legend>

					<div className="uk-form-row">
						<label className="uk-form-label">Address</label>
						<div className="uk-form-controls">
							<input type="text" name="address_street" value={this.state.model.address_street} onChange={this.handleChange} className="uk-form-width-large" />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Address extra</label>
						<div className="uk-form-controls">
							<input type="text" name="address_extra" value={this.state.model.address_extra} onChange={this.handleChange} className="uk-form-width-large" />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Postadress</label>
						<div className="uk-form-controls">
							<input type="text" name="address_zipcode" value={this.state.model.address_zipcode} onChange={this.handleChange} className="uk-form-width-small" />
							<input type="text" name="address_city" value={this.state.model.address_city} onChange={this.handleChange} />
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Land</label>
						<div className="uk-form-controls">
							<CountryDropdown country={this.state.model.address_country} onChange={this.changeCountry} />
						</div>
					</div>
				</fieldset>

				<fieldset data-uk-margin>
					<legend><i className="uk-icon-tag"></i> Metadata</legend>

					<div className="uk-form-row">
						<label className="uk-form-label">Medlem sedan</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-calendar"></i>
								<input type="text" value={this.state.model.created_at} disabled />
							</div>
						</div>
					</div>

					<div className="uk-form-row">
						<label className="uk-form-label">Senast uppdaterad</label>
						<div className="uk-form-controls">
							<div className="uk-form-icon">
								<i className="uk-icon-calendar"></i>
								<input type="text" value={this.state.model.updated_at} disabled />
							</div>
						</div>
					</div>
				</fieldset>

				<div className="uk-form">
					<button className="uk-button uk-button-danger uk-float-left" onClick={this.cancel}><i className="uk-icon-close"></i> Återställ</button>
					<button className="uk-button uk-button-success uk-float-right" onClick={this.save}><i className="uk-icon-save"></i> Spara personuppgifter</button>
				</div>
			</form>
			</div>
		);
	},

	changeCountry: function(country)
	{
		this.getModel().set({
			address_country: country
		});
	}
});

var MemberAddHandler = React.createClass({
	getInitialState: function()
	{
		return {
			model: new MemberModel(),
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Skapa medlem</h2>
				<MemberForm model={this.state.model} />
			</div>
		);
	},
});
MemberAddHandler.title = "Skapa medlem";

module.exports = {
	MemberHandler,
	MemberAddHandler
}