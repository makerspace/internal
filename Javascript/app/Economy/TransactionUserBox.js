import React from 'react'

import { TransactionCollection } from '../models'
import { TransactionsUser } from './TransactionsUser'

var TransactionUserBox = React.createClass(
{
	render: function()
	{
		return (
			<div>
				<TransactionsUser type={TransactionCollection} member_number={this.props.member_number} />
			</div>
		);
	},
});

module.exports = {
	TransactionUserBox
}