<h2>{{@lng.tickets}} › {{@ticket->title}} <span class="hash">#{{@ticket->tickethash}}</span></h2>

<div class="ticket">
    <table class="ticket">
        <tr>
            <th>{{@lng.status}}</th>
            <td>{{@ticket->statusname}}</td>
            <th>{{@lng.created}}</th>
            <td>{{date('d.m.Y H:i', @ticket->created)}}</td>
        </tr>

        <tr>
            <th>{{@lng.priority}}</th>
            <td>{{@ticket->priorityname}}</td>
            <th>{{@lng.assignedTo}}</th>
            <td><a href="{{@BASE}}/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
        </tr>

        <tr>
            <th>{{@lng.owner}}</th>
            <td><a href="{{@BASE}}/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
            <th></th>
            <td></td>
        </tr>

        <tr>
            <th>{{@lng.category}}</th>
            <td>{{@ticket->categoryname}}</td>
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

<div class="ticket_timeline">
    <h3>Ticket Timeline</h3>

    <table class="overview">
        <tr>
            <th>{{@lng.description}}</th>
            <th>{{@lng.changed}}</th>
            <th>{{@lng.by}}</th>
        </tr>
        <F3:repeat group="{{@activities}}" key="{{@i}}" value="{{@activity}}">
            <tr class="tr{{@i%2}}">
                <td>{{@activity->description}}
                    <F3:check if="{{@activity->comment}}">
                        <br/><span class="acitivityCmnt">{{@lng.comment}}: <em>{{nl2br(@activity->comment)}}</em></span>
                    </F3:check>
                </td>
                <td>{{date('d.m.Y H:i', @activity->changed)}}</td>
                <td><a href="{{@BASE}}/user/{{@activity->username}}">{{@activity->username}}</a></td>
            </tr>
        </F3:repeat>
    </table>
</div>

<F3:check if="{{@SESSION.user}}">
    <F3:true>
        <div class="editTicket">
            <form method="POST" action="{{@BASE}}/ticket/{{@ticket->tickethash}}">

                <div class="formRow">
                    <div class="formLabel">{{@lng.assignedTo}}</div>
                    <div class="formValue">
                        <select name="userId" size="1">
                            <F3:repeat group="{{@users}}" value="{{@user}}">
                                <option value="{{@user->id}}">{{@user->name}}</option>
                            </F3:repeat>
                        </select>
                    </div>
                </div>

                <div class="formRow">
                    <div class="formLabel">{{@lng.status}}</div>
                    <div class="formValue">
                        <select name="state" size="1">
                            <F3:repeat group="{{@states}}" key="{{@i}}" value="{{@state}}">
                                <F3:check if="{{@state->name == @ticket->statusname}}">
                                    <F3:true><option value="{{@state->id}}" selected="selected">{{@state->name}}</option></F3:true>
                                    <F3:false><option value="{{@state->id}}">{{@state->name}}</option></F3:false>
                                </F3:check>
                            </F3:repeat>
                        </select>
                    </div>
                </div>
                
                <div class="formRow">
                    <div class="formLabel">{{@lng.comment}}</div>
                    <div class="formValue">
                        <textarea name="comment" class="ticketComment"></textarea>
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