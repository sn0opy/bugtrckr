<!doctype html>
<html>
<head>
      <meta charset="utf-8" />
      <title>{{@pageTitle}} - Bugtrckr</title>
      <base href="{{@BASE}}" />
      <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
<link href="/css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="/">Bugtrckr</a>
			<ul class="nav">
				<li{{@onpage=='start'?' class="active"':''}}><a href="/">{{@lng.home}}</a></li>
				<check if="{{isset(@SESSION.project)}}">
					<true>
						<li{{@onpage=='tickets'?' class="active"':''}}><a href="tickets">{{@lng.tickets}}</a></li>
						<li{{@onpage=='roadmap'?' class="active"':''}}><a href="roadmap">{{@lng.roadmap}}</a></li>
						<li{{@onpage=='timeline'?' class="active"':''}}><a href="timeline">{{@lng.timeline}}</a></li>
						<li{{@onpage=='wiki'?' class="active"':''}}><a href="wiki">{{@lng.wiki}}</a></li>
						<check if="{{isset(@SESSION.user)}}">
							<true>
								<li{{@onpage=='settings'?' class="active"':''}}><a href="/project/settings">{{@lng.settings}}</a></li>
							</true>
						</check>
					</true>
				</check>  
				<check if="{{(isset(@projects) && count(@projects) > 0) || isset(@SESSION.user.hash)}}">
					<li class="projectChooser dropdown">	
						<a class="dropdown-toggle textDropdown" data-toggle="dropdown">Project <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<repeat group="{{@projects}}" value="{{@project}}">
								<check if="{{helper::canRead(@project->hash)}}">
									<li><a href="/project/select/{{@project->hash}}">{{@project->name}} {{(isset(@SESSION.project) && @project->hash == @SESSION.project)?'<span class="label label-success">'.strtolower(@lng.active).'</span>':''}}</a></li>
								</check>
							</repeat>
							<li><a href="/project/add"><em>{{@lng.newProject}}</em></a>
						</ul>
					</li>
				</check>
			</ul>
			<ul class="nav pull-right">
				<check if="{{isset(@SESSION.user)}}">
					<true>
						<li><a href="/user/{{@SESSION.user.name}}">{{@SESSION.user.name}}</a></li>
						<li><a href="/user/logout">{{@lng.logout}}</a></li>
					</true>
					<false>
						<li{{@onpage=='registration'?' class="active"':''}}><a href="/user/new">{{@lng.registration}}</a></li>
						<li><a href="#login" data-toggle="modal">{{@lng.login}}</a></li>
					</false>
				</check>
			</ul>
		</div>
	</div>
</div>

<check if="{{isset(@installWarning) && isset(@RELEASE)}}">
	<div class="message warning">{{@lng.warningInstallFiles}}</div>
</check>

<check if="{{!isset(@SESSION.user)}}">
	<div id="login" class="modal hide">
		<form action="/user/login" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>{{@lng.login}}</h3>
			</div>
			<div class="modal-body">
				<div class="formRow">
					<div class="formLabel">{{@lng.email}}: </div>
					<div class="formValue"><input type="text" name="email" /></div>
				</div>
				<div class="formRow">
					<div class="formLabel">{{@lng.password}}: </div>
					<div class="formValue"><input type="password" name="password" /><br/><a href="/user/new" class="noAcc">{{@lng.noaccount}}</a></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" value="{{@lng.login}}" class="btn btn-primary" />
			</div>
		</form>
	</div>
</check>

<div id="content">
	<div id="content-inner">
		<check if="{{isset(@SESSION.FAILURE)}}">
			<div class="alert alert-error">{{@SESSION.FAILURE}}</div>
		</check>
		<check if="{{isset(@SESSION.SUCCESS)}}">
			<div class="alert alert-success">{{@SESSION.SUCCESS}}</div>
		</check>
		<include href="{{@template}}" />
	</div>
</div>

<div id="footer">
	<p>&copy; 2013 Bugtrckr-Team</p>
</div>

<script src="/js/jquery.js"></script>
<script src="/js/jquery.tablesorter.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/bugtrckr.js"></script>
</body>
</html>
