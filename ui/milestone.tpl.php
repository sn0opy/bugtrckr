<h2>{{@lng.milestone}} › {{@milestone->name}}</h2>

<check if="{{@stats.fullTicketCount}}">
    <true>
        <div class="progress">
            <repeat group="{{@stats.ticketCount}}" value="{{@tickCnt}}">
                <div width="{{@tickCnt.percent}}%" class="bar background-color{{@tickCnt.state}}">{{@tickCnt.count}}</div>
            </repeat>
		</div>
    </true>
    <false>
        <div class="progress">
			<div class="bar" style="width: 100%;">0</div>
        </div>
    </false>
</check>

<p class="rminfo">{{@stats.openTickets}} {{@lng.ticketsleft}}</p>
<p>{{helper::translateBBCode(@milestone->description)}}</p>

<div class="milestoneTickets">
    <h3>{{@lng.tickets}}:</h3>
    <check if="{{count(@tickets)}}">
        <true>
            <table class="table table-striped sortable table-bordered">
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
                        <td class="title">
                                <a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a>
                        </td>
                        <td class="type">{{Helper::getName('types', @ticket->type)}}</td>
                        <td class="state"><span class="color{{@ticket->state}}">{{Helper::getName('states', @ticket->state)}}</span></td>
                        <td class="priority"><span style="display: none;">{{@ticket->priority}}</span><span class="color{{@ticket->priority}}">{{Helper::getName('priorities', @ticket->priority)}}</span></td>
                        <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
                        <td class="owner"><a href="{{@BASE}}/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
                        <td class="owner"><a href="{{@BASE}}/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
                    </tr>
                </repeat>
                </tbody>
            </table>
        </true>
        <false>
            <div class="alert alert-info">{{@lng.noTickets}}</div>
        </false>
	</check>
</div>
