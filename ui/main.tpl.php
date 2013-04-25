<!doctype html>
<html>
    <head>
		<meta charset="utf-8" />
		<link href="{{@BASE}}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="{{@BASE}}/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
		<link href="{{@BASE}}/css/style.css" rel="stylesheet" type="text/css" />
		<title>{{@pageTitle}} - Bugtrckr</title>

		<script type="text/javascript" src="{{@BASE}}/js/jquery.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/bootstrap.modals.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/bootstrap.dropdown.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/bugtrckr.js"></script>
    </head>

    <body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="{{@BASE}}/">Bugtrckr</a>
				<ul class="nav">
					<li{{@onpage=='start'?' class="active"':''}}><a href="{{@BASE}}/">{{@lng.home}}</a></li>
					<check if="{{isset(@SESSION.project)}}">
						<true>
							<li{{@onpage=='tickets'?' class="active"':''}}><a href="{{@BASE}}/tickets">{{@lng.tickets}}</a></li>
							<li{{@onpage=='roadmap'?' class="active"':''}}><a href="{{@BASE}}/roadmap">{{@lng.roadmap}}</a></li>
							<li{{@onpage=='timeline'?' class="active"':''}}><a href="{{@BASE}}/timeline">{{@lng.timeline}}</a></li>
							<li{{@onpage=='wiki'?' class="active"':''}}><a href="{{@BASE}}/wiki">{{@lng.wiki}}</a></li>
							<check if="{{@SESSION.user}}">
								<true>
									<li{{@onpage=='settings'?' class="active"':''}}><a href="{{@BASE}}/project/settings">{{@lng.settings}}</a></li>
								</true>
							</check>
						</true>
					</check>
					<check if="{{isset(@SESSION.user)}}">
						<false>
							<li{{@onpage=='registration'?' class="active"':''}}><a href="{{@BASE}}/user/new">{{@lng.registration}}</a></li>
						</false>
					</check>  
					<check if="{{count(@projects) > 0 || @SESSION.user.hash}}">
						<li class="projectChooser dropdown">	
							<a class="dropdown-toggle textDropdown" data-toggle="dropdown">Project <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<repeat group="{{@projects}}" value="{{@project}}">
									{*
									TODO: static call is broken
									helper::canRead(@project->hash)
									<check if="{{1==1}}">
										<li><a href="{{@BASE}}/project/select/{{@project->hash}}">{{@project->name}} {{(isset(@SESSION.project) && @project->hash == @SESSION.project)?'<span class="label label-success">'.strtolower(@lng.active).'</span>':''}}</a></li>
									</check>
									*}
								</repeat>
								<li><a href="{{@BASE}}/project/add"><em>{{@lng.newProject}}</em></a>
							</ul>
						</li>
					</check>
				</ul>
				<ul class="nav pull-right">
					<check if="{{isset(@SESSION.user)}}">
						<true>
							<li><a href="{{@BASE}}/user/{{@SESSION.user->name}}">{{@SESSION.user->name}}</a></li>
							<li><a href="{{@BASE}}/user/logout">{{@lng.logout}}</a></li>
						</true>
						<false>
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
			<form action="{{@BASE}}/user/login" method="post">
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
						<div class="formValue"><input type="password" name="password" /><br/><a href="{{@BASE}}/user/new" class="noAcc">{{@lng.noaccount}}</a></div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" value="{{@lng.login}}" class="btn btn-primary" />
				</div>
			</form>
        </div>
    </check>

    <div id="content">
		<div id="innerContentLOL">
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
        {{@lng.footer}}
    </div>
    </body>
</html>
