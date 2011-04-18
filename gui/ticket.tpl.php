<h2>{@ticket.title}</h2>

<div class="ticket">
	<table class="ticket">
		<tr>
			<th>{@lng.status}</th>
			<td>{@ticket.state}</td>
			<th>{@lng.created}</th>
			<td>{@ticket.created}</td>
		</tr>

		<tr>
			<th>{@lng.priority}</th>
			<td>{@ticket.priority}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@lng.owner}</th>
			<td><a href="/{@BASE}user/{@ticket.owner}">{@ticket.owner}</a></td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@lng.category}</th>
			<td>{@ticket.category}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@lng.milestone}</th>
			<td>{@milestone.name}</td>
			<th></th>
			<td></td>
		</tr>
	</table>

	<hr noshade="noshade" />

	<h3>{@lng.description}</h3>

	<p>
		{nl2br(htmlentities(@ticket.description))}
	</p>
</div>


