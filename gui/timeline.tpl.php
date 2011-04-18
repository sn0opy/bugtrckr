<h2>{@lng.timeline}</h2>

<table class="overview">
	<tr>
		<th>{@lng.description}</th><th>{@lng.changed}</th><th>{@lng.changedby}</th>
	</tr>
<F3:repeat group="{@activities}" key={@i} value="{@activity}">
	<tr class="tr{@i%2}">
		<td>{htmlentities(@activity.description)}</td>
		<td>{htmlentities(@activity.changed)}</td>
		<td><a href="/{@BASE}user/{htmlentities(@activity.user)}">{htmlentities(@activity.user)}</a></td>
	</tr>
</F3:repeat>
</table>
