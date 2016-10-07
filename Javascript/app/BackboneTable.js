import React from 'react'
import ReactDOM from 'react-dom'
import { Loading } from './Common'
import config from './config'

/// TODO: This Mixin's should handle auto refresh

/**
 * This is a mixin used with Backbone to provide error handlers.
 */
var BackboneTable = {
	getInitialState: function()
	{
		var _this = this;

		// Extend the collection
		var collection = this.props.type;
		var ExtendedCollection = collection.extend(
		{
			state:
			{
				pageSize: config.pagination.pageSize
			},

			parseRecords: function(resp, options)
			{
				return resp.data;
			},

			parseState: function(resp, queryParams, state, options)
			{
				_this.updatePagination(resp.last_page);

				// Otherwise we just save the parameters to be used when initializing the paginator
				_this.setState({
					totalRecords: resp.total,
					totalPages:   resp.last_page,
					pageSize:     resp.per_page,
				});
			},
		});

		// Create a new extended collection
		// TODO: Does params really work?
		var data = new ExtendedCollection(null, this.props.params);

		this.pagination = [];

		return {
			status: "done",
			collection: data,
		};
	},

	initializePagination: function(i)
	{
		var _this = this;

		// Initialize top and bottom pagination
		for(var i = 1; i <= 2; i++)
		{
			var name = "pag" + i;
			var pag = ReactDOM.findDOMNode(this.refs[name]);

			if(pag !== undefined)
			{
				this.pagination[i] = UIkit.pagination(pag, {
					items:       this.state.totalRecords,
					itemsOnPage: this.state.pageSize,
				});
				$(".uk-pagination").hide();


				$(pag).on("select.uk.pagination", function(e, pageIndex)
				{
					// This one is a little bit ugle as it require the ref="pag1" to be the first attribute
					if(e.target.attributes[0].nodeValue == "pag1")
					{
						var thisPag = _this.pagination[1];
						var otherPag = _this.pagination[2];
					}
					else
					{
						var thisPag = _this.pagination[2];
						var otherPag = _this.pagination[1];
					}

					_this.getCollection().getPage(pageIndex + 1);

					// Update the other paginator
					otherPag.currentPage = thisPag.currentPage;
					otherPag.render(thisPag.pages);
				});
			}
		}
	},

	updatePagination: function(last_page)
	{
		// Initialize top and bottom pagination
		for(var i = 1; i <= 2; i++)
		{
			// If the paginator is already set up we need to update the parameters and rerender it
			if(typeof this.pagination[i] != "undefined")
			{
				// TODO: For some reason Laravel only sends the number of pages on first request
				if(last_page > 1)
				{
					$(".uk-pagination").show();
					this.pagination[i].pages = last_page;
				}

				this.pagination[i].render();
			}
		}
	},

	removeButton: function(i)
	{
		return (
			<a onClick={this.remove.bind(this, i)} className="removebutton uk-icon-hover uk-icon-remove"> Ta bort</a>
		);
	},

	remove: function(row)
	{
		var _this = this;
		var entity = this.getCollection().at(row);
		UIkit.modal.confirm(this.removeTextMessage(entity.attributes), function() {
			entity.destroy({
				wait: true,
				success: function(model, response) {
					if(response.status == "deleted")
					{
//						UIkit.modal.alert("Successfully deleted");
					}
					else
					{
						_this.removeErrorMessage();
					}
				},
				error: function() {
					_this.removeErrorMessage();
				},
			});
		});
		return false;
	},

	componentWillMount: function()
	{
		this.wrapper.setCollections(this.state.collection);

		var _this = this;

		// This event is fired when a request is sent to the server
		this.state.collection.on("request", function()
		{
			console.log("Backbone::request()");
			_this.setState({
				status: "loading"
			});
		});

		// This event is fired after a collection have been received from the server
		this.state.collection.on("sync", function()
		{
			console.log("Backbone::sync()");
			_this.setState({
				status: "done"
			});
		});

		// This event is fired after a model have been successfully deleted.
		this.state.collection.on("destroy", function()
		{
			console.log("Backbone::destroy()");
			_this.setState({
				status: "done"
			});
		});

		// This event is fired when receiveing a collection from the server failed
		this.state.collection.on("error", function(e)
		{
			// If the pending flag is set to false this is probably and delete error or anything else that does not need to update the collection
			if(e._pending === false)
			{
				_this.setState({
					status: "done"
				});
				return;
			}

			_this.setState({
				status: "error"
			});
		});
	},

	componentDidMount: function()
	{
		var _this = this;
		window.requestAnimationFrame(function()
		{
			_this.initializePagination();
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
			<div>
				<ul name="pag1" ref="pag1" className="uk-pagination">
					<li className=""><a><i className="uk-icon-angle-double-left"></i></a></li>
				</ul>
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
				<ul name="pag2" ref="pag2" className="uk-pagination">
					<li className=""><a><i className="uk-icon-angle-double-left"></i></a></li>
				</ul>
			</div>
		);
	},
	tryAgain: function()
	{
		this.getCollection().fetch();
	},
};

module.exports = {
	BackboneTable,
};