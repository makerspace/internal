
var Link = ReactRouter.Link;
var PropTypes = React.PropTypes

var NavItem = React.createClass({
	contextTypes: {
      router: PropTypes.func.isRequired
    },

	getActiveClassName: function () {
		return 'uk-active';
	},

	getActiveState() {
		return this.context.router.isActive(this.props.navItem.target, this.props.params, this.props.query)
	},


	render: function () {
		if (this.props.navItem.external) {
			return (
					<li><a href={this.props.navItem.target}>{this.props.navItem.text}</a></li>
				);
		} else {
			var className = this.getActiveState() ? this.getActiveClassName() : null;
			return (
					<li className={className}>
						<Link activeClassName={this.getActiveClassName()} to={this.props.navItem.target}>
							{this.props.navItem.text}
						</Link>
					</li>
				);
		}
	}
});

var Nav = React.createClass({
    mixins: [Backbone.React.Component.mixin],

	render: function () {
		return (
			<nav className="uk-navbar">
				<div className="uk-container uk-container-center">
				<Link to="app" className="uk-navbar-brand">{this.state.model.brand}</Link>
				<ul className="uk-navbar-nav uk-hidden-small uk-navbar-attached">
					{this.state.model.navItems.map(function (navItem) {
						return (<NavItem navItem={navItem} />);
					})}
				</ul>
					<div className="uk-navbar-flip">
						<a className="uk-navbar-toggle uk-visible-small" data-uk-offcanvas="{target:'#sidenav'}"></a>
					</div>
				</div>
			</nav>
			);
	}
});

var SideNav = React.createClass({
    mixins: [Backbone.React.Component.mixin],
	render: function () {
		return (
			<div id="sidenav" className="uk-offcanvas">
				<div className="uk-offcanvas-bar">
					<ul className="uk-nav uk-nav-offcanvas" data-uk-nav>
						<li><Link to="app">{this.state.model.brand}</Link></li>
						{this.state.model.navItems.map(function (navItem) {
							return (<NavItem navItem={navItem} />);
						})}
					</ul>
				</div>
			</div>
			);
	}
})