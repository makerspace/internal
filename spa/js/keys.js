
var Keys = React.createClass({
    mixins: [Backbone.React.Component.mixin],

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
        <h1>Key editor</h1>
        <p>Edit keys, denounce foes!</p>
        <table className="uk-table uk-table-striped uk-table-hover">
		    <caption>Editor for keys</caption>
		    <thead>
		        <tr>
		            <th>#</th>
		            <th>Name</th>
		            <th>Expires</th>
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
