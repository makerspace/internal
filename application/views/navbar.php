<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<?php if($this->Member_model->is_admin()) { ?>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				<?php } else { ?>
					<span class="icon-bar"></span>
				<?php } ?>
			</a>
			
			<a class="brand" href="/">Makerspace Internal</a>

			<div class="nav-collapse collapse">
				<p class="navbar-text pull-right">Logged in as <a href="/members/view/<?php echo member_id(); ?>"><?php echo $this->Member_model->get_member()->email; ?></a> | <a href="/auth/logout">Log out</a></p>
				
				<ul class="nav">
					<?php if($this->Member_model->is_admin()) { ?>
						<li<?php echo menu_active('members'); ?>><a href="/members">Members</a></li>
						<li<?php echo menu_active('newsletter'); ?>><a href="/newsletter">Newsletter</a></li>
						<li<?php echo menu_active('todo'); ?>><a href="/todo">ToDo</a></li>
						<li<?php echo menu_active('admin'); ?>><a href="/admin">Admin</a></li>
						<li<?php echo menu_active('debug'); ?>><a href="/debug">Debug</a></li>
					<?php } else { ?>
						<li><a href="mailto:info@makerspace.se">Contact</a></li>
					<?php } ?>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>
