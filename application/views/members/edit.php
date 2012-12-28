<h2>Edit Member</h2>
<div class="row">
	<?php echo form_open('members/edit', 'class="form-horizontal"'); ?>
	<?php echo form_hidden('user_id', $user->id); ?>
	
	<div class="span6 pull-left">
		<?php echo form_fieldset('Member Information'); ?>
			<div class="control-group">
				<?php echo form_label('E-mail address', 'email', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_email('email', $user->email, 'id="email" required placeholder="email@example.com"'); ?>
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
						<?php echo form_input('twitter', $user->twitter, 'id="twitter" style="width:179px"'); ?>
					</div>
					<span class="help-inline">Optional</span>
				</div>
			</div>
			
			
			<div class="control-group">
				<?php echo form_label('Membership Due', 'membership', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_input('membership', $user->membership, 'data-date-format="yyyy-mm-dd" id="membership" class="datepicker" required'); ?>
				</div>
			</div>
				
		<?php echo form_fieldset_close(); ?>
	</div>
	<div class="span6 pull-right">
		<?php echo form_fieldset('Personal Information'); ?>
			
				<div class="control-group">
					<?php echo form_label('Firstname', 'firstname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('firstname', $user->firstname, 'id="firstname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Lastname', 'lastname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('lastname', $user->lastname, 'id="lastname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Company Name', 'company', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('company', $user->company, 'id="company"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address', 'address', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address', $user->address, 'id="address" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address 2', 'address2', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address2', $user->address2, 'id="address2"'); ?>
					<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('City', 'city', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('city', $user->city, 'id="city" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Zip code', 'zipcode', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('zipcode', $user->zipcode, 'id="zipcode" required placeholder="12345"'); ?>
						<span class="help-inline">Without spaces</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Country', 'country', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_dropdown('country', $this->dbconfig->countries, $user->country, 'id="country" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Phone', 'phone', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('phone', $user->phone, 'id="phone" placeholder="+46812345678"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Mobile', 'mobile', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('mobile', $user->mobile, 'id="mobile" placeholder="+46812345678"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
		<?php echo form_fieldset_close(); ?>
	</div>
	<div class="span6">
		<?php echo form_fieldset('Member Access (ACL)'); ?>
			<div class="span2 pull-left">
				<label><?php echo form_checkbox('active', '1', (bool)$user->active); ?> Active member</label>
				<label><?php echo form_checkbox('labaccess', '1', (bool)$user->labaccess); ?> Access to the lab</label>
				<label><?php echo form_checkbox('feepaid', '1', (bool)$user->feepaid); ?> Member Fee Paid</label>
			</div>
			<div class="span3 pull-right">
				<label><?php echo form_checkbox('boardmember', '1', (bool)$user->boardmember); ?> Board member</label>
				<label><?php echo form_checkbox('founder', '1', (bool)$user->founder); ?> Founder of Makerspace</label>
				<label><?php echo form_checkbox('admin', '1', (bool)$user->admin); ?> Administrator</label>
			</div>
			<div class="span6 pull-left">
				<br>
				<br>
				<button type="submit" class="btn btn-large span5 btn-info pull-left">Update Member</button>
			</div>	
		<?php echo form_fieldset_close(); ?>
	</div>
	<?php echo form_close(); ?>
</div>
<br>