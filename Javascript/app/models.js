import Backbone from 'backbone'
import PageableCollection from 'backbone.paginator'

var InstructionModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/instruction",

	defaults: {
		verification_number: 0,
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		accounting_date: "0000-00-00 00:00:00",
		external_id: "",
		importer: "",
		description: "",
		amount: 0,
		transactions: []
	},
});

var InstructionCollection = PageableCollection.extend(
{
	url: "/api/v2/economy/2015/instruction",//?account_id=2999

	parseRecords: function(resp, options)
	{
		return resp.data;
	}
});

var TransactionCollection = Backbone.Collection.extend(
{
	initialize: function(models, options)
	{
		this.id = options.id;
	},

	url: function()
	{
		return "/api/v2/economy/2015/transaction/" + this.id;
	},//?account_id=1930",//TODO
});

var CostCenterModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/costcenter",

	defaults: {
//		verification_number: 0,
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
		/*
		accounting_date: "0000-00-00 00:00:00",
		external_id: "",
		importer: "",
		description: "",
		amount: 0,
		posts: []
		*/
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
		account_number: null,
		title: "",
		description: "",
		accounting_transaction: [],
		instructions: [],
	},
});

var AccountCollection = Backbone.Collection.extend({
	model: AccountModel,
	url: "/api/v2/economy/2015/account",
});

var MasterledgerCollection = Backbone.Collection.extend({
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

var InvoiceCollection = Backbone.Collection.extend({
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

var LabAccessCollection = Backbone.Collection.extend({
	model: LabAccessModel,
	url: "/api/v2/labaccess",
	parse: function (response, options) {
		return response.data;
	}
});

var MemberModel = Backbone.Model.extend({
	urlRoot: "/api/v2/member",
	defaults: {
		member_id: 0,
		member_number: null,
		firstname: "",
		lastname: "",
		email: "",
		created_at: "0000-00-00 00:00:00",
		updated_at: "0000-00-00 00:00:00",
	},
});

var MemberCollection = Backbone.Collection.extend({
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
	TransactionCollection,
}
