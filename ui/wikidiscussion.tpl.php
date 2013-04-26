<h1>{{@title}}</h1>

<repeat group="{{@discussions}}" value="{{@disc}}">
<div class="discussion">
	<p>{{@lng.written_by}} {{Helper::getUsername(@disc->created_by)}}</p>
	<div class="discussion_content">
		{{@disc->content}}
	</div>
</div>
</repeat>

<form action="/{{@BASE}}wikidiscussion" method="POST">
	<textarea name="content"></textarea>

	<input type="hidden" name="entry" value="{{@entry->hash}}" />
	<input type="submit" value="submit" />
</form>
