import React from 'react'
import ReactDOM from 'react-dom'
import { Loading } from './Common'

/// TODO: This Mixin's should handle auto refresh

/**
 * This is a mixin used with Backbone to provide error handlers.
 */
var BackboneTable = {
	getInitialState: function()
	{
		return {
			status: "loading",
		};
	},

	componentWillMount: function()
	{
		var _this = this;
		this.getCollection().on("request", function()
		{
			_this.setState({
				status: "loading"
			});
		});

		this.getCollection().on("sync", function()
		{
			_this.setState({
				status: "done"
			});
		});

		this.getCollection().on("error", function()
		{
			_this.setState({
				status: "error"
			});
		});
	},

	render: function ()
	{
		if(this.state.status == "loading")
		{
			var loading = (
				<div className="loadingOverlay">
					<div className="loadingWrapper">
						<Loading />
					</div>
				</div>
			);
			var loadingClass = " backboneTableLoading";
		}

		if(this.state.status == "error")
		{
			var content = (
				<tr key="0">
					<td colSpan={this.state.columns} className="uk-text-center">
						<p>
							<em>Hämtning av data misslyckades.</em>&nbsp;&nbsp;<button className="uk-button uk-button-primary uk-button-mini" onClick={this.tryAgain}><i className="uk-icon-refresh"></i> Försök igen</button>
						</p>
					</td>
				</tr>
			);
		}
		else if(this.state.collection.length == 0)
		{
			var content = (
				<tr key="0">
					<td colSpan={this.state.columns} className="uk-text-center">
						<em>Listan är tom</em>
					</td>
				</tr>
			);
		}
		else
		{
			var content = this.state.collection.map(this.renderRow);
		}

		return (
			<div style={{position: "relative"}}>
				<table className={"uk-table uk-table-condensed uk-table-striped uk-table-hover" + loadingClass}>
					<thead>
						{this.renderHeader()}
					</thead>
					<tbody>
						{content}
					</tbody>
				</table>
				{loading}
			</div>
		);
	},
	tryAgain: function()
	{
		this.getCollection().fetch();
	},
};

var PaginatedDataTable = React.createClass({
	createPaginatedCollection: function(collection, options)
	{
		if(typeof options == "undefined")
		{
			options = {};
		}

		var _this = this;

		var ExtendedCollection = collection.extend({
			state:
			{
				pageSize: 10 // TODO
			},

			parseRecords: function(resp, options)
			{
				return resp.data;
			},

			parseState: function(resp, queryParams, state, options)
			{
				// If the paginator is already set up we need to update the parameters and rerender it
				if(typeof _this.pagination != "undefined")
				{
					// TODO: For some reason Laravel only sends the number of pages on first request
					if(resp.last_page > 0)
					{
						_this.pagination.pages = resp.last_page;
					}

					_this.pagination.render();
				}

				// Otherwise we just save the parameters to be used when initializing the paginator
				_this.setState({
					totalRecords: resp.total,
					totalPages:   resp.last_page,
					pageSize:     resp.per_page,
				});
			},
		});

		var data = new ExtendedCollection(null, options);
		data.fetch();

		return data;
	},

	componentDidMount: function()
	{
		var _this = this;
		window.requestAnimationFrame(function()
		{
			var node = ReactDOM.findDOMNode(_this.refs.pag);
			if(node !== undefined)
			{
				_this.pagination = UIkit.pagination(node, {
//					items:       _this.state.totalRecords,
//					itemsOnPage: _this.state.pageSize,
				});

				$('.uk-pagination').on('select.uk.pagination', function(e, pageIndex){
					_this.state.collection.getPage(pageIndex + 1);
				});
			}
		});
	},

	renderPaginator: function()
	{
		return (
			<ul ref="pag" className="uk-pagination">
				<li className=""><a><i className="uk-icon-angle-double-left"></i></a></li>
			</ul>
		);
	},

	render: function()
	{
		return (
			<div>
				<p>You should extend this class and overide the render() method.</p>
			</div>
		);
	}
});

module.exports = {
	BackboneTable,
	PaginatedDataTable,
};
