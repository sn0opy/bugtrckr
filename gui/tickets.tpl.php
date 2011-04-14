<h1>Tickets</h1>

<button type="button"
	onclick="document.getElementById('add').style.display = 'block'" >
	{@LANG.ADDTICKET} 
</button>

{* Form for new Tickets *}
<div id="add">
	<button onclick="document.getElementById('add').style.display = 'none'"
			style="float: right">
		X
	</button>

	<form method="POST" action="/{@BASE}ticket/">
		<div class="formRow">
			<div class="formLabel">{@LANG.TITLE}</div>
			<div class="formValue"><input type="text" name="title" /></div>
		</div>

		<div class="formRow">
			<div class="formLabel">{@LANG.DESCRIPTION}</div>
			<div class="formValue">
				<textarea name="description"></textarea>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{@LANG.TYPE}</div>
			<div class="formValue">
				<select name="type" size="1">
					<option value="1">{@LANG.BUG}</option>
					<option value="2">{@LANG.FEATURE}</option>
					<option value="3">{@LANG.REQUIREMENT}</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{@LANG.PRIORITY}</div>
			<div class="formValue">
				<select name="priority" size="1">
					<option value="1">1 - {@LANG.VERYHIGH}</option>
					<option value="2">2 - {@LANG.HIGHT}</option>
					<option value="3">3 - {@LANG.NORMAL}</option>
					<option value="4">4 - {@LANG.LOW}</option>
					<option value="5">5 - {@LANG.VERYLOW}</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">{@LANG.MILESTONE}</div>
			<div class="formValue">
				<select name="milestone" size="1">
				<F3:repeat group="{@milestones}" value="{@milestone}">
					<option value="{@milestone.id}">{@milestone.name}</option>
				</F3:repeat>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">&nbsp;</div>
			<div class="formValue">
				<input type="submit" value="{@LANG.SUBMIT}" />
			</div>
		</div>
	</form>
	<br class="clearfix" />
</div>

<table class="overview">
	<tr>
		<th><a href="/{@BASE}tickets/id">{@LANG.ID}</a></th>
		<th><a href="/{@BASE}tickets/title">{@LANG.TITLE}</a></th>
		<th><a href="/{@BASE}tickets/state">{@LANG.STATUS}</a></th>
		<th><a href="/{@BASE}tickets/priority">{@LANG.PRIORITY}</a></th>
		<th><a href="/{@BASE}tickets/created">{@LANG.CREATED}</a></th>
		<th>{@LANG.OWNER}</th>
	</tr>

	<F3:repeat group="{@tickets}" key="{@i}" value="{@ticket}">
	<tr class="tr{@i%2}">
		<td>{@ticket.id}</td>
		<td><a href="/{@BASE}ticket/{@ticket.hash}">{@ticket.title}</a></td>
		<td>{@ticket.state}</td>
		<td>{@ticket.priority}</td>
		<td>{@ticket.created}</td>
		<td>{@ticket.owner}</td>
	</tr>
	</F3:repeat>
</table>
