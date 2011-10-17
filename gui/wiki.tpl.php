<script type="text/javascript">
	function edit()
	{
		document.getElementById('submitbutton').style.display = 'block';
		document.getElementById('cancelbutton').style.display = 'block';
		document.getElementById('wikiedit_textarea').style.display = 'block';
		document.getElementById('editbutton').style.display = 'none';
		document.getElementById('wikicontent').style.display = 'none';
	}

	function cancel()
	{
		document.getElementById('submitbutton').style.display = 'none';
		document.getElementById('cancelbutton').style.display = 'none';
		document.getElementById('wikiedit_textarea').style.display = 'none';
		document.getElementById('editbutton').style.display = 'block';
		document.getElementById('wikicontent').style.display = 'block';
	}

	function submit()
	{
		document.forms['wiki'].submit();		
	}
</script>


<h1>{{@title}}</h1>

<div id="wikicontent">
	{{@displayablecontent}}
</div>

<div id="wikiedit">
	<form action="{{@BASE}}/wiki" method="POST" name="wiki">
		<textarea id="wikiedit_textarea" name="content">{{@entry->content}}</textarea>
		<input type="hidden" name="hash" value="{{@entry->hash}}" />
		<input type="hidden" name="title" value="{{@entry->title}}" />
	</form>

	<a href="#" id="editbutton" onclick="edit()">Edit</a>
	<a href="#" id="submitbutton" onclick="submit()">Submit</a>
	<a href="#" id="cancelbutton" onclick="cancel()">Cancel</a>
</div>
