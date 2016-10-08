import Backbone from 'backbone'
import PageableCollection from 'backbone.paginator'
import auth from './auth'
import config from './config'

// Update the Backbone sync() method to work with our RESTful API with OAuth 2.0 authentication
var backboneSync = Backbone.sync;
Backbone.sync = function(method, model, options)
{
	// Include the OAuth 2.0 access token
	options.headers = {
		"Authorization": "Bearer " + auth.getAccessToken()// + "meep"
	};

	// Base path for API access
	options.url = config.apiBasePath + (typeof model.url == "function" ? model.url() : model.url);

	// Add generic error handling to those models who doesn't implement own error handling
	if(!options.error)
	{
		options.error = function(data, xhr, options)
		{
			if(xhr.status == 401)
			{
				UIkit.modal.alert("<h2>Error</h2>You are unauthorized to use this API resource. This could be because one of the following reasons:<br><br>1) You have been logged out from the API<br>2) You do not have permissions to access this resource");
			}
			else
			{
				UIkit.modal.alert("<h2>Error</h2>Received an unexpected result from the server<br><br>" + data.status + " " + data.statusText + "<br><br>" + data.responseText);
			}
		}
	};

	// Call the stored original Backbone.sync method with extra headers argument added
	backboneSync(method, model, options);
};

Backbone.Model.fullExtend = function (protoProps, staticProps)
{
	if(typeof protoProps === "undefined")
	{
		var protoProps = [];
	}

	// TODO: Override the set() method so when React inputs a "" it is casted to NULL

	// Preprocess the data received from the API and make sure all null's are changed to empty strings, because otherwise React will be whining when using those values in a <input value={...} />
	protoProps["parse"] = function(response, options)
	{
		for(var key in response)
		{
			if(response.hasOwnProperty(key) && response[key] === null)
			{
				response[key] = "";
			}
		}

		return response;
	};


	// Call default extend method
	var extended = Backbone.Model.extend.call(this, protoProps, staticProps);

	return extended;
};

var InstructionModel = Backbone.Model.fullExtend({
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

var InstructionCollection = Backbone.PageableCollection.extend(
{
	model: InstructionModel,
	url: "/economy/2015/instruction",//?account_id=2999
});

var TransactionModel = Backbone.Model.fullExtend({
	urlRoot: "/economy/2015/transaction",
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
//		return "/economy/2015/transaction/" + this.id;
		return "/economy/2015/transaction";
	},
});

var CostCenterModel = Backbone.Model.fullExtend({
	urlRoot: "/economy/2015/costcenter",

	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
	},
});

var CostCenterCollection = Backbone.PageableCollection.extend(
{
	model: CostCenterModel,
	url: "/economy/2015/costcenter",
});

var AccountModel = Backbone.Model.fullExtend({
	urlRoot: "/economy/2015/account",
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
	url: "/economy/2015/account",
});

var MasterledgerCollection = Backbone.PageableCollection.extend({
	model: AccountModel,
	url: "/economy/2015/masterledger",
});

var InvoiceModel = Backbone.Model.fullExtend({
	urlRoot: "/economy/2015/invoice",
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
	url: "/economy/2015/invoice",
});

var SubscriptionModel = Backbone.Model.fullExtend({
//	idAttribute: "subscription_id",
	urlRoot: "/subscription",
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
	url: "/subscription",
});

var RfidModel = Backbone.Model.fullExtend({
	idAttribute: "entity_id",
	urlRoot: "/rfid",
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
	url: "/rfid",
});

var MemberModel = Backbone.Model.fullExtend({
	idAttribute: "member_number",
	urlRoot: "/member",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		entity_id: 0,
		member_number: 0,
		civicregno: "000000-0000",
		firstname: "",
		lastname: "",
		email: "",
		phone: "",
		address_street: "",
		address_extra: "",
		address_zipcode: "",
		address_city: "",
		address_country: "",
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
	url: "/member",
});

var GroupModel = Backbone.Model.fullExtend({
	idAttribute: "entity_id",
	urlRoot: "/group",
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
	url: "/group",
});

var ProductModel = Backbone.Model.fullExtend({
	idAttribute: "entity_id",
	urlRoot: "/product",
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
	url: "/product",
});

var MailModel = Backbone.Model.fullExtend({
	idAttribute: "entity_id",
	urlRoot: "/mail",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		type: "",
		recipient: "",
		title: "",
		description: "",
		status: 0,
		date_sent: "0000-00-00T00:00:00Z",
	},
});

var MailCollection = Backbone.PageableCollection.extend({
	model: MailModel,
	url: "/mail",
});

var SalesHistoryModel = Backbone.Model.fullExtend({
	idAttribute: "entity_id",
	urlRoot: "/sales/history",
	defaults: {
		created_at: "0000-00-00T00:00:00Z",
		updated_at: "0000-00-00T00:00:00Z",
		recipient: "",
		title: "",
		description: "",
	},
});

var SalesHistoryCollection = Backbone.PageableCollection.extend({
	model: SalesHistoryModel,
	url: "/sales/history",
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
	MailModel,
	MailCollection,
	SalesHistoryModel,
	SalesHistoryCollection,
}