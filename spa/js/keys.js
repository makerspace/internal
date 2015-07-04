var Keys = React.createClass({
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
		            <th>Color</th>
		        </tr>
		    </thead>
		    <tbody>
		        <tr>
		            <td>1</td>
		            <td>3</td>
		            <td>2</td>
		            <td>1</td>
		        </tr>
		        <tr>
		            <td>1</td>
		            <td>1</td>
		            <td>1</td>
		            <td>1</td>
		        </tr>
		    </tbody>
		</table>
      </div>
    );
  }
});