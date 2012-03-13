<h2>{{@lng.tickets}} › {{@ticket->title}} <span class="hash">#{{@ticket->tickethash}}</span></h2>

<div class="ticket">
    <table class="ticket">
        <tr>
            <th>{{@lng.status}}</th>
            <td>{{\misc\Helper::getName('states', @ticket->state)}}</td>
            <th>{{@lng.created}}</th>
            <td>{{date('d.m.Y H:i', @ticket->created)}}</td>
        </tr>
        <tr>
            <th>{{@lng.priority}}</th>
            <td>{{\misc\Helper::getName('priorities', @ticket->priority)}}</td>
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
                {{\misc\Helper::getMsName(@ticket->milestone)}}
            </td>
            <th></th>
            <td></td>
        </tr>
    </table>

    <hr />

    <h3>{{@lng.description}}</h3>
    <p>{{\misc\helper::translateBBCode(@ticket->description)}}</p>

</div>

<div class="ticket_timeline">
    <h3>Ticket Timeline</h3>

    <table class="zebra pinOnTop">
        <thead>
            <tr>
                <th>{{@lng.description}}</th>
                <th>{{@lng.changed}}</th>
                <th>{{@lng.by}}</th>
            </tr>
        </thead>
        <tbody>
        <F3:repeat group="{{@activities}}" key="{{@i}}" value="{{@activity}}">
            <tr>
                <td>                    
                    {{@activity->description}}
                    <F3:check if="{{@activity->changedFields > 0}}">
                        <ul class="changedFields">
                            <F3:repeat group="{{@activity->changedFields}}" value="{{@changedField}}">
                                <F3:check if="{{@changedField->field=='state'}}">
                                    <li>{{@lng.status}} {{@lng.changedfrom}} <strong>{{\misc\Helper::getName('states', @changedField->from)}}</strong> {{@lng.to}} <strong>{{\misc\Helper::getName('states', @changedField->to)}}</strong></li>
                                </F3:check>
                                <F3:check if="{{@changedField->field=='priority'}}">
                                    <li>{{@lng.theprioritychanged}} <strong>{{\misc\Helper::getName('priorities', @changedField->from)}}</strong> {{@lng.to}} <strong>{{\misc\Helper::getName('priorities', @changedField->to)}}</strong></li>
                                </F3:check>
                                <F3:check if="{{@changedField->field=='milestone'}}">
                                    <li>{{@lng.milestone}} {{@lng.changedfrom}} <strong>{{\misc\Helper::getMsName(@changedField->from)}}</strong> {{@lng.to}} <strong>{{\misc\Helper::getMsName(@changedField->to)}}</strong></li>
                                </F3:check>
                                <F3:check if="{{@changedField->field=='assigned'}}">
                                    <li>Ticket von <strong>{{\misc\Helper::getUserName(@changedField->from)}}</strong> an <strong>{{\misc\Helper::getUserName(@changedField->to)}}</strong> {{strtolower(@lng.assigned)}}</li>
                                </F3:check>
                            </F3:repeat>
                        </ul>
                    </F3:check>
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
                        <select name="assigned" size="1">
                            <option value=""></option>
                            <F3:repeat group="{{@users}}" value="{{@user}}">
                                <F3:check if="{{@user->hash==@ticket->assigned}}">
                                    <F3:true>
                                        <option value="{{@user->hash}}" selected="selected">{{@user->name}}</option>
                                    </F3:true>
                                    <F3:false>
                                        <option value="{{@user->hash}}">{{@user->name}}</option>
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
                                <F3:check if="{{@milestone->hash==@ticket->milestone}}">
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
                                <F3:check if="{{@state.id == @ticket->state}}">
                                    <F3:true><option value="{{@state.id}}" selected="selected">{{@state.name}}</option></F3:true>
                                    <F3:false><option value="{{@state.id}}">{{@state.name}}</option></F3:false>
                                </F3:check>
                            </F3:repeat>
                        </select>
                    </div>
                </div>
                
                <div class="formRow">
                    <div class="formLabel">{{@lng.priority}}</div>
                    <div class="formValue">
                        <select name="priority" size="1">
                            <F3:repeat group="{{@lng.priorities}}" key="{{@i}}" value="{{@priority}}">
                                <F3:check if="{{@priority.id == @ticket->priority}}">
                                    <F3:true><option value="{{@priority.id}}" selected="selected">{{@priority.name}}</option></F3:true>
                                    <F3:false><option value="{{@priority.id}}">{{@priority.name}}</option></F3:false>
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
