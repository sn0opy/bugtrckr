<h2 class="floatleft">{{@lng.tickets}}</h2>

<div class="floatright ticketSearch">
	<form action="{{@BASE}}/search" method="post" id="searchForm">
		<input type="input" name="search" value="{{@SESSION.ticketSearch}}" id="searchInput" /> <input type="submit" value="{{@lng.search}}"/> <button type="button" title="Suche löschen" id="delSearch">x</button>
	</form>
</div>

<F3:check if="{{@getPermission('iss_addIssues')}}">
    <button type="button" class="floatright showLayer">{{@lng.addticket}}</button>
</F3:check>

<div class="layer" id="add">
    <h3 class="floatleft">{{@lng.addticket}}</h3>
    <a class="closeButton" href="#">×</a>

	<form method="post" action="{{@BASE}}/ticket/">
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
			<div class="formLabel"></div>
			<div class="formValue">
				<input type="submit" value="{{@lng.submit}}" />
			</div>
		</div>
	</form>
	<br class="clearfix" />
</div>
<F3:check if="{{count(@tickets)}}">
    <F3:true>
        <table class="sortable zebra">
            <thead>
                <tr>
                    <th>#</th>
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
                    <td class="id"><a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{substr(@ticket->tickethash,0,5)}}</a></td>
                    <td class="title"><a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a></td>
                    <td class="type">{{@ticket->type}}</td>
                    <td class="state color{{@ticket->state}}">{{@ticket->state}}</td>
                    <td class="priority">{{@ticket->priority}}</td>
                    <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
                    <td class="owner"><a href="{{@BASE}}/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
                    <td class="owner"><a href="{{@BASE}}/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
                </tr>
                </F3:repeat>
            </tbody>
        </table>
    </F3:true>
    <F3:false>
        <div class="info message clearfix">{{@lng.noTickets}}</div>
    </F3:false>
</F3:check>
