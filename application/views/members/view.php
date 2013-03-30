<div class="row">
	<div class="span12">
		<h2 class="pull-left">View member</h2>
		<a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary pull-right">Change Member Information</a>
	</div>
	
	<?php if($member->id != 1000) { ?>
	<div class="span4">
		<h4>Member Tasks</h4>		
		<p>
			<a href="/members/membership_card/<?php echo $member->id; ?>" class="btn btn-primary">Get Membership Card <small>(as PDF)</small></a>
		</p><p>
			<a href="/finance/member/<?php echo $member->id; ?>" class="btn">View Transaction History</a>
		</p>
		<br>
		<h4>Member of Groups <small>Click to switch state</small></h4>
			
		<?php foreach($this->Group_model->get_all() as $row) { 
			if($row->name == 'admins' && !$this->Group_model->member_of_group(member_id(), 'admins')) {
				continue;
			}
		?>
			<a href="/members/group_switch/<?php echo $member->id; ?>/<?php echo $row->name; ?>" style="margin: 4px 2px;" class="btn<?php echo (!empty($member->groups[$row->name]) ? ' btn-inverse' : ''); ?>">
				<?php echo $row->description; ?>
			</a>
		<?php } ?>
	</div>
	<?php } else { ?>
		<div class="span4"><br><br><p class="well lead">This is a internal member account used for API-access and similar only and shall not be used for administrative purposes.</p></div>
	<?php } ?>

	<div class="span8">
		<h4>Profile</h4>
		
		<div class="row">
			<div class="span3">
				<img src="<?php echo gravatar($member->email, 256); ?>" class="img-polaroid">
				<center><small>Avatar by <a href="https://www.gravatar.com/">Gravatar</a></small></center>
			</div>
			
			<div class="span2">
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
				
				<?php if(!empty($member->civicregno)) { ?>
				<p>
					<strong>Civic Reg. Number:</strong><br>
					<?php echo $member->civicregno; ?>
				</p>
				<?php } ?>
				
				<?php if(!empty($member->company)) { ?>
				<p>
					<strong>Company:</strong><br>
					<?php echo $member->company; ?> <?php if(!empty($member->orgno)) echo '('.$member->orgno.')'; ?>
				</p>
				<?php } ?>
				
				
				<?php if(!empty($member->address) || !empty($member->zipcode) || !empty($member->city)) { ?>
				<p>
					<strong>Address:</strong><br>
					<?php echo (!empty($member->address) ? $member->address . '<br>' : ''); ?>
					<?php echo (!empty($member->address2) ? $member->address2 . '<br>' : ''); ?>
					<?php echo (!empty($member->zipcode) ? 'SE-'.$member->zipcode : ''); ?> <?php echo (!empty($member->city) ? $member->city : ''); ?>
					<?php echo (!empty($member->country) ? '<br>'.$this->dbconfig->countries->{$member->country} : ''); ?>
				</p>
				<?php } ?>
				
				<p>
					<strong>Member since:</strong><br>
					<?php echo date('Y-m-d', $member->registered); ?>
				</p>
			</div>
		</div>
	</div>
</div>
