<h1>{@lng.timeline}</h1>

<table class="overview">
	<tr>
		<th>{@lng.description}</th><th>{@lng.changed}</th><th>{@lng.changedby}</th>
	</tr>
<F3:repeat group="{@activities}" value="{@activity}">
	<tr>
		<td>{@activity.description}</td>
		<td>{@activity.changed}</td>
		<td>{@activity.user}</td>
	</tr>
</F3:repeat>
</table>
