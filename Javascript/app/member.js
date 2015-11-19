import React from 'react'
import BackboneReact from 'backbone-react-component'
import { MemberModel, MemberCollection } from './models'
import { Link } from 'react-router'
import { BackboneTable } from './BackboneTable'

var MembersHandler = React.createClass({
	getInitialState: function()
	{
		var members = new MemberCollection();
		members.fetch();

		return {
			collection: members
		};
	},

	render: function()
	{
		return (
			<div>
				<h2>Medlemmar</h2>
				<p>På denna sida ser du en lista på samtliga medlemmar.</p>
				<Members collection={this.state.collection} />
			</div>
		);
	}
});

var MemberHandler = React.createClass({
	getInitialState: function()
	{
		var id = this.props.params.id;
		var member = new MemberModel({id: id});
		member.fetch();

		return {
			model: member
		};
	},

	render: function()
	{
		return <Member model={this.state.model} />;
	}
});

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
			<tr>
				<td><Link to={"/member/" + row.member_id}>{row.member_id}</Link></td>
				<td>-</td>
				<td>{row.firstname}</td>
				<td>{row.lastname}</td>
				<td>{row.email}</td>
				<td>{row.created_at}</td>
				<td>x</td>
				<td>x</td>
				<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
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

	render: function()
	{
		return (
			<div>
				<h2>{this.state.model.firstname} {this.state.model.lastname} #{this.state.model.member_number}</h2>

				<ul className="uk-tab" data-uk-switcher="{connect:'#my-id'}">
					<li><a href="">Personuppgifter</a></li>
					<li><a href="">Transaktioner</a></li>
					<li><a href="">Nycklar</a></li>
					<li><a href="">Prenumerationer</a></li>
					<li><a href="">Grupper</a></li>
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
						<table className="uk-table uk-table-striped uk-table-hover">
							<thead>
								<tr>
									<th>Id</th>
									<th>Startdatum</th>
									<th>Slutdatum</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>000031961352</td>
									<td>2013-11-27 00:00:00</td>
									<td>2016-02-12 23:59:59</td>
									<td className="uk-text-right"><a href="#" className="uk-icon-remove uk-icon-hover"> Ta bort</a> <a href="#" className="uk-icon-cog uk-icon-hover"> Redigera</a></td>
								</tr>
							</tbody>
						</table>
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

module.exports = { MemberHandler, MembersHandler, MemberAddHandler }