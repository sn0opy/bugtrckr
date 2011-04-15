<html>
	<head>
		<link 	ref="stylesheet" href="/{@BASE}gui/style.css" 
				rel="stylesheet" typ="text/css" />

		<title>{@title}</title>

	</head>

	<body>
        <div id="head">
            <h1>bugtrckr</h1>
        </div>

        <div id="menu">
            <ul>
                <li><a href="/{@BASE}">{@lng.home}</a></li>
                <li><a href="/{@BASE}tickets">{@lng.tickets}</a></li>
                <li><a href="/{@BASE}roadmap">{@lng.roadmap}</a></li>
				<li><a href="/{@BASE}timeline">{@lng.timeline}</a></li>
				<li><a href="/{@BASE}user/new">{@lng.registration}</a></li>

				<li>
					<form method="POST" action="/{@BASE}project/select">
						<select name="project" size="1" onchange="submit()">
							<F3:repeat group="{@projects}" value="{@project}">
								<F3:check if="{@project.id == @SESSION.project}">
									<F3:true>
										<option value="{@project.hash}"
												selected="selected">
											{@project.name}
										</option>
									</F3:true>
									<F3:false>
										<option value="{@project.hash}">
											{@project.name}
										</option>
									</F3:false>
								</F3:check>
							</F3:repeat>
						</select>
					</form>
				</li>
                <F3:check if="{@SESSION.userId}">
                    <F3:true>
                        <li class="alignright">Eingeloggt als <a href="/{@BASE}user/{@SESSION.userHash}"><strong>{htmlentities(@SESSION.userName)}</strong></a> [<a href="/{@BASE}user/logout">{@lng.logout}</a>]</li>
                    </F3:true>
                    <F3:false>
                        <li class="alignright"><a href="/{@BASE}user/login">{@lng.login}</a></li>
                    </F3:false>
                </F3:check>
			</ul>
            <br class="clearfix" />
        </div>
	

        <div id="content">
			<div id="innerContentLOL">

