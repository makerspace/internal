import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	MemberModel,
	RfidCollection
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField } from '../Common'

var MemberHandler = React.createClass({
	getInitialState: function()
	{
		var member_number = this.props.params.id;
		var member = new MemberModel({member_number});
		member.fetch();

		this.title = "Meep";
		return {
			model: member,
		};
	},

	render: function()
	{
		return (
			<Member model={this.state.model} />
		);
	},
});
MemberHandler.title = "Visa medlem";

var Member = React.createClass({
	mixins: [Backbone.React.Component.mixin],

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
					browserHistory.push("/member/group/" + response.entity.entity_id);
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
			<div>
				<h2>{this.state.model.firstname} {this.state.model.lastname} #{this.state.model.member_number}</h2>

				<ul className="uk-tab" data-uk-switcher="{connect:'#user-tabs'}">
					<li id="member_info"><a>Personuppgifter</a></li>
					<li id="member_keys"><a>Nycklar</a></li>
					<li id="member_transactions"><a>Transaktioner</a></li>
					<li id="member_sales"><a>Försäljning</a></li>
					<li id="member_labaccess"><a>Prenumerationer</a></li>
					<li id="member_groups"><a>Grupper</a></li>
					<li id="member_groups"><a>Utskick</a></li>
				</ul>

				<ul id="user-tabs" className="uk-switcher">
					<li>
						<br />
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
						</form>
					</li>

					<li>
						<MemberKeys type={RfidCollection} member_number={this.state.model.member_number} />
					</li>

					<li>
						<table className="uk-table uk-table-striped uk-table-hover">
							<thead>
								<tr>
									<th>Datum</th>
									<th>Beskrivning</th>
									<th>Belopp</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colSpan="4"><em>Listan är tom</em></td>
								</tr>
							</tbody>
						</table>
					</li>

					<li>
						<p>Försäljningshistorik</p>
					</li>

					<li>
						<table className="uk-table uk-table-striped uk-table-hover">
							<thead>
								<tr>
									<th>Startdatum</th>
									<th>Slutdatum</th>
									<th>Beskrivning</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2015-01-01 00:00:00</td>
									<td>2015-12-31 23:59:59</td>
									<td>Medlemskap</td>
									<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
								</tr>
								<tr>
									<td>2013-11-27 00:00:00</td>
									<td>2016-02-12 23:59:59</td>
									<td>Labaccess</td>
									<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
								</tr>
							</tbody>
						</table>
					</li>

					<li>
						<table className="uk-table uk-table-striped uk-table-hover">
							<thead>
								<tr>
									<th>Beskrivning</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Styrelse</td>
									<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
								</tr>
							</tbody>
						</table>
					</li>

					<li>
						<p>Utskickshistorik</p>
					</li>
				</ul>

				<br />

				<div>
					<button className="uk-button" onClick={this.save}><i className="uk-icon-save"></i> Spara</button>
				</div>
			</div>
		);
	},
});

var MemberKeys = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 5,
		};
	},

	componentWillMount: function()
	{
		// Load RFID keys related to member
		this.state.collection.fetch({
			data: {
				relation: {
					type: "member",
					member_number: this.props.member_number,
				}
			}
		});
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td>{row.tagid}</td>
				<td>{row.active}</td>
				<td>{row.title}</td>
				<td>{row.description}</td>
				<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>RFID</th>
				<th>Aktiv</th>
				<th>Titel</th>
				<th>Beskrivning</th>
				<th></th>
			</tr>
		);
	},
});

var MemberAddHandler = React.createClass({
	getInitialState: function()
	{
		var newMember = new MemberModel({firstname: "new member"});
		return {
			model: newMember,
		};
	},

	render: function()
	{
		return <Member model={this.state.model}/>
	},
});
MemberAddHandler.title = "Skapa medlem";

module.exports = {
	MemberHandler,
	MemberKeys,
	MemberAddHandler
}