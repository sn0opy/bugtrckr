<h2 class="floatleft">{{@lng.roadmap}}</h2>

<F3:check if="{{count(@road)}}">
    <F3:true>
        <F3:repeat group="{{@road}}" key="{{@i}}" value="{{@item}}">
        <div class="milestone clearfix">
            <h3><a href="{{@BASE}}/milestone/{{@item.infos->hash}}">{{@item.infos->name}}</a></h3>

            <div class="meta">
                <F3:check if="{{@item.fullTicketCount}}">
                    <F3:true>
                        <table class="percentBar">
                            <tr>
                            <F3:repeat group="{{@item.ticketCount}}" value="{{@tickCnt}}">
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
                <p class="rminfo">{{@item.openTickets}} {{@lng.ticketsleft}}</p>
                <p>{{nl2br(@item.infos->description)}}</p>
            </div>
        </div>
        </F3:repeat>
    </F3:true>
    <F3:false>
        <p class="info message clearfix">{{@lng.noMilestones}}</p>
    </F3:false>
</F3:check>
