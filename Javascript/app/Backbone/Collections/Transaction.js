import Backbone from 'backbone'
import TransactionModel from '../Models/Account'

var TransactionCollection = Backbone.PageableCollection.extend(
{
	model: TransactionModel,
	initialize: function(models, options)
	{
		this.id = options.id;
	},

	url: "/economy/2015/transaction",
});

module.exports = TransactionCollection;