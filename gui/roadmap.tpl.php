<h2 class="floatleft">{@lng.roadmap}</h2>

<button type="button" class="floatright"
        onclick="document.getElementById('add').style.display = 'block'">
	{@lng.addmilestone}
</button>

{* Form for new milestones *}
<div id="add">
    <h3 class="floatleft">{@lng.addmilestone}</h3>
	<a class="closeButton" href="#" onclick="document.getElementById('add').style.display = 'none'">
		X
	</a>

	<form method="POST" action="/{@BASE}milestone">
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
			<div class="formValue"><input type="text" name="finished" /></div>
		</div>
		<div class="formRow">
			<div class="formLabel">&nbsp;</div>
			<div class="formValue"><input type="submit" value="{@lng.submit}" /></div>
		</div>
	</form>
	<br class="clearfix" />
</div>
{* End form for new milestones *}

<F3:repeat group="{@road}" key="{@i}" value="{@item}">
<div class="milestone clearfix">
	<h3>{@item.milestone.name}</h3>

    <div class="meta">
        <table class="percentBar">
            <tr>
            <F3:repeat group="{@item.ticketCount}" value="{@tickCnt}">
                <td width="{@tickCnt.percent}%" title="{@tickCnt.title}"
                    class="color{@tickCnt.state}">{@tickCnt.count}</td>
            </F3:repeat>
            </tr>
        </table>
        <p class="info">{@item.fullTicketCount} {@lng.tickets}</p>
        <p>{nl2br(@item.milestone.description)}</p>

        {*
        <ul class="sublist">
            <F3:repeat group="{@item.tickets}" key="{@j}" value="{@ticket}">
            <li><a href="/{@BASE}ticket/{@ticket.hash}">{@ticket.title}</a></li>
            </F3:repeat>
        </ul>
        *}
    </div>
</div>
</F3:repeat>
