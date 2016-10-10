import Backbone from 'backbone'
import SalesHistoryModel from '../Models/Account'

var SalesHistoryCollection = Backbone.PageableCollection.extend(
{
	model: SalesHistoryModel,
	url: "/sales/history",
});

module.exports = SalesHistoryCollection;