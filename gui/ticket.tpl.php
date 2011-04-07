<h2>{@ticket.title}</h2>

<div class="ticket">
	<table class="ticket">
		<tr>
			<th>{@CREATED}</th>
			<td>{@ticket.created}</td>
			<th>{@OWNER}</th>
			<td>{@ticket.owner}</td>
		</tr>

		<tr>
			<th>{@STATUS}</th>
			<td>{@ticket.state}</td>
			<th></th>
			<td></td>
		</tr>
	</table>
</div>

<h3>{@DESCRIPTION}</h3>

<p>
	{@ticket.description}
</p>
