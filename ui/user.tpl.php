<h2>{{@lng.user}} › {{@user->name}}</h2>

<h3>{{@lng.tickets}}:</h3>
<check if="{{count({@tickets)}}">
	<true>
		<table class="table table-striped sortable table-bordered">
			<thead>
				<tr>
					<th>{{@lng.title}}</th>
					<th>{{@lng.type}}</th>
					<th>{{@lng.status}}</th>
					<th>{{@lng.priority}}</th>
					<th>{{@lng.created}}</th>
					<th>{{@lng.owner}}</th>
					<th>{{@lng.assigned}}</th>
				</tr>
			</thead>

			<tbody>
				<repeat group="{{@tickets}}" value="{{@ticket}}">
				<tr>
					<td class="title"><a href="/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a></td>
					<td class="type">{{helper::getName('types', @ticket->type)}}</td>
					<td class="state"><span class="color{{@ticket->state}}">{{Helper::getName('states', @ticket->state)}}</span></td>
					<td class="priority"><span style="display: none;">{{@ticket->priority}}</span><span class="color{{@ticket->priority}}">{{Helper::getName('priorities', @ticket->priority)}}</span></td>
					<td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
					<td class="owner"><a href="/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
					<td class="owner"><a href="/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
				</tr>
				</repeat>
			</tbody>
		</table>
	</true>
	<false>
		<p class="alert alert-info">{{@lng.noTickets}}</p>
	</false>
</check>
