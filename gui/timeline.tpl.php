<h2>{{@lng.timeline}}</h2>

<table class="overview">
	<tr>
		<th>{{@lng.description}}</th>
        <th>{{@lng.changed}}</th>
        <th>{{@lng.changedby}}</th>
	</tr>
<F3:repeat group="{{@activities}}" key="{{@i}}" value="{{@activity}}">
	<tr class="tr{{@i%2}}">
		<td>{{@activity->description}}</td>
		<td>{{date('d.m.Y H:i', @activity->changed)}}</td>
		<td><a href="/{{@BASE}}user/{{@activity->username}}">{{@activity->username}}</a></td>
	</tr>
</F3:repeat>
</table>
