import Backbone from 'backbone'
import SubscriptionModel from '../Models/Account'

var SubscriptionCollection = Backbone.PageableCollection.extend(
{
	model: SubscriptionModel,
	url: "/subscription",
});

module.exports = SubscriptionCollection;