import Backbone from 'backbone'
import PageableCollection from 'backbone.paginator'

var InstructionModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/instruction",

	defaults: {
		instruction_number: 0,
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		accounting_date: "0000-00-00 00:00:00",
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

var InstructionCollection = PageableCollection.extend(
{
	model: InstructionModel,
	url: "/api/v2/economy/2015/instruction",//?account_id=2999
});

var TransactionModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/transaction",

	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		transaction_title: "",
		transaction_description: "",
		accounting_instruction: "",
		accounting_account: "",
		accounting_cost_center: "",
		amount: 0,
		external_id: "",
		instruction_title: "",
		instruction_number: 0,
		accounting_date: "0000-00-00 00:00:00",
		extid: 0,
		balance: 0,
	},
});

var TransactionCollection = Backbone.PageableCollection.extend(
{
	model: TransactionModel,
	initialize: function(models, options)
	{
		this.id = options.id;
	},

	url: function()
	{
		return "/api/v2/economy/2015/transaction/" + this.id;
	},//?account_id=1930",//TODO

//	urlRoot: "/api/v2/economy/2015/instruction",//?account_id=2999
});

var CostCenterModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/costcenter",

	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
	},
});

var CostCenterCollection = PageableCollection.extend(
{
	url: "/api/v2/economy/2015/costcenter",

/*	
	parseRecords: function(resp, options)
	{
		return resp.data;
	}
*/
});

var AccountModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/account",

	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		account_number: "",
		title: "",
		description: "",
		balance: 0,
		accounting_transaction: [],
		instructions: [],
	},
});

var AccountCollection = Backbone.PageableCollection.extend({
	model: AccountModel,
	url: "/api/v2/economy/2015/account",
});

var MasterledgerCollection = Backbone.PageableCollection.extend({
	model: AccountModel,
	url: "/api/v2/economy/2015/masterledger",
});

var InvoiceModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/invoice",

	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		invoice_number: 0,
		title: "",
		description: "",
		your_reference: "",
		our_reference: "",
		address: "",
		posts: [],
	},
});

var InvoiceCollection = Backbone.PageableCollection.extend({
	model: InvoiceModel,
	url: "/api/v2/economy/2015/invoice",
});

var LabAccessModel = Backbone.Model.extend({
	defaults: {
		member_id: 0,
		date_start: null,
		duration: 0,
		description: ""
	},
	parse: function (response, options) {
		return {
			member_id: response.member_id,
			date_end: new Date(response.date_start.getTime() + duration),
			description: response.description
		}
	}
});

var LabAccessCollection = Backbone.PageableCollection.extend({
	model: LabAccessModel,
	url: "/api/v2/labaccess",
	/*
	parse: function (response, options) {
		return response.data;
	}
	*/
});

var RfidModel = Backbone.Model.extend({
	urlRoot: "/api/v2/member",
	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		tagid: null,
		active: 1,
		title: "",
		description: "",
	},
});

var RfidCollection = PageableCollection.extend({
	model: RfidModel,
	url: "/api/v2/rfid",
	/*
	parseRecords: function(resp, options)
	{
		return resp.data;
	}
	*/
	
});

var MemberModel = Backbone.Model.extend({
	urlRoot: "/api/v2/member",
	defaults: {
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		member_id: 0,
		member_number: null,
		firstname: "",
		lastname: "",
		email: "",
		keys: new RfidCollection(),
	},
	initialize: function(options)
	{
		// TODO
		if(false)
		{
			this.attributes.keys.fetch();
		}
	},
});

var MemberCollection = PageableCollection.extend({
	model: MemberModel,
	url: "/api/v2/member",
});

module.exports = {
	InstructionModel,
	InstructionCollection,
	AccountModel,
	AccountCollection,
	MasterledgerCollection,
	InvoiceModel,
	InvoiceCollection,
	LabAccessModel,
	LabAccessCollection,
	MemberModel,
	MemberCollection,
	CostCenterModel,
	CostCenterCollection,
	TransactionModel,
	TransactionCollection,
}