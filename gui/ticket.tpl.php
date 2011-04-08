<h1>{@ticket.title}</h1>

<div class="ticket">
	<table class="ticket">
		<tr>
			<th>{@LANG.CREATED}</th>
			<td>{@ticket.created}</td>
			<th>{@LANG.OWNER}</th>
			<td>{@ticket.owner}</td>
		</tr>

		<tr>
			<th>{@LANG.STATUS}</th>
			<td>{@ticket.state}</td>
			<th></th>
			<td></td>
		</tr>
	</table>
</div>

<h2>{@DESCRIPTION}</h2>

<p>
	{@ticket.description}
</p>
