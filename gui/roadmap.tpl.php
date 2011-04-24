<h2 class="floatleft">{@lng.roadmap}</h2>

<button type="button" class="floatright"
        onclick="document.getElementById('add').style.display = 'block'">
	{@lng.addmilestone}
</button>

{* Form for new milestones *}
<div id="add">
    <h3 class="floatleft">{@lng.addmilestone}</h3>
	<a class="closeButton" href="#" onclick="document.getElementById('add').style.display = 'none'; return false">
		X
	</a>

	<form method="post" action="/{@BASE}milestone">
		<div class="formRow">
			<div class="formLabel">{@lng.name}</div>
			<div class="formValue"><input type="text" name="name" /></div>
		</div>
		<div class="formRow">
			<div class="formLabel">{@lng.description}</div>
			<div class="formValue"><textarea name="description"></textarea></div>
		</div>
		<div class="formRow">
			<div class="formLabel">{@lng.finisheddate}</div>
			<div class="formValue"><input type="date" name="finished" value="{@today}" /></div>
		</div>
		<div class="formRow">
			<div class="formLabel"> </div>
			<div class="formValue"><input type="submit" value="{@lng.submit}" /></div>
		</div>
	</form>
	<br class="clearfix" />
</div>
{* End form for new milestones *}

<F3:repeat group="{@road}" key="{@i}" value="{@item}">
<div class="milestone clearfix">
	<h3><a href="/{@BASE}milestone/{@item.milestone.hash}">{@item.milestone.name}</a></h3>

    <div class="meta">
        <F3:check if="{@item.fullTicketCount}">
            <F3:true>
                <table class="percentBar">
                    <tr>
                    <F3:repeat group="{@item.ticketCount}" value="{@tickCnt}">
                        <td width="{@tickCnt.percent}%" title="{@tickCnt.title}"
                            class="color{@tickCnt.state}">{@tickCnt.count}</td>
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
        <p class="info">{@item.openTickets} {@lng.ticketsleft}</p>
        <p>{nl2br(@item.milestone.description)}</p>
    </div>
</div>
</F3:repeat>
