(function () {
	var Router = ReactRouter;
	var DefaultRoute = Router.DefaultRoute;
	var Link = Router.Link;
	var Route = Router.Route;
	var RouteHandler = Router.RouteHandler;

	var keyHeaders = new Backbone.Model({
		title: "Lab Access",
		headers: ["Member #", "End date", "Description"],
		blurb: "Wherein the keys are edited and henceforth accessed.",
		caption: "Change keys"
	});

	var LabAccessModel = Backbone.Model.extend({
		defaults: {
			member_id: 0,
			date_start: null,
			duration: 0,
			description: ""
		},
		parse: function (response, options) {
			return {
				member_id: response.member_id,
				date_end: new Date(response.date_start.getTime() + duration),
				description: response.description
			}
		}
	});

	var LabAccesses = Backbone.Collection.extend({
		model: LabAccessModel,
		url: "/api/v2/labaccess",
		parse: function (response, options) {
			return response.data;
		}
	});

	var keys = new LabAccesses([
		new LabAccessModel({member_id: 1023, date_end: new Date()}),
		new LabAccessModel({member_id: 1653, date_end: new Date(), description: "Temp"})
	]);

	//keys.fetch();

	var nav = new Backbone.Model({
		brand: "Makerspace Internal v2",
		navItems: [
			{target: '/v1/members/', text: "Members", external: true},
			{target: '/v1/groups/', text: "Groups", external: true},
			{target: 'labaccess', text: "Lab Access"},
			{target: 'economy', text: "Economy"},
			{target: '/v1/members/export/', text: "Export", external: true},
			{target: 'statistics', text: "Statistics"}
		]
	});

	var App = React.createClass({
		render: function () {
			return (
				<div>
					<Nav model={nav}/>
					<SideNav model={nav}/>
					<div className="uk-container uk-container-center uk-margin-top">
						<div className="uk-grid">
							<RouteHandler />
						</div>
					</div>
				</div>
				);
		}
	});

	var LabAccess = React.createClass({
		render: function () {
			return (
					<LabAccessTable model={keyHeaders} collection={keys} />
				);
		}
	});

	var Statistics = React.createClass({
		render: function () {
			return (
				<div className="uk-width-1-1">
					<h1>Heavy stats, bro!</h1>
					<p>Remove the stats you dislike.</p>
				</div>
				);
		}
	});

	var Dashboard = React.createClass({
		render: function () {
			return (
				<div className="uk-width-1-1">
					<div className="uk-panel uk-panel-box uk-panel-box-primary">
						<h1 className="uk-heading-large">Makerspace internal systems thingamajig</h1>
					</div>
				</div>
				);
		}
	});

	var routes = (
		<Route name="app" path="/" handler={App}>
			<Route name="labaccess" handler={LabAccess}/>
			<Route name="economy" handler={Economy}/>
			<Route name="statistics" handler={Statistics}/>
			<DefaultRoute handler={Dashboard}/>
		</Route>
		);

	Router.run(routes, Router.HashLocation, function (Handler) {
		React.render(<Handler />, document.body);
	});
})();