<!doctype html>
<html>
    <head>
		<meta charset="UTF-8" />
		<link href="{{@BASE}}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="{{@BASE}}/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
		<link href="{{@BASE}}/css/style.css" rel="stylesheet" type="text/css" />
		<title>{{@pageTitle}} - Bugtrckr</title>

		<script type="text/javascript" src="{{@BASE}}/js/jquery.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/bootstrap.modals.js"></script>
		<script type="text/javascript" src="{{@BASE}}/js/bugtrckr.js"></script>
    </head>

    <body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="{{@BASE}}/">Bugtrckr</a>
				<ul class="nav">
					<li{{@onpage=='start'?' class="active"':''}}><a href="{{@BASE}}/">{{@lng.home}}</a></li>
					<F3:check if="{{@SESSION.project}}">
						<F3:true>
							<li{{@onpage=='tickets'?' class="active"':''}}><a href="{{@BASE}}/tickets">{{@lng.tickets}}</a></li>
							<li{{@onpage=='roadmap'?' class="active"':''}}><a href="{{@BASE}}/roadmap">{{@lng.roadmap}}</a></li>
							<li{{@onpage=='timeline'?' class="active"':''}}><a href="{{@BASE}}/timeline">{{@lng.timeline}}</a></li>
							<li{{@onpage=='wiki'?' class="active"':''}}><a href="{{@BASE}}/wiki">{{@lng.wiki}}</a></li>
							<F3:check if="{{@SESSION.user}}">
								<F3:true>
									<li{{@onpage=='settings'?' class="active"':''}}><a href="{{@BASE}}/project/settings">{{@lng.settings}}</a></li>
								</F3:true>
							</F3:check>
						</F3:true>
					</F3:check>
					<F3:check if="{{@SESSION.user}}">
						<F3:false>
							<li{{@onpage=='registration'?' class="active"':''}}><a href="{{@BASE}}/user/new">{{@lng.registration}}</a></li>
						</F3:false>
					</F3:check>  
					<F3:check if="{{count(@projects) > 0 || @SESSION.user.hash}}">
						{{*<li>
							<form method="post" action="{{@BASE}}/project/select">
								<select name="project" size="1" id="projectChooser">
									<option value=""></option>
									<F3:check if="{{@SESSION.user.hash}}">
										<option value="new">{{@lng.newProject}}</option>
									</F3:check>
									<F3:repeat group="{{@projects}}" value="{{@project}}">
										<F3:check if="{{\misc\helper::canRead(@project->hash)}}">
										<option value="{{@project->hash}}" {{(@project->hash == @SESSION.project)?'selected="selected"':''}}>{{@project->name}}</option>
										</F3:check>
									</F3:repeat>
								</select>
							</form>
						</li>*}}
					</F3:check>
				</ul>
				<ul class="nav pull-right">
					<F3:check if="{{@SESSION.user}}">
						<F3:true>
							<li><a href="{{@BASE}}/user/{{@SESSION.user->name}}">{{@SESSION.user->name}}</a></li>
							<li><a href="{{@BASE}}/user/logout">{{@lng.logout}}</a></li>
						</F3:true>
						<F3:false>
							<li><a href="#login" data-toggle="modal">{{@lng.login}}</a></li>
						</F3:false>
					</F3:check>
				</ul>
			</div>
		</div>
	</div>


    <F3:check if="{{@installWarning && @RELEASE}}">
        <div class="message warning">{{@lng.warningInstallFiles}}</div>
    </F3:check>

    <F3:check if="{{!@SESSION.user}}">
        <div id="login" class="modal hide">
			<form action="{{@BASE}}/user/login" method="post" class="clearfix">
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
    </F3:check>

    <div id="content">
		<div id="innerContentLOL">
            <F3:check if="{{@SESSION.FAILURE}}">
                <div class="alert alert-error">{{@SESSION.FAILURE}}</div>
            </F3:check>
            <F3:check if="{{@SESSION.SUCCESS}}">
                <div class="alert alert-success">{{@SESSION.SUCCESS}}</div>
            </F3:check>
            <F3:include href="{{@template}}" />
        </div>
    </div>

    <div id="footer">
        {{@lng.footer}}
    </div>
    </body>
</html>
