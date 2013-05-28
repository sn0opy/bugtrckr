<h2 class="floatleft">{{@lng.tickets}}</h2>

<check if="{{@getPermission('iss_addIssues')}}">
  <a class="btn btn-primary floatright" href="#addIssue" data-toggle="modal">{{@lng.addticket}}</a>
</check>

<br class="clearfix" />

<check if="{{count(@tickets)}}">
    <true>
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
                <repeat group="{{@tickets}}" value="{{@ticket}}">
                <tr>
                    <td class="title"><a href="/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a></td>
                    <td class="type">{{Helper::getName('types', @ticket->type)}}</td>
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
        <div class="alert alert-info clearfix">{{@lng.noTickets}}</div>
    </false>
</check>

<div>
  <form action="/search" method="post" id="searchForm" class="form-search">
    <div class="input-group col col-lg-7">
      <input type="text" name="search" value="{{@SESSION.ticketSearch}}" id="searchInput" />
      <div class="input-group-btn">
        <button class="btn btn-default" type="submit">{{@lng.search}}</button>
      </div>
    </div>
	</form>
</div>

<div class="modal" id="addIssue">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="/ticket">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>{{@lng.addticket}}</h3>
				</div>
				<div class="modal-body form-horizontal">
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.title}}</label>
						<div class="col col-lg-10">
							<input type="text" name="title" />
						</div>
					</div>
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.category}}</label>
						<div class="col col-lg-10">
							<select name="category" size="1">
								<repeat group="{{@categories}}" value="{{@category}}">
									<option value="{{@category->hash}}">{{@category->name}}</option>
								</repeat>
							</select>
						</div>
					</div>					
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.type}}</label>
						<div class="col col-lg-10">
							<select name="type" size="1">
								<repeat group="{{@lng.types}}" value="{{@type}}">
									<option value="{{@type.id}}">{{@type.name}}</option>
								</repeat>
							</select>
						</div>
					</div>					
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.priority}}</label>
						<div class="col col-lg-10">
							<select name="priority" size="1">
								<repeat group="{{@lng.priorities}}" value="{{@priority}}">
									<option value="{{@priority.id}}" {{(@priority.id==3)?'selected="selected"':''}}>{{@priority.id}} - {{@priority.name}}</option>
								</repeat>
							</select>
						</div>
					</div>					
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.milestone}}</label>
						<div class="col col-lg-10">
							<select name="milestone" size="1">
								<repeat group="{{@milestones}}" value="{{@milestone}}">
									<option value="{{@milestone->hash}}">{{@milestone->name}}</option>
								</repeat>
							</select>
						</div>
					</div>
					
					<div class="row">
						<label for="" class="col col-lg-2 control-label">{{@lng.description}}</label>
						<div class="col col-lg-10">
							<textarea name="description" class="ticketComment"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" value="{{@lng.addTicket}}" class="btn btn-primary" />
				</div>
			</form>
		</div>
	</div>
</div>
