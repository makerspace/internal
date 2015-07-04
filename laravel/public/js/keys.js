
var Keys = React.createClass({
    mixins: [Backbone.React.Component.mixin],

    renderHeader: function (header, i) {
    	return (
    		<th key={i}>{header}</th>
    	);
    },

	renderRow: function (row, i) {
		return (
	        <tr key={i}>
	            <td>{row.key}</td>
	            <td>{row.name}</td>
	            <td>{row.expires.toJSON()}</td>
	        </tr>
		);
	},

  render: function () {
    return (
      <div className="uk-width-1-1">
        <h1>{this.state.model.title}</h1>
        <p>{this.state.model.blurb}</p>
        <table className="uk-table uk-table-striped uk-table-hover">
		    <caption>{this.state.model.caption}</caption>
		    <thead>
		        <tr>
		        	{this.state.model.headers.map(this.renderHeader)}
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
