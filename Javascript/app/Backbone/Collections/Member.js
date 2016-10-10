import Backbone from 'backbone'
import MemberModel from '../Models/Account'

var MemberCollection = Backbone.PageableCollection.extend(
{
	model: MemberModel,
	url: "/member",
});

module.exports = MemberCollection;