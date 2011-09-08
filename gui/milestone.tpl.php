<h2>{{@lng.milestone}} › {{@milestone->name}} <span class="hash">#{{@milestone->hash}}</span></h2>

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
<p>{{nl2br(@milestone->description)}}</p>

<div class="milestoneTickets">
    <h3>{{@lng.tickets}}:</h3>
    <table class="sortable zebra">
        <thead>
            <tr>
                <th>#</a></th>
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
                <td class="id">{{substr(@ticket->tickethash,0,5)}}</td>
                <td class="title">
                    <a href="{{@BASE}}/ticket/{{@ticket->tickethash}}">{{@ticket->title}}</a>
                </td>
                <td class="type">{{@ticket->typename}}</td>
                <td class="state color{{@ticket->state}}">
                    {{@ticket->statusname}}</td>
                <td class="priority">{{@ticket->priorityname}}</td>
                <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
                <td class="owner"><a href="{{@BASE}}/user/{{@ticket->username}}">{{@ticket->username}}</a></td>
                <td class="owner"><a href="{{@BASE}}/user/{{@ticket->assignedname}}">{{@ticket->assignedname}}</a></td>
            </tr>
        </F3:repeat>
        </tbody>
    </table>
</div>
