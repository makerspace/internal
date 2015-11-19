import React from 'react'

var StatisticsHandler = React.createClass({
	render: function () {
		return (
			<div className="uk-width-1-1">
				<h2>Heavy stats, bro!</h2>
				<p>Remove the stats you dislike.</p>
			</div>
		);
	}
});

module.exports = { StatisticsHandler }