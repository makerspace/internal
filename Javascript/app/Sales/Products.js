import React from 'react'

import {
	ProductModel,
	ProductCollection
} from '../models'
import { Link } from 'react-router'
import {
	BackboneTable,
} from '../BackboneTable'
import { DateField, Currency } from '../Common'

var SalesProductsHandler = React.createClass({
	render: function()
	{
		return (
			<div>
				<h2>Produkter</h2>
				<p>På denna sida ser du en lista på samtliga produkter som finns för försäljning.</p>
				<Products type={ProductCollection}/>
				<Link className="uk-button" to="/product/add"><i className="uk-icon-plus"></i> Skapa ny produkt</Link>
			</div>
		);
	},
});

var Products = React.createClass({
	mixins: [Backbone.React.Component.mixin, BackboneTable],

	getInitialState: function()
	{
		return {
			columns: 9,
		};
	},

	removeTextMessage: function(entity)
	{
		return "Are you sure you want to remove product \"" + entity.title + "\"?";
	},

	removeErrorMessage: function()
	{
		UIkit.modal.alert("Error deleting product");
	},

	renderRow: function(row, i)
	{
		return (
			<tr key={i}>
				<td>{row.entity_id}</td>
				<td><Link to={"/product/" + row.entity_id}>{row.title}</Link></td>
				<td>{row.expiry_date}</td>
				<td>{row.auto_extend}</td>
				<td>{row.interval}</td>
				<td><Currency value={row.price} /></td>
				<td className="uk-text-right">{this.removeButton(i)}</td>
			</tr>
		);
	},

	renderHeader: function()
	{
		return (
			<tr>
				<th>#</th>
				<th>Namn</th>
				<th>Giltig till</th>
				<th>Prenumeration</th>
				<th>Giltighetstid</th>
				<th>Pris</th>
				<th></th>
			</tr>
		);
	},
});

module.exports = {
	SalesProductsHandler,
}