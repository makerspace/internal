import Backbone from 'backbone'

var InvoiceModel = Backbone.Model.fullExtend(
{
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

module.exports = InvoiceModel;