<h2 class="floatleft">{{@lng.tickets}}</h2>

<F3:check if="{{@getPermission('iss_addIssues')}}">
    <button type="button" class="floatright"
        onclick="document.getElementById('add').style.display = 'block'" >
        {{@lng.addticket}}
    </button>
</F3:check>

<div class="add" id="add">
    <h3 class="floatleft">{{@lng.addticket}}</h3>
    <a class="closeButton" href="#" onclick="document.getElementById('add').style.display = 'none'; return false;">
		X
	</a>

	<form method="POST" action="/{{@BASE}}ticket/">
		<div class="formRow">
			<div class="formLabel">{{@lng.title}}</div>
			<div class="formValue"><input type="text" name="title" /></div>
		</div>

		<div class="formRow">
			<div class="formLabel">{{@lng.description}}</div>
			<div class="formValue">
				<textarea name="description"></textarea>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{{@lng.category}}</div>
			<div class="formValue"><input type="text" name="category" /></div>
		</div>

		<div class="formRow">
			<div class="formLabel">{{@lng.type}}</div>
			<div class="formValue">
				<select name="type" size="1">
					<option value="1">{{@lng.bug}}</option>
					<option value="2">{{@lng.feature}}</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{{@lng.priority}}</div>
			<div class="formValue">
				<select name="priority" size="1">
					<option value="1">1 - {{@lng.veryhigh}}</option>
					<option value="2">2 - {{@lng.high}}</option>
					<option value="3" selected="selected">3 - {{@lng.normal}}</option>
					<option value="4">4 - {{@lng.low}}</option>
					<option value="5">5 - {{@lng.verylow}}</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{{@lng.milestone}}</div>
			<div class="formValue">
				<select name="milestone" size="1">
				<F3:repeat group="{{@milestones}}" value="{{@milestone}}">
                    <option value="{{@milestone->id}}">{{@milestone->name}}</option>
				</F3:repeat>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel"></div>
			<div class="formValue">
				<input type="submit" value="{{@lng.submit}}" />
			</div>
		</div>
	</form>
	<br class="clearfix" />
</div>

<table class="overview">
    <thead>
        <tr>
            <th><a href="/{{@BASE}}tickets/id">#</a></th>
			<th><a href="/{{@BASE}}tickets/title">{{@lng.title}}</a></th>
			<th><a href="/{{@BASE}}tickets/type">{{@lng.type}}</a></th>
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
                <a href="/{{@BASE}}ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a>
			</td>
			<td class="type">{{@ticket->typename}}</td>
            <td class="state">{{@ticket->statusname}}</td>
            <td class="priority">{{@ticket->priorityname}}</td>
            <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
            <td class="owner"><a href="/{{@BASE}}user/{{@ticket->name}}">{{@ticket->name}}</a></td>
        </tr>
        </F3:repeat>
    </tbody>
</table>
