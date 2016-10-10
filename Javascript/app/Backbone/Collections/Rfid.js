import Backbone from 'backbone'
import RfidModel from '../Models/Account'

var RfidCollection = Backbone.PageableCollection.extend(
{
	model: RfidModel,
	url: "/rfid",
});

module.exports = RfidCollection;