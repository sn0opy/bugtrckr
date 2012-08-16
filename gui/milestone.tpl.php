<h2>{{@lng.milestone}} › {{@milestone->name}}</h2>

<F3:check if="{{@stats.fullTicketCount}}">
    <F3:true>
        <table class="percentBar">
            <tr>
            <F3:repeat group="{{@stats.ticketCount}}" value="{{@tickCnt}}">
                <td width="{{@tickCnt.percent}}%" class="background-color{{@tickCnt.state}}">{{@tickCnt.count}}</td>
            </F3:repeat>
            </tr>
        </table>
    </F3:true>
    <F3:false>
        <table class="percentBar">
            <tr>
                <td width="100%" class="noTickets">0</td>
            </tr>
        </table>
    </F3:false>
</F3:check>

<p class="rminfo">{{@stats.openTickets}} {{@lng.ticketsleft}}</p>
<p>{{\misc\helper::translateBBCode(@milestone->description)}}</p>

<div class="milestoneTickets">
    <h3>{{@lng.tickets}}:</h3>
    <F3:check if="{{count(@tickets)}}">
        <F3:true>
            <table class="sortable zebra">
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
                        <td class="title">
                                <a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a>
                        </td>
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
            <div class="message info">{{@lng.noTickets}}</div>
        </F3:false>
	</F3:check>
</div>
