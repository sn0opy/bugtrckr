<h2>{{@lng.tickets}} › {{@ticket.title}} <span class="hash">#{{@ticket.hash}}</span></h2>

<div class="ticket">
	<table class="ticket">
		<tr>
			<th>{{@lng.status}}</th>
			<td>{{@ticket->state}}</td>
			<th>{{@lng.created}}</th>
			<td>{{@ticket->created}}</td>
		</tr>

		<tr>
			<th>{{@lng.priority}}</th>
			<td>{{@ticket->priority}}</td>
			<th>{{@lng.assignedTo}}</th>
			<td><a href="/{{@BASE}}user/{{@ticket.assigned}}">{{@ticket->assigned}}</a></td>
		</tr>

		<tr>
			<th>{{@lng.owner}}</th>
			<td><a href="/{{@BASE}}user/{{@ticket->owner}}">{{@ticket->owner}}</a></td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{{@lng.category}}</th>
			<td>{{@ticket->category}}</td>
			<th></th>
			<td></td>
		</tr>

		<tr>
			<th>{{@lng.milestone}}</th>
			<td>{{@milestone->name}}</td>
			<th></th>
			<td></td>
		</tr>
	</table>

	<hr />

	<h3>{{@lng.description}}</h3>

	<p>
		{{nl2br(@ticket->description)}}
	</p>
</div>


<F3:check if="{{@SESSION.user}}">
	<F3:true>
    
    <div class="editTicket">
        <form method="POST" action="/{{@BASE}}ticket/{{@ticket->hash}}">

            <div class="formRow">
                <div class="formLabel">{{@lng.assignedTo}}</div>
                <div class="formValue">
                    <select name="userId" size="1">
                    <F3:repeat group="{{@users}}" value="{{@user}}">
                        <option value="{{@user.id}}">{{@user.name}}</option>
                    </F3:repeat>
                    </select>
                </div>
            </div>

            <div class="formRow">
                <div class="formLabel">{{@lng.status}}</div>
                <div class="formValue">
                    <select name="state" size="1">
                    <F3:repeat group="{{@ticket_state}}" key="{{@i}}" value="{{@state}}">
                        <option value="{{@i}}">{{@state}}</option>
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
    </div>
	</F3:true>
</F3:check>
