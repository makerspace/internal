<p>You're inside! :)</p>

<a href="/auth/logout" class="btn btn-primary">Sign out</a>

<h2>Userdata:</h2>
<pre><?php var_dump($this->session->userdata); ?></pre>

<h2>Database:</h2>
<pre><?php var_dump($this->User_model->get_user()); ?></pre>