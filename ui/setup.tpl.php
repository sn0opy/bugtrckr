<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Bugtrckr Setup</title>

        <link href="{{@BASE}}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{@BASE}}/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{@BASE}}/css/setup.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
	<form action="{{@BASE}}/setup" method="post">
	    <div class="container">
		<h1>Bugtrckr Setup</h1>
		<ul class="nav nav-pills">
		    <li class="active"><a href="{{@BASE}}/setup/#requirements" data-toggle="tab">1. Requirements</a></li>
		    <check if="{{!@reqs.error}}">
			<li><a href="{{@BASE}}/setup/#database" data-toggle="tab">2. Database</a></li>
			<li><a href="{{@BASE}}/setup/#admin" data-toggle="tab">3. Admin</a></li>
		    </check>
		</ul>
		<div class="tab-content">
		    <div class="tab-pane active" id="requirements">
			<table class="table table-bordered">
			    <tr>
				<th>Requirement</th>
				<th>Result</th>
			    </tr>
			    <tr>
				<td>MySQL</td>
				<td>
				    <check if="{{@reqs.checks.mysql}}">
					<true><span class="label label-success">avilable</span></true>
					<false><span class="label label-important">unavailable</false>
				    </check>
				</td>
			    </tr>
			    <tr>
				<td>SQLite</td>
				<td>
				    <check if="{{@reqs.checks.sqlite}}">
					<true><span class="label label-success">avilable</span></true>
					<false><span class="label label-important">unavailable</false>
				    </check>
				</td>
			    </tr>
			    <tr>
				<td><em>app</em> folder writeable</td>
				<td>
				    <check if="{{@reqs.checks.config1}}">
					<true><span class="label label-success">yes</span></true>
					<false><span class="label label-important">no</false>
				    </check>
				</td>
			    </tr>
			    <tr>
				<td><em>config.ini.php</em> existing</td>
				<td>
				    <check if="{{@reqs.checks.config1}}">
					<true><span class="label label-success">no</span></true>
					<false><span class="label label-important">yes</false>
				    </check>
				</td>
			    </tr>
			</table>
			<check if="{{@reqs.error}}">
			    <true>
				<p class="alert alert-danger">Please fix above errors. You also need at least one DB engine.</p>
			    </true>
			    <false>
				<p class="alert alert-success">You meet all requirements. Let's continue.</p>
				<hr/>
				<a href="{{@BASE}}/setup/#dataabse" data-toggle="tab" class="btn btn-success">Next step &raquo;</a>
			    </false>
			</check>
		    </div>
		    <div class="tab-pane" id="database">
			<div class="row">
				<check if="{{@reqs.checks.mysql}}">
				    <true>
						<div class="span3 well">				
							<input type="text" name="sqlusername" placeholder="User name" /><br/>
							<input type="password" name="sqlpassword" placeholder="Password" /><br/>
							<input type="text" name="sqldb" placeholder="Database name" /><br/>
							<input type="text" name="sqlserver" placeholder="Server" /><br/>
							<a href="#" class="btn pull-right"><i class="icon-ok"></i> Validate</a>
						</div>
				    </true>
				    <false>
					<p class="alert alert-danger span3">MySQL is not available</p>
				    </false>
				</check>
			    <div class="span8">
				<check if="{{@reqs.checks.sqlite}}">
				    <true>
					<check if="{{@reqs.checks.mysql}}">
					    <true>
						<p class="alert alert-info">Skipping this step is possible!</p>
						<p>Please enter your MySQL credentials on the left.</p>

						<p><strong>Hint: </strong>Since SQLite is available on your system, you could skip this step 
							by leaving the fields on the left blank. The setup will create a random-named SQLite 
							database. You can migrate this database to MySQL later.</p>
						<p>If you like to use Bugtrckr in a productive environment, you should 
						consider MySQL!</p>
					    </true>
					    <false>
						<p>Please enable MySQL and reload the page, <strong>or</strong> just continue to the next step, to use SQLite instead.</p>
						<p>The setup will create a random-named SQLite 
						    database. You can migrate this database to MySQL later.</p>
					    </false>
					</check>
				    </true>
				    <false>
					<p>Please enter your MySQL credentials on the left.</p>
					<p><strong>Hint:</strong> SQLite is not available on your server. If you just want to test 
					    Bugtrckr, we recommend enabling it, because there's no need for credentials and you just 
					    have to delete the database file.</p>
				    </false>
				</check>
			    </div>
			</div>
			<hr/>
			<a href="{{@BASE}}/setup/#dataabse" data-toggle="tab" class="btn btn-success">Next step &raquo;</a>
		    </div>
		    <div class="tab-pane" id="admin">
			<div class="row">
			    <div class="span8">
				<p class="alert alert-info">Almost done!</p>

				<p>Create your own account.</p>

				<p>This account will have admin rights, which means 
				    you have access to all projects without any restrictions.</p>
			    </div>
				<div class="span3 well">
					<input type="text" name="username" placeholder="User name" />
					<input type="text" name="email" placeholder="Email" />
					<input type="password" name="password" placeholder="Password" />
					<input type="password" name="passwordrepeat" placeholder="Repeat password" />
				</div>
			</div>
			<hr/>
			<input type="submit" value="Installieren" class="btn btn-primary" />
		    </div>
		</div>
	    </div>
	</form>
        <script src="{{@BASE}}/js/jquery.js"></script>
        <script src="{{@BASE}}/js/bootstrap.min.js"></script>
	<script>
	$(document).ready(function() {
	    
	});
	</script>
    </body>
</html>
