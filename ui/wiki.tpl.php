<script type="text/javascript">
	function edit()
	{
		document.getElementById('submitbutton').style.display = 'inline-block';
		document.getElementById('cancelbutton').style.display = 'inline-block';
		document.getElementById('wikiedit_textarea').style.display = 'block';
		document.getElementById('editbutton').style.display = 'none';
		document.getElementById('wikicontent').style.display = 'none';
	}

	function cancel()
	{
		document.getElementById('submitbutton').style.display = 'none';
		document.getElementById('cancelbutton').style.display = 'none';
		document.getElementById('wikiedit_textarea').style.display = 'none';
		document.getElementById('editbutton').style.display = 'inline-block';
		document.getElementById('wikicontent').style.display = 'block';
	}

	function submit()
	{
		document.forms['wiki'].submit();		
	}
</script>


<h2>{{@title}}</h2>

<check if="{{@entry->hash}}">
	<a href="/{{@BASE}}wiki/discussion/{{@entry->hash}}">Diskussion betrachten</a>
</check>

<div id="wikicontent">
	{{@displayablecontent}}
</div>

<div id="wikiedit">
	<form action="{{@BASE}}/wiki" method="POST" name="wiki">
		<textarea id="wikiedit_textarea" name="content">{{@entry->content}}</textarea>
		<input type="hidden" name="hash" value="{{@entry->hash}}" />
		<input type="hidden" name="title" value="{{@entry->title}}" />
	</form>

	<button id="editbutton" onclick="edit()" class="btn" type="button">Edit</button>
	<button id="submitbutton" onclick="submit()" class="btn btn-primary" type="button">Submit</button>
	<button id="cancelbutton" onclick="cancel()" class="btn" type="button">Cancel</button>
</div>
