<html>
	<head>
		<link 	ref="stylesheet" href="/{@BASE}gui/style.css" 
				rel="stylesheet" typ="text/css" />

		<title>{@title}</title>

	</head>

	<body>
        <div id="head">

        </div>

        <div id="menu">
            <ul>
                <li><a href="/{@BASE}">Home</a></li>
                <li><a href="/{@BASE}tickets">Tickets</a></li>
                <li><a href="/{@BASE}roadmap">Roadmap</a></li>
				<li><a href="/{@BASE}timeline">Timeline</a></li>
				<li><a href="/{@BASE}user/new">Registration</a></li>

				<li>
					<form method="POST" action="/{@BASE}project/select">
						<select name="project" size="1" onchange="submit()">
							<option></option>
							<F3:repeat group="{@projects}" value="{@project}">
								<F3:check if="{@project.id == @SESSION.project}">
									<F3:true>
										<option value="{@project.hash}"
												checked="checked">
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
			</ul>
            <br class="clearfix" />
        </div>
	

        <div id="content">
			<div id="innerContentLOL">

