<h1>Timeline</h1>

<table class="overview">
	<tr>
		<th>{@LANG.DESCRIPTION}</th><th>{@LANG.CHANGED}</th><th>{@LANG.CHANGEDBY}</th>
	</tr>
<F3:repeat group="{@activities}" value="{@activity}">
	<tr>
		<td>{@activity.description}</td>
		<td>{@activity.changed}</td>
		<td>{@activity.user}</td>
	</tr>
</F3:repeat>
</table>
