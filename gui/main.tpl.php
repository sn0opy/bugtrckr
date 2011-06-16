<!doctype html>
<html>
	<head>
        <meta charset="UTF-8" />
		<link href="{{@BASE}}/gui/style.css" rel="stylesheet" type="text/css" />
		<title>{{@pageTitle}} - {{@title}}</title>
        
        <script type="text/javascript" src="{{@BASE}}/gui/js/jquery.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {              
            $("a.tab").click(function() {
                $(".active").removeClass("active");  
                $(this).parent().addClass("active");  
                $(".tabContent").hide();  
                var content_show = $(this).attr("title");  
                $("#"+content_show).show();  
                return false;
            });  
        }); 
        </script>
	</head>

	<body>
        <div id="head">
   			<h1>{{@title}}</h1>
        </div>
        <div id="menu">
            <ul>
                <li><a href="{{@BASE}}/">{{@lng.home}}</a></li>
                <F3:check if="{{@SESSION.project}}">
                    <F3:true>
                        <li><a href="{{@BASE}}/tickets">{{@lng.tickets}}</a></li>
                        <li><a href="{{@BASE}}/roadmap">{{@lng.roadmap}}</a></li>
                        <li><a href="{{@BASE}}/timeline">{{@lng.timeline}}</a></li>
                        <F3:check if="{{@SESSION.user}}">
                            <F3:true>
                                <li><a href="{{@BASE}}/project/settings">{{@lng.settings}}</a></li>
                            </F3:true>
                        </F3:check>
                    </F3:true>
                </F3:check>
                <F3:check if="{{@SESSION.user}}">
                    <F3:false>
                        <li><a href="{{@BASE}}/user/new">{{@lng.registration}}</a></li>
                    </F3:false>
                </F3:check>                
				<li>
					<form method="post" action="{{@BASE}}/project/select">
						<select name="project" size="1" onclick="submit()">
							<F3:repeat group="{{@projects}}" value="{{@project}}">
                                <option value="{{@project->hash}}" {{(@project->id == @SESSION.project)?'selected="selected"':''}}>{{@project->name}}</option>
							</F3:repeat>
						</select>
					</form>
				</li>
                <F3:check if="{{@SESSION.user}}">
                    <F3:true>
                        <li class="alignright">Eingeloggt als <a href="{{@BASE}}/user/{{@SESSION.user->name}}" class="normLink"><strong class="normalText">{{@SESSION.user->name}}</strong></a> [<a href="/{{@BASE}}/user/logout" class="normalText normLink">{{@lng.logout}}</a>]</li>
                    </F3:true>
                    <F3:false>
                        <li class="alignright"><a href="{{@BASE}}/user/login" onclick="document.getElementById('login').style.display = 'block'; return false" class="normLink">{{@lng.login}}</a></li>
                    </F3:false>
                </F3:check>
			</ul>
            <br class="clearfix" />

            <div id="login">
                <h3 class="floatleft">{{@lng.login}}</h3>
                <a class="closeButton" href="#" onclick="document.getElementById('login').style.display = 'none'">
                    X
                </a>

                <form action="{{@BASE}}/user/login" method="post">
                    <div class="formRow">
                        <div class="formLabel">{{@lng.email}}: </div>
                        <div class="formValue"><input type="text" name="email" /></div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel">{{@lng.password}}: </div>
                        <div class="formValue"><input type="password" name="password" /></div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel"> </div>
                        <div class="formValue"><a href="{{@BASE}}/user/new">{{@lng.noaccount}}</a></div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel"> </div>
                        <div class="formValue"><input type="submit" value="{{@lng.login}}" /></div>
                    </div>
                </form>
                <br class="clearfix" />
            </div>
        </div>

        <div id="content">
			<div id="innerContentLOL">
                <F3:check if="{{@FAILURE}}">
                    <F3:true>
                        <h2>{{@lng.error}}</h2>
                        <div id="failure">
                            <p>{{@FAILURE}}</p>
                        </div>
                    </F3:true>
                    <F3:false>
						<F3:check if="{{@SESSION.SUCCESS}}">
							<F3:true>
							<div id="success">
								<p>{{@SESSION.SUCCESS}}</p>
							</div>
							</F3:true>
						</F3:check>

                        <F3:include href="{{@template}}" />
                    </F3:false>
                </F3:check>
            </div>
        </div>

        <div id="footer">
            {{@lng.footer}}
        </div>
	</body>
</html>
