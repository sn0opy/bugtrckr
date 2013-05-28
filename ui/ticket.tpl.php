<h2 class="floatleft">{{@lng.ticket}} › {{@ticket->title}}</h2>

<a href="/#editIssue" data-toggle="modal" class="btn btn-primary floatright">{{@lng.editTicket}}</a>

<br class="clearfix" />

<div class="hero-unit hero-ticket">
    <table class="ticket">
        <tr>
            <th>{{@lng.status}}</th>
            <td>{{Helper::getName('states', @ticket->state)}}</td>
            <th>{{@lng.created}}</th>
            <td>{{date('d.m.Y H:i', @ticket->created)}}</td>
        </tr>
        <tr>
            <th>{{@lng.priority}}</th>
            <td>{{Helper::getName('priorities', @ticket->priority)}}</td>
            <th>{{@lng.assignedTo}}</th>
            <td><a href="/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
        </tr>
        <tr>
            <th>{{@lng.owner}}</th>
            <td><a href="/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
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
                <a href="/milestone/{{@ticket->milestone}}">{{Helper::getMsName(@ticket->milestone)}}</a>
            </td>
            <th></th>
            <td></td>
        </tr>
    </table>

    <hr />

    <h3>{{@lng.description}}</h3>
    <p>{{helper::translateBBCode(@ticket->description)}}</p>

</div>

<div>
    <h3>Ticket Timeline</h3>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{{@lng.description}}</th>
                <th>{{@lng.changed}}</th>
                <th>{{@lng.by}}</th>
            </tr>
        </thead>
        <tbody>
        <repeat group="{{@activities}}" key="{{@i}}" value="{{@activity}}">
            <tr>
                <td>                    
                    {{@activity->description}}
                    <check if="{{@activity->changedFields > 0}}">
                        <ul class="changedFields">
                            <repeat group="{{@activity->changedFields}}" value="{{@changedField}}">
                                <check if="{{@changedField->field=='state'}}">
                                    <li>{{@lng.status}} {{@lng.changedfrom}} <strong>{{Helper::getName('states', @changedField->from)}}</strong> {{@lng.to}} <strong>{{Helper::getName('states', @changedField->to)}}</strong></li>
                                </check>
                                <check if="{{@changedField->field=='priority'}}">
                                    <li>{{@lng.theprioritychanged}} <strong>{{Helper::getName('priorities', @changedField->from)}}</strong> {{@lng.to}} <strong>{{Helper::getName('priorities', @changedField->to)}}</strong></li>
                                </check>
                                <check if="{{@changedField->field=='milestone'}}">
                                    <li>{{@lng.milestone}} {{@lng.changedfrom}} <strong>{{Helper::getMsName(@changedField->from)}}</strong> {{@lng.to}} <strong>{{Helper::getMsName(@changedField->to)}}</strong></li>
                                </check>
                                <check if="{{@changedField->field=='assigned'}}">
                                    <li>Ticket von <strong>{{Helper::getUserName(@changedField->from)}}</strong> an <strong>{{Helper::getUserName(@changedField->to)}}</strong> {{strtolower(@lng.assigned)}}</li>
                                </check>
                            </repeat>
                        </ul>
                    </check>
                    <check if="{{@activity->comment}}">
                        <br/><span class="acitivityCmnt">{{@lng.comment}}: <em>{{nl2br(@activity->comment)}}</em></span>
                    </check>
                </td>
                <td>{{date('d.m.Y H:i', @activity->changed)}}</td>
                <td><a href="/user/{{@activity->username}}">{{@activity->username}}</a></td>
            </tr>
        </repeat>
        </tbody>
    </table>
</div>

<check if="{{isset(@SESSION.user)}}">
  <div class="modal" id="editIssue">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="/ticket/{{@ticket->tickethash}}">

		    	<div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal">×</button>
    				<h3>{{@lng.editTicket}}</h3>
          </div>

		    	<div class="modal-body form-horizontal">
				    <div class="row">
    					<div class="formLabel">{{@lng.assignedTo}}</div>
		    			<div class="formValue">
				    		<select name="assigned" size="1">
						    	<option value=""></option>
    							<repeat group="{{@users}}" value="{{@user}}">
		    						<check if="{{@user->hash==@ticket->assigned}}">
				    					<true>
						    				<option value="{{@user->hash}}" selected="selected">{{@user->name}}</option>
								    	</true>
									    <false>
    										<option value="{{@user->hash}}">{{@user->name}}</option>
		    							</false>
				    			</repeat>
						    </select>
    					</div>
		    		</div>

				    <div class="row">
    					<div class="formLabel">{{@lng.milestone}}</div>
		      		<div class="formValue">
					    	<select name="milestone" size="1">
							    <repeat group="{{@milestones}}" value="{{@milestone}}">
    								<check if="{{@milestone->hash==@ticket->milestone}}">
		    							<true>
				    						<option value="{{@milestone->hash}}" selected="selected">{{@milestone->name}}</option>
						    			</true>
								    	<false>
										    <option value="{{@milestone->hash}}">{{@milestone->name}}</option>
    									</false>
		    					</repeat>
				    		</select>
					    </div>
    				</div>               

		    		<div class="row">
				    	<div class="formLabel">{{@lng.status}}</div>
    					<div class="formValue">
		    				<select name="state" size="1">
				    			<repeat group="{{@lng.states}}" key="{{@i}}" value="{{@state}}">
						    		<check if="{{@state.id == @ticket->state}}">
								    	<true><option value="{{@state.id}}" selected="selected">{{@state.name}}</option></true>
    									<false><option value="{{@state.id}}">{{@state.name}}</option></false>
		    						</check>
				    			</repeat>
						    </select>
    					</div>
		    		</div>

				    <div class="row">
    					<div class="formLabel">{{@lng.priority}}</div>
		    			<div class="formValue">
				    		<select name="priority" size="1">
						    	<repeat group="{{@lng.priorities}}" key="{{@i}}" value="{{@priority}}">
								    <check if="{{@priority.id == @ticket->priority}}">
    									<true><option value="{{@priority.id}}" selected="selected">{{@priority.name}}</option></true>
		    							<false><option value="{{@priority.id}}">{{@priority.name}}</option></false>
				    				</check>
						    	</repeat>
    						</select>
		    			</div>
				    </div>

    				<div class="row">
		    			<div class="formLabel">{{@lng.comment}}</div>
				    	<div class="formValue">
						    <textarea name="comment" class="ticketComment"></textarea>
    					</div>
		    		</div>
          </div>

			    <div class="modal-footer">
				    <input type="submit" value="{{@lng.edit}}" class="btn btn-primary" />
          </div>

        </form>
      </div>
    </div>
	</div>
</check>
