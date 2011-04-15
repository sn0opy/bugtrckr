<h1>{@lng.roadmap}</h1>

<button type="button" onclick="document.getElementById('add').style.display = 'block'">
	{@lng.addmilestone}
</button>

<div id="add">
	<button onclick="document.getElementById('add').style.display = 'none'"
			style="float: right">
		X
	</button>

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


<F3:repeat group="{@road}" key="{@i}" value="{@item}">

<div class="milestone">
	<h2>{@item.milestone.name}</h2>
	
	<p>{@item.milestone.description}</p>

	<p class="info">{@item.ticketcount} {@lng.ticketsleft}</p>

	<ul class="sublist">
	<F3:repeat group="{@item.tickets}" key="{@j}" value="{@ticket}">
		<li><a href="/{@BASE}ticket/{@ticket.hash}">{@ticket.title}</a></li>
	</F3>
	</ul>	

</div>

</F3:repeat>
