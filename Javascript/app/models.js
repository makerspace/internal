import Backbone from 'backbone'
import PageableCollection from 'backbone.paginator'

var InstructionModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/instruction",
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

var InstructionCollection = Backbone.PageableCollection.extend(
{
	model: InstructionModel,
	url: "/api/v2/economy/2015/instruction",//?account_id=2999
});

var TransactionModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/transaction",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		transaction_title: "",
		transaction_description: "",
		accounting_instruction: "",
		accounting_account: "",
		accounting_cost_center: "",
		amount: 0,
		external_id: "",
		instruction_title: "",
		instruction_number: 0,
		accounting_date: "0000-00-00T00:00:00Z",
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
	},
});

var CostCenterModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/costcenter",

	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
	},
});

var CostCenterCollection = Backbone.PageableCollection.extend(
{
	model: CostCenterModel,
	url: "/api/v2/economy/2015/costcenter",
});

var AccountModel = Backbone.Model.extend({
	urlRoot: "/api/v2/economy/2015/account",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
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
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
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

var SubscriptionModel = Backbone.Model.extend({
//	idAttribute: "subscription_id",
	urlRoot: "/api/v2/subscription",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		title: "",
		member_id: 0,
		product_id: 0,
		date_start: "0000-00-00T00:00:00Z",
	},
/*
	parse: function (response, options) {
		return {
			member_id: response.member_id,
			date_end: new Date(response.date_start.getTime() + duration),
			description: response.description
		}
	}
*/
});

var SubscriptionCollection = Backbone.PageableCollection.extend({
	model: SubscriptionModel,
	url: "/api/v2/subscription",
});

var RfidModel = Backbone.Model.extend({
	urlRoot: "/api/v2/member",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		tagid: "",
		active: 1,
		title: "",
		description: "",
	},
});

var RfidCollection = Backbone.PageableCollection.extend({
	model: RfidModel,
	url: "/api/v2/rfid",
});

var MemberModel = Backbone.Model.extend({
	urlRoot: "/api/v2/member",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		member_id: 0,
		member_number: 0,
		civicregno: "000000-0000",
		firstname: "",
		lastname: "",
		email: "",
		phone: "",
		adress_street: "",
		adress_zipcode: "",
		adress_city: "",
		adress_country: "",
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

var MemberCollection = Backbone.PageableCollection.extend({
	model: MemberModel,
	url: "/api/v2/member",
});

var GroupModel = Backbone.Model.extend({
	idAttribute: "group_id",
	urlRoot: "/api/v2/group",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
//		group_id: 0,
		title: "",
		description: "",
	},
});

var GroupCollection = Backbone.PageableCollection.extend({
	model: GroupModel,
	url: "/api/v2/group",
});

var ProductModel = Backbone.Model.extend({
	idAttribute: "product_id",
	urlRoot: "/api/v2/product",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
//		group_id: 0,
		title: "",
		description: "",
	},
});

var ProductCollection = Backbone.PageableCollection.extend({
	model: ProductModel,
	url: "/api/v2/product",
});

module.exports = {
	InstructionModel,
	InstructionCollection,
	AccountModel,
	AccountCollection,
	MasterledgerCollection,
	InvoiceModel,
	InvoiceCollection,
	SubscriptionModel,
	SubscriptionCollection,
	MemberModel,
	MemberCollection,
	GroupModel,
	GroupCollection,
	CostCenterModel,
	CostCenterCollection,
	TransactionModel,
	TransactionCollection,
	RfidModel,
	RfidCollection,
	ProductModel,
	ProductCollection,
}