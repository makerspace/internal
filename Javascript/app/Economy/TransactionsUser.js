import React from 'react'
import BackboneReact from 'backbone-react-component'
import { BackboneTable } from '../BackboneTable'
import { Link } from 'react-router'
import { Currency, DateField } from '../Common'
import TableDropdownMenu from '../TableDropdownMenu'

var TransactionsUser = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 4,
		};
	},

	componentWillMount: function()
	{
		if(this.props.member_number !== undefined)
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
		}
		else
		{
			// Load all RFID keys
			this.state.collection.fetch();
		}
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>Bokföringsdatum</th>
				<th>Transaktion</th>
				<th className="uk-text-right">Belopp</th>
				<th></th>
			</tr>
		);
	},

	renderRow: function (row, i)
	{
		return (
			<tr key={i}>
				<td><DateField date={row.accounting_date}/></td>
				<td><Link to={"/economy/instruction/" + row.instruction_number}>{row.transaction_title}</Link></td>
				<td className="uk-text-right"><Currency value={row.amount} currency="SEK" /></td>
				<td>
					<TableDropdownMenu>
						<Link to={"/product/" + row.entity_id + "/edit"}><i className="uk-icon uk-icon-cog" /> Redigera metadata</Link>
						<Link to={"/economy/instruction/" + row.instruction_number}><i className="uk-icon uk-icon-cog" /> Visa verifikation</Link>
					</TableDropdownMenu>
				</td>
			</tr>
		);
	},
});

module.exports = {
	TransactionsUser,
}