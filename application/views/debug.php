<h2>UUID:</h2>
<pre><?php var_dump(uuid()); ?></pre>

<h2>DBConfig:</h2>
<pre><?php var_dump($this->dbconfig); ?></pre>

<h2>Userdata:</h2>
<pre><?php var_dump($this->session->userdata); ?></pre>

<h2>Database:</h2>
<pre><?php var_dump($this->Member_model->get_member()); ?></pre>