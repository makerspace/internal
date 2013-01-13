<div class="row">
	<div class="span12">
		<h2 class="pull-left">View member</h2>
		<a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary pull-right">Update information</a>
	</div>
	
	<div class="span4">
		<h4>Quick links</h4>
		<p>
			<a href="#" class="btn btn-info">Ge lokalaccess</a> 
			<a href="#" class="btn btn-primary">Gör till admin</a>
		</p>
		
		<p>
			<a href="#" class="btn btn-primary">Skicka betalningsinfo</a> 
		</p>
		
		<p>
			<a href="#" class="btn btn-inverse">Ladda ner medlemskort i PDF</a> 
		</p>
		
		<br>
		<h4>Access list (ACL) <small>Click to change</small></h4>
		
		<?php foreach($this->dbconfig->acl as $acl) { ?>
			<a href="/members/acl_switch/<?php echo $member->id; ?>/<?php echo $acl; ?>" class="label<?php echo ($member->{$acl} ? ' label-success' : ''); ?>">
				<?php echo ucfirst($acl); ?>
			</a>
		<?php } ?>
	</div>

	<div class="span8">
		<h4>Profile</h4>
		
		<div class="row">
			<div class="span2">
				<img src="<?php echo gravatar($member->email, 160); ?>" class="img-polaroid">
			</div>
			
			<div class="span3">
				<p>
					<strong>Name:</strong><br>
					<?php echo $member->firstname; ?> <?php echo $member->lastname; ?>
				</p>
				
				<?php if(!empty($member->company)) { ?>
				<p>
					<strong>Company:</strong><br>
					<?php echo $member->company; ?> <?php if(!empty($member->orgno)) echo '('.$member->orgno.')'; ?>
				</p>
				<?php } ?>
				
				<p>
					<strong>Member since:</strong><br>
					<?php echo date('Y-m-d', $member->registered); ?>
				</p>
				
				<p>
					<strong>Membership Due:</strong><br>
					<?php echo $member->membership; ?>
				</p>
				
			</div>
			
			<div class="span3">
				<p>
					<strong>E-mail address:</strong><br>
					<a href="mailto:<?php echo $member->email; ?>"><?php echo $member->email; ?></a>
				</p>
				
				<p>
					<strong>Address:</strong><br>
					<?php echo $member->address; ?><br>
					<?php echo (!empty($member->address2) ? $member->address2 . '<br>' : ''); ?>
					<?php echo $member->zipcode; ?> <?php echo $member->city; ?>
					<?php echo (!empty($member->country) ? '<br>'.$this->dbconfig->countries->{$member->country} : ''); ?>
				</p>
				
				<?php if(!empty($member->phone)) { ?>
				<p>
					<strong>Phone:</strong><br>
					<a href="callto:<?php echo $member->phone; ?>"><?php echo $member->phone; ?></a>
				</p>
				<?php } ?>
				
				<?php if(!empty($member->mobile)) { ?>
				<p>
					<strong>Mobile:</strong><br>
					<a href="callto:<?php echo $member->mobile; ?>"><?php echo $member->mobile; ?></a>
				</p>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<br>
<div class="row">
	<div class="span6">
		<h4>Member Projects</h4>
		<p>
			* Comes later, maybe *
		</p>
		
		<br>
		<h4>Member Wall</h4>
		<p>
			* Comes later, maybe *
		</p>
	</div>
	
	<?php if(!empty($member->twitter)) { ?>
		<div class="span6">
			<h4>Tweets by @<?php echo $member->twitter; ?></h4>
			<a class="twitter-timeline" data-dnt=true href="https://twitter.com/<?php echo $member->twitter; ?>" data-widget-id="288460471424647169">Tweets by @<?php echo $member->twitter; ?></a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
	<?php } ?>
</div>