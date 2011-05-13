<h2>{{@lng.user}} › {{@user->name}}</h2>

<h3>{{@lng.informations}}</h3>
<div class="formRow">
    <div class="formLabel">{{@lng.name}}: </div>
    <div class="formValue">{{@user->name}}</div>
</div>

<h3>{{@lng.tickets}}:</h3>
<table class="overview">
    <thead>
	<tr>
		<th><a href="/{{@BASE}}tickets/id">#</a></th>
		<th><a href="/{{@BASE}}tickets/title">{{@lng.title}}</a></th>
		<th><a href="/{{@BASE}}tickets/state">{{@lng.status}}</a></th>
		<th><a href="/{{@BASE}}tickets/priority">{{@lng.priority}}</a></th>
		<th><a href="/{{@BASE}}tickets/created">{{@lng.created}}</a></th>
		<th>{{@lng.owner}}</th>
    </tr>
    </thead>

    <tbody>
        <F3:repeat group="{{@tickets}}" key="{{@i}}" value="{{@ticket}}">
        <tr class="tr{{@i%2}}">
            <td class="id">{{@i+1}}</td>
            <td class="title">
                <a href="/{{@BASE}}ticket/{{@ticket->hash}}">{{@ticket->title}}</a>
            </td>
            <td class="state">{{@ticket->state}}</td>
            <td class="priority">{{@ticket->priority}}</td>
            <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
            <td class="owner"><a href="/{{@BASE}}user/{{@ticket->name}}">{{@ticket->name}}</a></td>
        </tr>
        </F3:repeat>
    </tbody>
</table>

