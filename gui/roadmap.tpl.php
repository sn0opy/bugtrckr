<h1>Roadmap</h1>

<button type="button">{@LANG.ADDMILESTONE}</button>

<F3:repeat group="{@road}" key="{@i}" value="{@item}">

<div class="milestone">
	<h2>{@item.milestone.name}</h2>
	
	<p>{@item.milestone.description}</p>

	<p class="info">{@item.ticketcount.0.count} {@LANG.TICKETSLEFT}</p>

	<ul class="sublist">
	<F3:repeat group="{@item.tickets}" key="{@j}" value="{@ticket}">
		<li><a href="/{@BASE}ticket/{@ticket.hash}">{@ticket.title}</a></li>
	</F3>
	</ul>	

</div>

</F3:repeat>
