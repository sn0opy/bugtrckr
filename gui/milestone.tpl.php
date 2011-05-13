<h2>{{@lng.milestone}} › {{@milestone->name}} <span class="hash">#{{@milestone->hash}}</span></h2>

<F3:check if="{{@stats.fullTicketCount}}">
    <F3:true>
        <table class="percentBar">
            <tr>
            <F3:repeat group="{{@stats.ticketCount}}" value="{{@tickCnt}}">
                <td width="{{@tickCnt.percent}}%" title="{{@tickCnt.title}}"
                    class="color{{@tickCnt.state}}">{{@tickCnt.count}}</td>
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

<p class="info">{{@stats.openTickets}} {{@lng.ticketsleft}}</p>
<p>{{nl2br(@milestone->description)}}</p>

<div class="milestoneTickets">
    <h3>{{@lng.tickets}}:</h3>
    <table class="overview">
        <thead>
        <tr>
            <th><a href="/{{@BASE}}tickets/id">#</a></th>
            <th><a href="/{{@BASE}}tickets/title">{{@lng.title}}</a></th>
            <th><a href="/{{@BASE}}tickets/state">{{@lng.status}}</a></th>
            <th><a href="/{{@BASE}}tickets/priority">{{@lng.priority}}</a></th>
            <th><a href="/{{@BASE}}tickets/created">{{@lng.created}}</a></th>
            <th>{{@lng.owner}}</th>
        </tr>
        </thead>

        <tbody>
            <F3:repeat group="{{@tickets}}" key="{{@i}}" value="{{@ticket}}">
            <tr class="tr{{@i%2}}">
                <td class="id">{{@i+1}}</td>
                <td class="title">
                    <a href="/{{@BASE}}ticket/{{@ticket->hash}}">{{@ticket->title}}</a>
                </td>
                <td class="state">{{@ticket->state}}</td>
                <td class="priority">{{@ticket->priority}}</td>
                <td class="created">{{date('d.m.Y H:i', @ticket->created)}}</td>
                <td class="owner"><a href="/{{@BASE}}user/{{@ticket->owner}}">{{@ticket->owner}}</a></td>
            </tr>
            </F3:repeat>
        </tbody>
    </table>
</div>
