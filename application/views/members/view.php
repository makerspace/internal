<div class="row">
	<div class="span12">
		<h3 class="pull-left">Viewing member #<?php echo $member->id; ?></h3>
		<a href="/members/edit/<?php echo $member->id; ?>" class="btn btn-primary pull-right">Change Member Information</a>
	</div>
	
	<?php if($member->id != 1000) { ?>
	<div class="span4">
		<h3>Member Tasks</h3>		
		<p>
			<a href="/members/membership_card/<?php echo $member->id; ?>" class="btn btn-primary">Get Membership Card <small>(as PDF)</small></a>
		</p><p>
			<a href="/finance/member/<?php echo $member->id; ?>" class="btn">View Transaction History</a>
		</p>
		<br>

		<h3>Lab access <small>NOT WORKING YET</small></h3>
		<p>Months marked below represent those who the member have had or going to have labaccess.</p>
		<h4>2013</h4>
		<div class="row">
		<?php foreach(range(1,12) as $lab2013) { ?>
			<label class="span1"><?php echo form_checkbox('labaccess2013['.$lab2013.']', '1', false, 'disabled') . ' ' . date('M', strtotime('2013-'.$lab2013)); ?></label>
		<?php } ?>
		</div>

		<h4>2014</h4>
		<div class="row">
		<?php foreach(range(1,12) as $lab2013) { ?>
			<label class="span1"><?php echo form_checkbox('labaccess2014['.$lab2013.']', '1', false, 'disabled') . ' ' . date('M', strtotime('2013-'.$lab2013)); ?></label>
		<?php } ?>
		</div>

	</div>
	<?php } else { ?>
		<div class="span4"><br><br><p class="well lead">This is a internal member account used for API-access and similar only and shall not be used for administrative purposes.</p></div>
	<?php } ?>

	<div class="span8">
		<h3>Member information</h3>
		
		<div class="row">
			<div class="span3">
				<img src="<?php echo gravatar($member->email, 256); ?>" class="img-polaroid">
				<p class="pagination-centered"><small>Avatar by <a href="https://www.gravatar.com/">Gravatar</a></small></p>
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
				
				<?php if(!empty($member->phone)) { ?>
				<p>
					<strong>Phone:</strong><br>
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
	<?php if($member->id != 1000) { ?>
			<div class="span8">
					<h3>Member of Groups <small>Click to switch state</small></h3>
			
		<?php foreach($this->Group_model->get_all() as $row) { 
			// Don't allow non-admins to set admin-permissions - and cause of that, you shouldn't be able to remove your self as admin.
			if($row->name == 'admins' && (!$this->Group_model->member_of_group(member_id(), 'admins') || $member->id == member_id())) {
				continue;
			}
		?>
			<a href="/members/group_switch/<?php echo $member->id; ?>/<?php echo $row->name; ?>" style="margin: 4px 3px;" class="btn <?php echo (!empty($member->groups[$row->name]) ? ' btn-inverse' : ''); ?>">
				<?php echo $row->description; ?>
			</a>
		<?php } ?>
		<br>

			</div>
	<?php } ?>
		</div>
	</div>
</div>
