<h1>API Documentation</h1>
<p>REST API for Internal - Version 0.1.</p>

<h3>Information</h3>
<p>Resources returns response-data as JSON.<br>
Standard HTTP Status codes are used for all responses:</p>
<ul>
	<li>200 = OK</li>
	<li>201 = Resource created</li>
	<li>401 = Unauthorized</li>
	<li>404 = Not found</li>
	<li>405 = Method Not Allowed</li>
	<li>501 = Not Implemented</li>
	<li>503 = Service Unavailable</li>
</ul>


<h3>Public resources</h3>
<ul>
	<li>POST /api/member - Add a new member</li>
	<li>GET /api/newsletter/*id* - Get newsletter by id</li>
</ul>


<h3>Protected resources (Requires authentication)</h3>
<p>Use the HTTP Headers <strong>X-Username</strong> and <strong>X-Password</strong> to authenticate.<br>
<em>Note: These resources might also require the "API" ACL on your account.</em></p>

<ul>
	<li>GET /api/member/*id* - Get member by id</li>
	<li>GET /api/member/*key*/*value* - Get member by key</li>
</ul>