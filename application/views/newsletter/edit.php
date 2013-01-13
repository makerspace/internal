<div class="row">
		<div class="span9 pull-left">
		<h2>Edit Newsletter</h2>
			<p>When you're happy with the newsletter - Click <strong>Save and Preview</strong> to save it. 
			You'll be able to send a test newsletter and view the full list of recipients on the next page, before actually sending it.</p>
		</div>
		<div class="span3 pull-right">
			<h3>Using Placeholders</h3>
			<p>This function supports using per-user variables as placeholders to make each newsletter unique. 
			Each placeholder is replaced with the actual member value. List of supported placeholders:</p>

			<ul>
				<li>{email} - The members e-mail address</li>
				<li>{firstname} - Firstname of member</li>
				<li>{lastname} - Lastname of member</li>
				<li>{fullname} - First and lastname of member</li>
				<li>{company} - Company of member</li>
				<li>{membershipdue} - Membership Due Date</li>
				<br>
				<li>{week} - Current week (52)</li>
				<li>{date} - Current date (01)</li>
				<li>{month} - Current month (12)</li>
				<li>{year} - Current year (2013)</li>
			</ul>
		</div>
		
		<script src="/assets/js/ckeditor/ckeditor.js"></script>
		<div class="span9 pull-left">
		<?php echo form_open(); ?>
			
			<h4>Newsletter Subject</h4>
			<?php echo form_input('subject', (set_value('subject') ? set_value('subject') : $newsletter->subject), 'class="input-xxlarge" required'); ?>
			
			<h4>Newsletter Body</h4>
			<?php echo form_textarea('body', (set_value('body') ? set_value('body') : $newsletter->body), 'class="ckeditor"'); ?>
			
			<br>
			<?php echo form_submit('save', 'Save and Preview', 'class="btn btn-large"'); ?>
			
		<?php echo form_close(); ?>
	</div>
</div>
<br>