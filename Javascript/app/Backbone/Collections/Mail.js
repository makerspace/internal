import Backbone from 'backbone'
import MailModel from '../Models/Account'

var MailCollection = Backbone.PageableCollection.extend(
{
	model: MailModel,
	url: "/mail",
});

module.exports = MailCollection;