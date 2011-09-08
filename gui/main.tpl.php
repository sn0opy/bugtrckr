<!doctype html>
<html>
	<head>
        <meta charset="UTF-8" />
		<link href="{{@BASE}}/gui/css/style.css" rel="stylesheet" type="text/css" />
		<title>{{@pageTitle}} - Bugtrckr</title>
        
        <script type="text/javascript" src="{{@BASE}}/gui/js/jquery.js"></script>
        <script type="text/javascript" src="{{@BASE}}/gui/js/jquery.tablesorter.js"></script>
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
            
            $('#delSearch').click(function() {
               $('#searchInput').val("");
               $('#searchForm').submit();
            });
            
            $('.sortable').tablesorter({
                sortList: [[5,1]] 
            });
            
            $('.showLayer').click(function() {
               $('#darkBg').show();
               $('.layer').fadeIn();
               return false;
            });
            
            $('.closeButton').click(function() {
                $('.layer').fadeOut();
                $('#darkBg').hide();
                return false;
            });
            
            $('.succes').live(function() {
                $(this).fadeOut();
            });
        }); 
        </script>
	</head>

	<body>
        <div id="darkBg"></div>
        <div id="head">
   			<h1><a href="{{@BASE}}/">Bugtrckr</a></h1>
            <div id="menu">
                <ul>
                    <li><a href="{{@BASE}}/" {{@onpage=='start'?'class="menuActive"':''}}>{{@lng.home}}</a></li>
                    <F3:check if="{{@SESSION.project}}">
                        <F3:true>
                            <li><a href="{{@BASE}}/tickets" {{@onpage=='tickets'?'class="menuActive"':''}}>{{@lng.tickets}}</a></li>
                            <li><a href="{{@BASE}}/roadmap" {{@onpage=='roadmap'?'class="menuActive"':''}}>{{@lng.roadmap}}</a></li>
                            <li><a href="{{@BASE}}/timeline" {{@onpage=='timeline'?'class="menuActive"':''}}>{{@lng.timeline}}</a></li>
							<li><a href="{{@BASE}}/wiki" {{@onpage=='wiki'?'class="menuActive"':''}}>{{@lng.wiki}}</a></li>
                            <F3:check if="{{@SESSION.user}}">
                                <F3:true>
                                    <li><a href="{{@BASE}}/project/settings" {{@onpage=='settings'?'class="menuActive"':''}}>{{@lng.settings}}</a></li>
                                </F3:true>
                            </F3:check>
                        </F3:true>
                    </F3:check>
                    <F3:check if="{{@SESSION.user}}">
                        <F3:false>
                            <li><a href="{{@BASE}}/user/new" {{@onpage=='registration'?'class="menuActive"':''}}>{{@lng.registration}}</a></li>
                        </F3:false>
                    </F3:check>                
                    <li class="project_selector">
                        <form method="post" action="{{@BASE}}/project/select">
                            <select name="project" size="1" onBlur="submit();">
                                <F3:check if="{{@SESSION.user.hash}}">
                                    <option value="new">{{@lng.newProject}}</option>
                                </F3:check>
                                <F3:repeat group="{{@projects}}" value="{{@project}}">
                                    <option value="{{@project->hash}}" {{(@project->hash == @SESSION.project)?'selected="selected"':''}}>{{@project->name}}</option>
                                </F3:repeat>
                            </select>
                        </form>
                    </li>
                    <F3:check if="{{@SESSION.user}}">
                        <F3:true>
                            <li class="alignright llfix">{{@lng.loggedInAs}} <a href="{{@BASE}}/user/{{@SESSION.user->name}}" class="normLink"><strong class="normalText">{{@SESSION.user->name}}</strong></a> [<a href="/{{@BASE}}user/logout" class="normalText normLink">{{@lng.logout}}</a>]</li>
                        </F3:true>
                        <F3:false>
                            <li class="alignright llfix"><a href="{{@BASE}}/user/login" class="normLink showLayer">{{@lng.login}}</a></li>
                        </F3:false>
                    </F3:check>
                </ul>
                <br class="clearfix" />
            </div>
        </div>
        
        <F3:check if="{{@installWarning && @RELEASE}}">
            <div class="message warning">{{@lng.warningInstallFiles}}</div>
        </F3:check>
        
        <F3:check if="{{!@SESSION.user}}">
            <div id="login" class="layer">
                <h3 class="floatleft">{{@lng.login}}</h3>
                <a class="closeButton" href="#">×</a>

                <form action="{{@BASE}}/user/login" method="post" class="clearfix">
                    <div class="formRow">
                        <div class="formLabel">{{@lng.email}}: </div>
                        <div class="formValue"><input type="text" name="email" /></div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel">{{@lng.password}}: </div>
                        <div class="formValue"><input type="password" name="password" /></div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel"><input type="submit" value="{{@lng.login}}" /></div>
                        <div class="formValue"><a href="{{@BASE}}/user/new">{{@lng.noaccount}}</a></div>
                    </div>
                </form>
                <br class="clearfix" />
            </div>
        </F3:check>

        <div id="content">
			<div id="innerContentLOL">
                <F3:check if="{{@SESSION.FAILURE}}">
                    <div class="failure message">
                        <p>{{@SESSION.FAILURE}}</p>
                    </div>
                </F3:check>
                <F3:check if="{{@SESSION.SUCCESS}}">
                    <div class="success message">
                        <p>{{@SESSION.SUCCESS}}</p>
                    </div>
                </F3:check>
                <F3:include href="{{@template}}" />
            </div>
        </div>
        
        <div id="footer">
            {{@lng.footer}}
        </div>
	</body>
</html>
