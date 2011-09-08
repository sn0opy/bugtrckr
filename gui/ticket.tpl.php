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
            <td>
                {{* this ABSOLUTELY sucks, but works, sorry (TODO) *}}
                <F3:repeat group="{{@milestones}}" value="{{@milestone}}">
                    <F3:check if="{{@ticket->milestone==@milestone->id}}">
                        {{@milestone->name}}
                    </F3:check>
                </F3:repeat>
            </td>
            <th></th>
            <td></td>
        </tr>
    </table>

    <hr />

    <h3>{{@lng.description}}</h3>
    <p>{{nl2br(@ticket->description)}}</p>

</div>

<div class="ticket_timeline">
    <h3>Ticket Timeline</h3>

    <table>
        <thead>
        <tr>
            <th>{{@lng.description}}</th>
            <th>{{@lng.changed}}</th>
            <th>{{@lng.by}}</th>
        </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>
</div>

<F3:check if="{{@SESSION.user}}">
    <F3:true>
        <div class="editTicket">
            <form method="post" action="{{@BASE}}/ticket/{{@ticket->tickethash}}">

                <div class="formRow">
                    <div class="formLabel">{{@lng.assignedTo}}</div>
                    <div class="formValue">
                        <select name="userId" size="1">
                            <option value=""></option>
                            <F3:repeat group="{{@users}}" value="{{@user}}">
                                <F3:check if="{{@user->id==@ticket->assigned}}">
                                    <F3:true>
                                        <option value="{{@user->id}}" selected="selected">{{@user->name}}</option>
                                    </F3:true>
                                    <F3:false>
                                        <option value="{{@user->id}}">{{@user->name}}</option>
                                    </F3:false>
                            </F3:repeat>
                        </select>
                    </div>
                </div>
                
                 <div class="formRow">
                    <div class="formLabel">{{@lng.milestone}}</div>
                    <div class="formValue">
                        <select name="milestone" size="1">
                            <F3:repeat group="{{@milestones}}" value="{{@milestone}}">
                                <F3:check if="{{@milestone->id==@ticket->milestone}}">
                                    <F3:true>
                                        <option value="{{@milestone->hash}}" selected="selected">{{@milestone->name}}</option>
                                    </F3:true>
                                    <F3:false>
                                        <option value="{{@milestone->hash}}">{{@milestone->name}}</option>
                                    </F3:false>
                            </F3:repeat>
                        </select>
                    </div>
                </div>               

                <div class="formRow">
                    <div class="formLabel">{{@lng.status}}</div>
                    <div class="formValue">
                        <select name="state" size="1">
                            <F3:repeat group="{{@lng.states}}" key="{{@i}}" value="{{@state}}">
                                <F3:check if="{{@state.name == @ticket->statusname}}">
                                    <F3:true><option value="{{@state.id}}" selected="selected">{{@state.name}}</option></F3:true>
                                    <F3:false><option value="{{@state.id}}">{{@state.name}}</option></F3:false>
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