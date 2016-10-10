import Backbone from 'backbone'

var InstructionModel = Backbone.Model.fullExtend(
{
	urlRoot: "/economy/2015/instruction",
	defaults: {
		instruction_number: 0,
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		accounting_date: "0000-00-00T00:00:00Z",
		importer: "",
		external_id: "",
		external_date: "",
		external_data: "",
		description: "",
		transactions: [],
		files: [],
		balance: 0,
	},
});

module.exports = InstructionModel;