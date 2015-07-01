$(function() {
	var CommentBox = React.createClass({
		render: function() {
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

	React.render(<CommentBox />, document.getElementById('content'));
});