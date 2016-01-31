import React from 'react'
import BackboneReact from 'backbone-react-component'
import { MemberModel, MemberCollection } from './models'
import { Link } from 'react-router'
import { BackboneTable } from './BackboneTable'

var MembersHandler = React.createClass({
	getInitialState: function()
	{
		var _this = this;

		var Members = MemberCollection.extend({
			state:
			{
				pageSize: 15 // TODO
			},

			parseState: function(resp, queryParams, state, options)
			{
				// If the paginator is already set up we need to update the parameters and rerender it
				if(typeof _this.pagination != "undefined")
				{
					_this.pagination.pages = resp.last_page;
					_this.pagination.render();
				}

				// Otherwise we just save the parameters to be used when initializing the paginator
				_this.setState({
					totalRecords: resp.total,
					totalPages:   resp.last_page,
					pageSize:     resp.per_page,
				});
			},
		});

		var members = new Members();
		members.fetch();

		return {
			collection: members,
		};
	},

	componentDidMount: function()
	{
		var _this = this;
		window.requestAnimationFrame(function()
		{
			console.log("requestAnimationFrame");
			var node = _this.getDOMNode();
			if(node !== undefined)
			{
				_this.pagination = UIkit.pagination(_this.refs.pag.getDOMNode(), {
					items:       _this.state.totalRecords,
					itemsOnPage: _this.state.pageSize,
				});

				$('.uk-pagination').on('select.uk.pagination', function(e, pageIndex){
					_this.state.collection.getPage(pageIndex + 1);
				});
			}
		});
	},

	render: function()
	{
		return (
			<div>
				<h2>Medlemmar</h2>
				<p>På denna sida ser du en lista på samtliga medlemmar.</p>
				<Members collection={this.state.collection} />
				<ul ref="pag" className="uk-pagination">
					<li className=""><a><i className="uk-icon-angle-double-left"></i></a></li>
				</ul>
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
				<td><Link to={"/member/" + row.member_number}>{row.member_number}</Link></td>
				<td>-</td>
				<td>{row.firstname}</td>
				<td>{row.lastname}</td>
				<td>{row.email}</td>
				<td>{row.created_at}</td>
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

		var _this = this;
		$("[data-uk-switcher]").on("show.uk.switcher", function(event, area) {
			if(area.context.id == "member_keys")
			{
				if(!this.keys_synced)
				{
					// Get the RFID keys associated with the member
					_this.state.model.keys.fetch();
					this.keys_synced = true;
				}
			}
		});
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
						<MemberKeys collection={this.state.model.keys} />
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
			<tr>
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

module.exports = { MemberHandler, MembersHandler, MemberKeys, MemberAddHandler }