var app = app || {};

$(function() {
//	import { Router, Route, Link } from 'react-router';
//	var Router = require('react-router');
//	var Route = Router.Route;
//	app.SHOW_USERS = 'users';
//	app.SHOW_ECONOMY = 'economy';
var Router = ReactRouter.Router;
var Route = ReactRouter.Route;
//var history = ReactRouter;
var history = ReactRouter.HashHistory.History;
console.log(history);

//	import { Router, Route } from 'react-router';

	var EconomyTransaction = Backbone.Model.extend({
		url : function() {
			var base = '/api/v2/economy/transaction';
			/*
			if (this.isNew())
			{
				return base;
			}
			else
			{
				return base + (base.charAt(base.length - 1) == '/' ? '' : '/') + this.id;
			}
			*/
		},
	});

	var EconomyTransactionList = Backbone.Collection.extend({
		model: EconomyTransaction,

/*
		// Filter down the list of all todo items that are finished.
		done: function() {
		  return this.where({done: true});
		},

		// Filter down the list to only todo items that are still not finished.
		remaining: function() {
		  return this.where({done: false});
		},

		// We keep the Todos in sequential order, despite being saved by unordered
		// GUID in the database. This generates the next order number for new items.
		nextOrder: function() {
		  if (!this.length) return 1;
		  return this.last().get('order') + 1;
		},

		// Todos are sorted by their original insertion order.
		comparator: 'order'
*/
	});

	var App = React.createClass({
		componentDidMount: function ()
		{
			/*
			var setState = this.setState;
			var router = Router(
			{
				'/':        setState.bind(this, {nowShowing: app.SHOW_HOME}),
				'/economy': setState.bind(this, {nowShowing: app.SHOW_ECONOMY}),
				'/users':   setState.bind(this, {nowShowing: app.SHOW_USERS})
			});
			router.init('/');
			*/
		},

		render: function()
		{
			return (
				<Router history={history} location="/">
					<Route path="/" component={Home}>
						<Route path="/economy" component={Economy}/>
					</Route>
					<Route path="*" component={NoMatch}/>
				</Router>
			);
		},
	});

	var Home = React.createClass({
		render: function()
		{
			return (<p>Home</p>);
		},
	});

	var Economy = React.createClass({
		render: function()
		{
			return (<p>Economy</p>);
		},
	});

	var NoMatch = React.createClass({
		render: function()
		{
			return (<p>Invalid URL</p>);
		},
	});

	var Header = React.createClass({
		render: function()
		{
			return (
				<div className2="uk-container uk-container-center uk-margin-large-bottom">
					<nav className="uk-navbar uk-margin-large-bottom">
						<a className="uk-navbar-brand uk-hidden-small" href="/">Makerspace Internal 2.0</a>
						<ul className="uk-navbar-nav uk-hidden-small">
							<li className="uk-active">
								<a href="/">Frontpage</a>
							</li>
							<li>
								<a href="/members/">Members</a>
							</li>
							<li>
								<a href="/groups/">Groups</a>
							</li>
							<li>
								<a href="/finance/">Finance</a>
							</li>
							<li>
								<a href="/v1/members/export/">Export</a>
							</li>
							<li>
								<a href="/auth/logout">Log out</a>
							</li>
						</ul>
						<a href="#offcanvas" className="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
						<div className="uk-navbar-brand uk-navbar-center uk-visible-small">Brand</div>
					</nav>

					<div id="offcanvas" class="uk-offcanvas">
						<div class="uk-offcanvas-bar">
							<ul class="uk-nav uk-nav-offcanvas">
								<li class="uk-active">
									<a href="layouts_frontpage.html">Frontpage</a>
								</li>
								<li>
									<a href="layouts_portfolio.html">Portfolio</a>
								</li>
								<li>
									<a href="layouts_blog.html">Blog</a>
								</li>
								<li>
									<a href="layouts_documentation.html">Documentation</a>
								</li>
								<li>
									<a href="layouts_contact.html">Contact</a>
								</li>
								<li>
									<a href="layouts_login.html">Login</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			);
		}
	});

	React.render((
		<App/>
	), document.body);
});