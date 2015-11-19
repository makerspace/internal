import React from 'react'
import { Link } from 'react-router'
import BackboneReact from 'backbone-react-component'

	function findActiveRoute(this2, input, matchLevel, depth)
	{
//		console.log("findActiveRoute({}, [], " + matchLevel +  ", " + depth + ")");
		/*
		if(typeof returnFirst == 'undefined')
		{
			returnFirst = false;
		}
		*/

		if(typeof depth == 'undefined')
		{
			depth = 0;
		}

		var activeItem;

		for(var i = 0; i < input.length; i++)
		{
			var navItem = input[i];
//			console.log("Checking " + navItem.text + " depth " + depth);

			// Check link
			if(this2.context.history.isActive(navItem.target, this2.props.query))
			{
//				console.log("Found match at level " + matchLevel + " depth " + depth);
				return navItem;
			}

			// Recursive check children
			if(typeof navItem.children != 'undefined')
			{
				var x = findActiveRoute(this2, navItem.children, false, depth+1);
				if(x !== false)
				{
//					console.log("Match2");
					// If returnFirst is set, we should return the element at the first level in the hierarchy (The active element in the top menu bar)
					if(depth == matchLevel)
//					if(returnFirst)
					{
						return navItem;
					}
					// If returnFirst is not set, we should return the matching element
					else
					{
//						if(depth == 1)
						{
							return x;
						}
					}
				}
			}
		}

		// We did not find anything
		return false;
	}

var NavItem = React.createClass({
	contextTypes: {
		history: React.PropTypes.object
	},

	getActiveClassName: function()
	{
		return "uk-active";
	},

	getActiveState()
	{
		var item = this.props.navItem;

//		console.log("NavItem");
		if(this.context.history.isActive(this.props.navItem.target, this.props.query))
		{
//			console.log("Is active:" + this.props.navItem.target);
			return true;
		}
		return false;
/*
		var activeItem = findActiveRoute(this, this.props.navItem.children, false, 2);
		console.log("activeItem.target = " + activeItem.target);
		console.log("this.props.navItem.target = " + this.props.navItem.target);

		if(activeItem.target == this.props.navItem.target)
		{
			return true;
		}
		else
		{
			return false;
		}
*/
		/*
		if(typeof activeItem.target != "undefined")
		{
			return this.context.history.isActive(activeItem.target, this.props.query)
		}
		else
		{
			return false;
		}
		*/

//		return this.context.history.isActive(item.target, this.props.query)
	},

	render: function()
	{
		if(this.props.navItem.external)
		{
			return (
				<li>
					<a href={this.props.navItem.target}>{this.props.navItem.text}</a>
				</li>
			);
		}
		else
		{
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

	render: function ()
	{
		return (
			<nav className="uk-navbar">
				<div className="uk-container uk-container-center">
					<Link to="/" className="uk-navbar-brand">{this.state.model.brand}</Link>
					<ul className="uk-navbar-nav uk-hidden-small uk-navbar-attached">
						{this.state.model.navItems.map(function (navItem, i) {
							return (<NavItem navItem={navItem} key={i} />);
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

	render: function ()
	{
		return (
			<div id="sidenav" className="uk-offcanvas">
				<div className="uk-offcanvas-bar">
					<ul className="uk-nav uk-nav-offcanvas" data-uk-nav>
						<li><Link to="/">{this.state.model.brand}</Link></li>
						{this.state.model.navItems.map(function (navItem, i) {
							return (<NavItem navItem={navItem} key={i} />);
						})}
					</ul>
				</div>
			</div>
		);
	}
})

var SideNav2 = React.createClass({
	mixins: [Backbone.React.Component.mixin],

	contextTypes: {
		history: React.PropTypes.object
	},

	render: function ()
	{
		var activeItem = findActiveRoute(this, this.state.model.navItems, 0);

//		console.log(activeItem);

		// There is no active menu, or children.
		if(activeItem === null || typeof activeItem.children == 'undefined')
		{
//			return (<p>{activeItem.text}</p>);
			return (
				<div className="uk-panel uk-panel-box" data-uk-sticky="{top:35}">
					<ul className="uk-nav uk-nav-side" data-uk-scrollspy-nav="{closest:'li', smoothscroll:true}">
						<li className="uk-nav-header">{activeItem.text}</li>
						<li className="uk-nav-divider"></li>
					</ul>
				</div>
			);
		}
		else
		{
			return (
				<div className="uk-panel uk-panel-box" data-uk-sticky="{top:35}">
					<ul className="uk-nav uk-nav-side" data-uk-scrollspy-nav="{closest:'li', smoothscroll:true}">
						<li className="uk-nav-header">{activeItem.text}</li>
						<li className="uk-nav-divider"></li>
						{activeItem.children.map(function (navItem, i) {
							if(typeof navItem.type != "undefined" && navItem.type == "separator")
							{
								return (<li key={i} className="uk-nav-divider"></li>);
							}
							else if(typeof navItem.type != "undefined" && navItem.type == "heading")
							{
								return (<li key={i} className="uk-nav-header">{navItem.text}</li>);
							}
							else
							{
								return (<NavItem key={i} navItem={navItem} activeItem={activeItem} />);
							}
						})}
					</ul>
				</div>
			);
		}
	},
});

module.exports = { Nav, SideNav, SideNav2 }