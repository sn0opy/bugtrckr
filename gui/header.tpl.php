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
            </ul>
            <br class="clearfix" />
        </div>
	

        <div id="content">
			<div id="innerContentLOL">

				<F3:check if="{@FAILURE}">
					<F3:true>
						<div id="failure">
							{@FAILURE}
						</div>
					</F3:true>
				 </F3:check>
