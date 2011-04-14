<h1>{@ticket.title}</h1>

<div class="ticket">
	<table class="ticket">
		<tr>
			<th>{@LANG.STATUS}</th>
			<td>{@ticket.state}</td>
			<th>{@LANG.CREATED}</th>
			<td>{@ticket.created}</td>
		</tr>

		<tr>
			<th>{@LANG.PRIORITY}</th>
			<td>{@ticket.priority}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@LANG.OWNER}</th>
			<td>{@ticket.owner}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@LANG.CATEGORY}</th>
			<td>{@ticket.category}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{@LANG.MILESTONE}</th>
			<td>{@milestone.name}</td>
			<th></th>
			<td></td>
		</tr>
	</table>

	<hr noshade="noshade" />

	<h2>{@LANG.DESCRIPTION}</h2>

	<p>
		{@ticket.description}
	</p>
</div>


