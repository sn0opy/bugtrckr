<h2>Tickets</h2>

<img src="/{@BASE}img/new.png" height="20px" 
	onclick="document.getElementById('add').style.display = 'block'" />

{* Form for new Tickets *}
<div id="add">
	<button onclick="document.getElementById('add').style.display = 'none'"
			style="float: right">
		X
	</button>

	<form method="POST" action="/{@BASE}ticket/">
		<div class="formRow">
			<div class="formLabel">Title</div>
			<div class="formValue"><input type="text" name="title" /></div>
		</div>

		<div class="formRow">
			<div class="formLabel">Description</div>
			<div class="formValue">
				<textarea name="description"></textarea>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">Type</div>
			<div class="formValue">
				<select name="type" size="1">
					<option value="1">Feature</option>
					<option value="2">Bug</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<div class="formLabel">Priority</div>
			<div class="formValue">
				<select name="priority" size="1">
					<option value="1">1 - Low</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option value="5">5 - High</option>
				</select>
			</div>
		</div>

		<div class="formRow">
			<input type="submit" value="submit" />
		</div>
	</form>
</div>

<table class="overview">
	<tr>
		<th><a href="/{@BASE}tickets/id">{@ID}</a></th>
		<th><a href="/{@BASE}tickets/title">{@TITLE}</a></th>
		<th><a href="/{@BASE}tickets/state">{@STATUS}</a></th>
		<th><a href="/{@BASE}tickets/created">{@CREATED}</a></th>
		<th>{@OWNER}</th>
	</tr>

	<F3:repeat group="{@tickets}" key="{@i}" value="{@ticket}">
	<tr class="tr{@i%2}">
		<td>{@ticket.id}</td>
		<td><a href="/{@BASE}ticket/{@ticket.id}">{@ticket.title}</a></td>
		<td>{@ticket.state}</td>
		<td>{@ticket.created}</td>
		<td>{@ticket.owner}</td>
	</tr>
	</F3:repeat>
</table>
