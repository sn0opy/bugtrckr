<h2 class="pull-left">{{@lng.tickets}}</h2>

<div class="pull-right">
	<form action="{{@BASE}}/search" method="post" id="searchForm" class="form-search">
		<input type="input" name="search" value="{{@SESSION.ticketSearch}}" id="searchInput" class="search-query input-medium" /> <input type="submit" value="{{@lng.search}}" class="btn" /> <F3:check if="{{@getPermission('iss_addIssues')}}"><button class="btn btn-primary" data-toggle="modal" href="#addIssue">{{@lng.addticket}}</button></F3:check>
	</form>
</div>

<F3:check if="{{count(@tickets)}}">
    <F3:true>
        <table class="sortable table-striped table table-bordered">
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
    </F3:true>
    <F3:false>
        <div class="alert alert-info clearfix">{{@lng.noTickets}}</div>
    </F3:false>
</F3:check>


<div class="modal hide" id="addIssue">
	<form method="post" action="{{@BASE}}/ticket/">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h3>{{@lng.addticket}}</h3>
		</div>
		<div class="modal-body">
			<div class="formRow">
				<div class="formLabel">{{@lng.title}}</div>
				<div class="formValue"><input type="text" name="title" /></div>
			</div>

			<div class="formRow">
				<div class="formLabel">{{@lng.category}}</div>
				<div class="formValue">
					<select name="category" size="1">
						<F3:repeat group="{{@categories}}" value="{{@category}}">
							<option value="{{@category->hash}}">{{@category->name}}</option>
						</F3:repeat>
					</select>
				</div>
			</div>

			<div class="formRow">
				<div class="formLabel">{{@lng.type}}</div>
				<div class="formValue">
					<select name="type" size="1">
						<F3:repeat group="{{@lng.types}}" value="{{@type}}">
							<option value="{{@type.id}}">{{@type.name}}</option>
						</F3:repeat>
					</select>
				</div>
			</div>

			<div class="formRow">
				<div class="formLabel">{{@lng.priority}}</div>
				<div class="formValue">
					<select name="priority" size="1">
						<F3:repeat group="{{@lng.priorities}}" value="{{@priority}}">
							<option value="{{@priority.id}}" {{(@priority.id==3)?'selected="selected"':''}}>{{@priority.id}} - {{@priority.name}}</option>
						</F3:repeat>
					</select>
				</div>
			</div>

			<div class="formRow">
				<div class="formLabel">{{@lng.milestone}}</div>
				<div class="formValue">
					<select name="milestone" size="1">
					<F3:repeat group="{{@milestones}}" value="{{@milestone}}">
						<option value="{{@milestone->hash}}">{{@milestone->name}}</option>
					</F3:repeat>
					</select>
				</div>
			</div>
			
			<div class="formRow">
				<div class="formLabel">{{@lng.description}}</div>
				<div class="formValue">
					<textarea name="description" class="ticketComment"></textarea>
				</div>
			</div>
			
		</div>
		<div class="modal-footer">
			<input type="submit" value="{{@lng.add}}" class="btn btn-primary" />
		</div>
	</form>
</div>