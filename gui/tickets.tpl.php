<h2>Tickets</h2>

	<img src="/{@BASE}img/new.png" height="20px"/>

<table class="overview">
	<tr>
		<th><a href="/{@BASE}tickets/id">{@ID}</a></th>
		<th><a href="/{@BASE}tickets/title">{@TITLE}</a></th>
		<th><a href="/{@BASE}tickets/state">{@STATUS}</a></th>
		<th><a href="/{@BASE}tickets/created">{@CREATED}</a></th>
		<th>{@OWNER}</th>
	</tr>

	<F3:repeat group="{@tickets}" key="{@i}" value="{@ticket}">
	<tr class="tr{@i%2}">
		<td>{@ticket.id}</td>
		<td><a href="/{@BASE}ticket/{@ticket.id}">{@ticket.title}</a></td>
		<td>{@ticket.state}</td>
		<td>{@ticket.created}</td>
		<td>{@ticket.owner}</td>
	</tr>
	</F3:repeat>
</table>
