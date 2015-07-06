

var Economy = React.createClass({
	getInitialState: function()
	{
		var _this = this;
		var Meep = Backbone.PageableCollection.extend(
		{
			url: "/api/v2/accounting/instructions",
		
			state:
			{
				pageSize: 10
			},
		
			parseState: function(resp, queryParams, state, options)
			{
				_this.setState({
					totalRecords: resp.total,
					totalPages:   resp.last_page,
					pageSize:     resp.per_page,
				});

				return {
					totalRecords: resp.total
				};
			},

			parseRecords: function(resp, options)
			{
				return resp.data;
			}
		});

		this.data = new Meep();
		this.data.fetch(function() {
//			console.log("Loaded");
		});

		return {};
	},

	render: function ()
	{
		var _this = this;
		window.requestAnimationFrame(function()
		{
			var node = _this.getDOMNode();
			if(node !== undefined)
			{
				if(typeof _this.state.totalRecords != 'undefined')
				{
					console.log("recs: " + _this.state.totalRecords);
					console.log("size: " + _this.state.pageSize);

					var pagination = UIkit.pagination($('.uk-pagination'), {
						items:       _this.state.totalRecords,
						itemsOnPage: _this.state.pageSize,
					});
					$('.uk-pagination').on('select.uk.pagination', function(e, pageIndex){
						_this.data.getPage(pageIndex + 1);
					});
				}
			}
		});

		return (
			<div className="uk-width-1-1">
				<h1>Verifikationer</h1>
				<p>Lista över samtliga verifikationer i bokföringen</p>
				<EconomyAccountingInstructionList collection={this.data} />
				<ul className="uk-pagination">
					<li className=""><a><i className="uk-icon-angle-double-left"></i></a></li>
				</ul>
			</div>
		);
	}
});


var EconomyAccountingInstructionList = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	renderRow: function (row, i)
	{
		return (
			<tr key={i}>
				<td>{row.verification_number}</td>
				<td>{row.external_date}</td>
				<td>{row.external_id}</td>
				<td>{row.amount}</td>
				<td>{row.title}</td>
			</tr>
		);
	},

	render: function ()
	{
		return (
			<div>
				<table className="uk-table uk-table-striped uk-table-hover">
					<caption>Verifikationslista</caption>
					<thead>
						<tr>
							<th>#</th>
							<th>Bokföringsdatum</th>
							<th>Extern ID</th>
							<th>Belopp</th>
							<th>Beskrivning</th>
						</tr>
					</thead>
					<tbody>
						{this.state.collection.map(this.renderRow)}
					</tbody>
				</table>
			</div>
		);
	}
});

var Pagination = React.createClass({
	getInitialState: function() {
		return {
		};
	},

	render: function ()
	{
		console.log("Pages:   " + this.state.pages);
		console.log("Current: " + this.state.selected);
		return (
				<ul className="uk-pagination">
					<li className=""><a onClick={this.props.pagerPrev}><i className="uk-icon-angle-double-left"></i></a></li>
					<li><a href="">1</a></li>
					<li className="uk-active"><span>2</span></li>
					<li><span>...</span></li>
					<li className=""><a onClick={this.props.pagerNext}><i className="uk-icon-angle-double-right"></i></a></li>
				</ul>
		);
	},
});