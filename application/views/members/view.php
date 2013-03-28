<div class="row">
	<div class="span12">
		<h2 class="pull-left">View member</h2>
		<a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary pull-right">Change Member Information</a>
	</div>
	
	<div class="span4">
		<h4>Member Tasks</h4>		
		<p>
			<a href="/pdf/membership_card/<?php echo $member->id; ?>" class="btn btn-primary">Download Membership Card <small>(as PDF)</small></a>
		</p>
		
		<br>
		<h4>Membership Fee Payment</h4>
		<p>
			<a href="#" class="btn">Send Fortnox Invoice</a> 
			<a href="#" class="btn">Send PayPal Link</a> 
		</p>
		
		<br>
		<h4>Member of Groups <small>Click to switch state</small></h4>
			
		<?php foreach($this->Group_model->get_all() as $row) { ?>
			<a href="/members/group_switch/<?php echo $member->id; ?>/<?php echo $row->name; ?>" style="margin: 4px 2px;" class="btn<?php echo (!empty($member->groups[$row->name]) ? ' btn-inverse' : ''); ?>">
				<?php echo $row->description; ?>
			</a>
		<?php } ?>
	</div>

	<div class="span8">
		<h4>Profile</h4>
		
		<div class="row">
			<div class="span2">
				<img src="<?php echo gravatar($member->email, 160); ?>" class="img-polaroid">
				<center><small>Avatar by <a href="https://www.gravatar.com/">Gravatar</a></small></center>
			</div>
			
			<div class="span3">
				<p>
					<strong>Name:</strong><br>
					<?php echo $member->firstname; ?> <?php echo $member->lastname; ?>
				</p>
				
				<p>
					<strong>E-mail address:</strong><br>
					<a href="mailto:<?php echo $member->email; ?>"><?php echo $member->email; ?></a>
				</p>
				
				<?php if(!empty($member->mobile)) { ?>
				<p>
					<strong>Mobile:</strong><br>
					<a href="callto:<?php echo $member->mobile; ?>"><?php echo $member->mobile; ?></a>
				</p>
				<?php } ?>
				
				<?php if(!empty($member->phone)) { ?>
				<p>
					<strong>Alt. Phone:</strong><br>
					<a href="callto:<?php echo $member->phone; ?>"><?php echo $member->phone; ?></a>
				</p>
				
				<?php } if(!empty($member->skype)) { ?>
				<p>
					<strong>Skype:</strong><br>
					<a href="skype:<?php echo $member->skype; ?>?chat"><?php echo $member->skype; ?></a>
				</p>

				<?php } if(!empty($member->twitter)) { ?>
				<p>
					<strong>Twitter:</strong><br>
					<a href="http://twitter.com/<?php echo $member->twitter; ?>"><?php echo $member->twitter; ?></a>
				</p>

				<?php } ?>
				
			</div>
			
			<div class="span3">
				
				<?php if(!empty($member->company)) { ?>
				<p>
					<strong>Company:</strong><br>
					<?php echo $member->company; ?> <?php if(!empty($member->orgno)) echo '('.$member->orgno.')'; ?>
				</p>
				<?php } ?>
				
				<p>
					<strong>Address:</strong><br>
					<?php echo (!empty($member->address) ? $member->address . '<br>' : ''); ?>
					<?php echo (!empty($member->address2) ? $member->address2 . '<br>' : ''); ?>
					<?php echo $member->zipcode; ?> <?php echo $member->city; ?>
					<?php echo (!empty($member->country) ? '<br>'.$this->dbconfig->countries->{$member->country} : ''); ?>
				</p>
				
				<p>
					<strong>Member since:</strong><br>
					<?php echo date('Y-m-d', $member->registered); ?>
				</p>
			</div>
		</div>
	</div>
</div>
