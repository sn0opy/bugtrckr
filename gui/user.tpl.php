<h2>{{@lng.user}} › {{@user->name}}</h2>

<h3>{{@lng.tickets}}:</h3>
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
        <F3:repeat group="{{@tickets}}" value="{{@ticket}}">
        <tr>
            <td class="title"><a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a></td>
			<td class="type">{{\misc\Helper::getName('types', @ticket->type)}}</td>
            <td class="state"><span class="color{{@ticket->state}}">{{\misc\Helper::getName('states', @ticket->state)}}</span></td>
            <td class="priority"><span style="display: none;">{{@ticket->priority}}</span><span class="color{{@ticket->priority}}">{{\misc\Helper::getName('priorities', @ticket->priority)}}</span></td>
            <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
            <td class="owner"><a href="{{@BASE}}/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
            <td class="owner"><a href="{{@BASE}}/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
        </tr>
        </F3:repeat>
    </tbody>
</table>

