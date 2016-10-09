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

var MemberHandler = React.createClass({
	getInitialState: function()
	{
		var member = new MemberModel({
			member_number: this.props.params.id
		});
		console.log(member);
		member.fetch();

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
				<h2>Redigera {this.state.model.get("firstname")} {this.state.model.get("lastname")} #{this.state.model.get("member_number")}</h2>

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
			<form className="uk-form uk-form-horizontal">
				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">Medlemsnummer</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-tag"></i>
							<input type="text" name="member_number" value={this.state.model.member_number} disabled />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Skapad</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-calendar"></i>
							<input type="text" value={this.state.model.created_at} disabled />
						</div>
					</div>
				</div>

				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">Personnummer</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-calendar"></i>
							<input type="text" name="civicregno" value={this.state.model.civicregno} onChange={this.handleChange} />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Uppdaterad</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-calendar"></i>
							<input type="text" value={this.state.model.updated_at} disabled />
						</div>
					</div>
				</div>

				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">Förnamn</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-user"></i>
							<input type="text" name="firstname" value={this.state.model.firstname} onChange={this.handleChange} />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Efternamn</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-user"></i>
							<input type="text" name="lastname" value={this.state.model.lastname} onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">E-post</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-envelope"></i>
							<input type="text" name="email" value={this.state.model.email} onChange={this.handleChange} />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Telefonnummer</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-phone"></i>
							<input type="text" name="phone" value={this.state.model.phone} onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">Address</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-home"></i>
							<input type="text" name="address_street" value={this.state.model.address_street} onChange={this.handleChange} />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Land</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-home"></i>
							<input type="text" name="address_country" value={this.state.model.address_country} onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="uk-grid">
					<div className="uk-width-1-6">
						<label className="uk-form-label">Postnummer</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-home"></i>
							<input type="text" name="address_zipcode" value={this.state.model.address_zipcode} onChange={this.handleChange} />
						</div>
					</div>
					<div className="uk-width-1-6">
						<label className="uk-form-label">Postort</label>
					</div>
					<div className="uk-width-2-6">
						<div className="uk-form-icon">
							<i className="uk-icon-home"></i>
							<input type="text" name="address_city" value={this.state.model.address_city} onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<button className="uk-button" onClick={this.save}><i className="uk-icon-save"></i> Spara personuppgifter</button>
			</form>
		);
	},
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