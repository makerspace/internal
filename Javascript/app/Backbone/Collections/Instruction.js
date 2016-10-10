import Backbone from 'backbone'
import InstructionModel from '../Models/Account'

var InstructionCollection = Backbone.PageableCollection.extend(
{
	model: InstructionModel,
	url: "/economy/2015/instruction",
});

module.exports = InstructionCollection;