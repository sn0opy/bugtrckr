<h2 class="floatleft">{{@lng.roadmap}}</h2>

<check if="{{count(@road)}}">
    <true>
        <repeat group="{{@road}}" key="{{@i}}" value="{{@item}}">
        <div class="milestone clearfix">
            <h3><a href="/milestone/{{@item.infos->hash}}">{{@item.infos->name}}</a></h3>

            <div class="meta">
                <check if="{{@item.fullTicketCount}}">
                    <true>
                        <table class="percentBar">
                            <tr>
                                <repeat group="{{@item.ticketCount}}" value="{{@tickCnt}}">
                                    <td width="{{@tickCnt.percent}}%" class="background-color{{@tickCnt.state}}">{{@tickCnt.count}}</td>
                                </repeat>
                            </tr>
                        </table>
                    </true>
                    <false>
                       <table class="percentBar">
                            <tr>
                                <td width="100%" class="noTickets">0</td>
                            </tr>
                        </table>
                    </false>
                </check>
                <p class="rminfo">{{@item.openTickets}} {{@lng.ticketsleft}}</p>
                <p>{{nl2br(@item.infos->description)}}</p>
            </div>
        </div>
        </repeat>
    </true>
    <false>
        <p class="info message clearfix">{{@lng.noMilestones}}</p>
    </false>
</check>
