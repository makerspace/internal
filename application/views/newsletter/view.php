<div class="row">
		<div class="span9 pull-left">
			<h2>Viewing Newsletter</h2>
			<p>Below you'll see a demo of how the newsletter will look in the users client. It's not 100% correct, but it gives a good indication on how it will look.
			<br>To the right you'll able to send a <strong>Test Newsletter</strong> to your own e-mail, below it you'll also see some newsletter statistics and a list of recipients.</p>
		</div>
		
		<div class="span3 pull-right">
			<h3>Newsletter Actions</h3>
			<a href="/newsletter/edit/<?php echo $newsletter->id; ?>" class="btn btn-primary btn-block">Edit Newsletter</a><br>
			<a href="/newsletter/test_send/<?php echo $newsletter->id; ?>" class="btn btn-info btn-block">Send Test Newsletter</a><br>
			<a href="/newsletter/send/<?php echo $newsletter->id; ?>" class="btn btn btn-block" onclick="return confirm('Are you sure you want to send the newsletter to ALL recipients?');">Send Real Newsletter</a><br>
			
			<h3>Statistics</h3>
			<p>ToDo: Add statistics here.</p>
			
		</div>
		
		<div class="span9 pull-left">
			<h4>E-mail Subject:</h4>
			<?php echo $newsletter->subject; ?>
			<br><br>
			
			<h4>E-mail Body:</h4>
			<?php echo $newsletter->body; ?>
			
		</div>
</div><br>