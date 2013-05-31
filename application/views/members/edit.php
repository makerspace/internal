<h2>Edit Member</h2>
<div class="row">
	<?php echo form_open('', 'class="form-horizontal" autocomplete="off"'); ?>
	
	<div class="span6 pull-left">
		<?php echo form_fieldset('Member Information'); ?>
			<div class="control-group">
				<?php echo form_label('E-mail address', 'email', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_email('email', set_value('email', $member->email), 'id="email" required placeholder="email@example.com"'); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo form_label('Password', 'password', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_password('password', '', 'id="password"'); ?>
					<span class="help-block">Leave empty to keep current password.</span>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo form_label('Twitter', 'twitter', array('class' => 'control-label')); ?>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on">@</span>
						<?php echo form_input('twitter', set_value('twitter', $member->twitter), 'id="twitter" style="width:179px"'); ?>
					</div>
					<span class="help-inline">Optional</span>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo form_label('Skype Name', 'skype', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_input('skype', set_value('skype', $member->skype), 'id="skype"'); ?>
					<span class="help-inline">Optional</span>
				</div>
			</div>
			
				
		<?php echo form_fieldset_close(); ?>
	</div>
	<div class="span6 pull-right">
		<?php echo form_fieldset('Personal Information'); ?>

				<div class="control-group">
					<?php echo form_label('Firstname', 'firstname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('firstname', set_value('firstname', $member->firstname), 'id="firstname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Lastname', 'lastname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('lastname', set_value('lastname', $member->lastname), 'id="lastname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Civic Reg. Number', 'civicregno', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('civicregno', set_value('civicregno', $member->civicregno), 'id="civicregno"'); ?>
						<span class="help-inline">YYYYMMDD-XXXX</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address', 'address', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address', set_value('address', $member->address), 'id="address"'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address 2', 'address2', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address2', set_value('address2', $member->address2), 'id="address2"'); ?>
					<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Zip code', 'zipcode', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('zipcode', set_value('zipcode', $member->zipcode), 'id="zipcode"'); ?>
						<span class="help-inline">12345 (Without Space)</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('City', 'city', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('city', set_value('city', $member->city), 'id="city"'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Country', 'country', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_dropdown('country', $this->dbconfig->countries, $member->country, 'id="country"'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Phone number', 'phone', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('phone', set_value('phone', $member->phone), 'id="phone"'); ?>
						<span class="help-inline">+46123000000</span>
					</div>
				</div>
				
				<br>
				
				<div class="control-group">
					<?php echo form_label('Company Name', 'company', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('company', set_value('company', $member->company), 'id="company"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Org. Number', 'orgno', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('orgno', set_value('orgno', $member->orgno), 'id="orgno"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
		<?php echo form_fieldset_close(); ?>
	</div>
	
	<div class="span6 pull-left">
	
	<?php if($member->id != 1000) { ?>
		<?php echo form_fieldset('Member of Groups <small>Not implemented yet</small>'); ?>
			
			<?php foreach($this->Group_model->get_all() as $row) { ?>
				<label class="span2"><?php echo form_checkbox('group['.$row->name.']', '1', !empty($member->groups[$row->name]), 'disabled') . ' ' . $row->description; ?></label>
			<?php } ?>
			
	<?php } ?>
	
			<div class="span6">
				<br><br>
				<button type="submit" class="btn btn-large span5 btn-info pull-left">Update Member</button>
			</div>	
			
		<?php echo form_fieldset_close(); ?>
	</div>
	<?php echo form_close(); ?>
</div>
<br>
