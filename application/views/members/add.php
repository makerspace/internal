<h2>Add new member</h2>
<div class="row">
	<?php echo form_open('members/add', 'class="form-horizontal"'); ?>

	<div class="span6 pull-left">
		<?php echo form_fieldset('Member Information'); ?>
			<div class="control-group">
				<?php echo form_label('E-mail address', 'email', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_email('email', set_value('email'), 'id="email" required placeholder="email@example.com"'); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo form_label('Password', 'password', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_password('password', '', 'id="password"'); ?>
					<span class="help-block">Leave empty to generate a random password.</span>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo form_label('Twitter', 'twitter', array('class' => 'control-label')); ?>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on">@</span>
						<?php echo form_input('twitter', set_value('twitter'), 'id="twitter" style="width:179px"'); ?>
					</div>
					<span class="help-inline">Optional</span>
				</div>
			</div>
			
			
			<div class="control-group">
				<?php echo form_label('Membership Due', 'membership', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo form_input('membership', set_value('membership'), 'data-date-format="yyyy-mm-dd" id="membership" class="datepicker" required'); ?>
				</div>
			</div>
				
		<?php echo form_fieldset_close(); ?>
	</div>
	<div class="span6 pull-right">
		<?php echo form_fieldset('Personal Information'); ?>
			
				<div class="control-group">
					<?php echo form_label('Firstname', 'firstname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('firstname', set_value('firstname'), 'id="firstname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Lastname', 'lastname', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('lastname', set_value('lastname'), 'id="lastname" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Company Name', 'company', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('company', set_value('lastname'), 'id="company" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address', 'address', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address', set_value('address'), 'id="address" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Address 2', 'address2', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('address2', set_value('address2'), 'id="address2"'); ?>
					<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('City', 'city', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('city', set_value('city'), 'id="city" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Zip code', 'zipcode', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('zipcode', set_value('zipcode'), 'id="zipcode" required placeholder="123456"'); ?>
						<span class="help-inline">Without spaces</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Country', 'country', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_dropdown('country', $this->dbconfig->countries, set_value('country'), 'id="country" required'); ?>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Phone', 'phone', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('phone', set_value('phone'), 'id="phone" placeholder="+46812345678"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
				<div class="control-group">
					<?php echo form_label('Mobile', 'mobile', array('class' => 'control-label')); ?>
					<div class="controls">
						<?php echo form_input('mobile', set_value('mobile'), 'id="mobile" placeholder="+46812345678"'); ?>
						<span class="help-inline">Optional</span>
					</div>
				</div>
				
		<?php echo form_fieldset_close(); ?>
	</div>
	<div class="span6">
		<?php echo form_fieldset('Member Access (ACL)'); ?>
			<div class="span2 pull-left">
				<label><?php echo form_checkbox('active', '1', true); ?> Active member</label>
				<label><?php echo form_checkbox('labaccess', '1', false); ?> Access to the lab</label>
				<label><?php echo form_checkbox('feepaid', '1', false); ?> Member Fee Paid</label>
			</div>
			<div class="span3 pull-right">
				<label><?php echo form_checkbox('boardmember', '1', false); ?> Board member</label>
				<label><?php echo form_checkbox('founder', '1', false); ?> Founder of Makerspace</label>
				<label><?php echo form_checkbox('admin', '1', false); ?> Administrator</label>
			</div>
			<div class="span6 pull-left">
				<br>
				<br>
				<button type="submit" class="btn btn-large span3 btn-primary pull-left">Add new member</button>
				<button type="reset" class="btn btn-large span2 pull-left">Reset</button>
			</div>	
		<?php echo form_fieldset_close(); ?>
	</div>
	<?php echo form_close(); ?>
</div>
<br>