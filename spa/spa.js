var Router = ReactRouter;
var DefaultRoute = Router.DefaultRoute;
var Link = Router.Link;
var Route = Router.Route;
var RouteHandler = Router.RouteHandler;


var App = React.createClass({
  render: function () {
    return (
      <div className="uk-container uk-container-center uk-margin-top">
        <nav className="uk-navbar">
          <Link to="app" className="uk-navbar-brand">Makerspace Internal</Link>
          <ul className="uk-navbar-nav">
            <li><Link to="members">Members</Link></li>
            <li><Link to="keys">Keys</Link></li>
          </ul>
        </nav>
        <div className="uk-grid">
          <RouteHandler/>
        </div>
      </div>
    );
  }
});

var Members = React.createClass({
  render: function () {
    return (
      <div className="uk-width-1-1">
        <h1>Member editor</h1>
        <p>Remove the members you dislike.</p>
      </div>
    );
  }
});

var Keys = React.createClass({
  render: function () {
    return (
      <div className="uk-width-1-1">
        <h1>Key editor</h1>
        <p>Edit keys, denounce foes!</p>
      </div>
    );
  }
});

var Dashboard = React.createClass({
  render: function () {
    return (
      <div className="uk-width-1-1">
        <div className="uk-panel uk-panel-box">
        	<h1 className="uk-heading-large">Makerspace internal systems thingamajig</h1>
        </div>
      </div>
    );
  }
});

var routes = (
  <Route name="app" path="/" handler={App}>
    <Route name="members" handler={Members}/>
    <Route name="keys" handler={Keys}/>
    <DefaultRoute handler={Dashboard}/>
  </Route>
);


Router.run(routes, Router.HashLocation, function (Handler) {
  React.render(<Handler/>, document.body);
});