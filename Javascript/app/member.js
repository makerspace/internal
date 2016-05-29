import React from 'react'
import BackboneReact from 'backbone-react-component'
import {
	MemberModel,
	MemberCollection,
	RfidCollection
} from './models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from './BackboneTable'
import { DateField } from './Economy/Other'

var MembersHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Medlemmar</h2>
				<p>På denna sida ser du en lista på samtliga medlemmar.</p>
				<Members type={MemberCollection} />
			</div>
		);
	},
});
MembersHandler.title = "Visa medlemmar";

var MemberHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;
		var member = new MemberModel({id: id});
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

var Members = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 9,
		};
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td><Link to={"/member/" + row.member_number}>{row.member_number}</Link></td>
				<td>-</td>
				<td>{row.firstname}</td>
				<td>{row.lastname}</td>
				<td>{row.email}</td>
				<td><DateField date={row.created_at} /></td>
				<td>x</td>
				<td>x</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Kön</th>
				<th>Förnamn</th>
				<th>Efternamn</th>
				<th>E-post</th>
				<th>Blev medlem</th>
				<th>Medlem 2015</th>
				<th>Labbavgift</th>
				<th></th>
			</tr>
		);
	},
});

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

	render: function()
	{
		return (
			<div>
				<h2>{this.state.model.firstname} {this.state.model.lastname} #{this.state.model.member_number}</h2>

				<ul className="uk-tab" data-uk-switcher="{connect:'#my-id'}">
					<li id="member_info"><a href="">Personuppgifter</a></li>
					<li id="member_transactions"><a href="">Transaktioner</a></li>
					<li id="member_keys"><a href="">Nycklar</a></li>
					<li id="member_labaccess"><a href="">Prenumerationer</a></li>
					<li id="member_groups"><a href="">Grupper</a></li>
				</ul>

				<ul id="my-id" className="uk-switcher">
					<li>
						<dl>
							<dt>Medlemsnummer</dt>
							<dd>{this.state.model.member_number}</dd>

							<dt>Förnamn</dt>
							<dd>{this.state.model.firstname}</dd>

							<dt>Efternamn</dt>
							<dd>{this.state.model.lastname}</dd>

							<dt>E-post</dt>
							<dd>{this.state.model.email}</dd>

							<dt>Skapad</dt>
							<dd>{this.state.model.created_at}</dd>

							<dt>Uppdaterad</dt>
							<dd>{this.state.model.updated_at}</dd>

							<dt>Postadress</dt>
							<dd>
								{this.state.model.adress_street}
								{this.state.model.adress_zipcode} {this.state.model.adress_city}
								{this.state.model.adress_country}
							</dd>

							<dt>Personnummer</dt>
							<dd>{this.state.model.civicregno}</dd>

							<dt>Telefonnummer</dt>
							<dd>{this.state.model.phone}</dd>
						</dl>
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
						<MemberKeys type={RfidCollection} />
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

				</ul>
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


module.exports = { MemberHandler, MembersHandler, MemberKeys, MemberAddHandler }