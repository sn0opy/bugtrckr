<script type="text/javascript">
	function edit()
	{
		document.getElementById('submitbutton').style.display = 'inline-block';
		document.getElementById('cancelbutton').style.display = 'inline-block';
		document.getElementById('wikiedit').style.display = 'block';
		document.getElementById('editbutton').style.display = 'none';
		document.getElementById('wikicontent').style.display = 'none';
	}

	function cancel()
	{
		document.getElementById('submitbutton').style.display = 'none';
		document.getElementById('cancelbutton').style.display = 'none';
		document.getElementById('wikiedit').style.display = 'none';
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

<div class="row">
  <div id="wikicontent" class="col col-lg-7">
    {{@displayablecontent}}
  </div>

  <div id="wikiedit" class="col col-lg-7" style="display: none">
	  <form action="{{@BASE}}/wiki" method="POST" name="wiki">
		  <textarea id="wikiedit_textarea" name="content">{{@entry->content}}</textarea>
		  <input type="hidden" name="hash" value="{{@entry->hash}}" />
		  <input type="hidden" name="title" value="{{@entry->title}}" />
	  </form>
  </div>

  <div class="col col-lg-5 well">

    <fieldset>
      <legend>Legend</legend>

      <div class="row">
        <div class="col col-lg-6">==Headline2==</div>
        <div class="col col-lg-6"><h2>Headline 2</h2></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">===Headline3===</div>
        <div class="col col-lg-6"><h3>Headline 3</h3></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">'''Text'''</div>
        <div class="col col-lg-6"><b>Text</b></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">''Text''</div>
        <div class="col col-lg-6"><i>Text</i></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">----</div>
        <div class="col col-lg-6"><hr /></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">[[http://www.github.io GitHub]]</div>
        <div class="col col-lg-6"><a href="http://www.github.io">GitHub</a></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">[[Wikieintrag]]</div>
        <div class="col col-lg-6"><a href="{{@BASE}}/wiki/Wikieintrag">Wikieintrag</a></div>
      </div>

      <div class="row">
        <div class="col col-lg-6">~~Code~~</div>
        <div class="col col-lg-6"><pre>Code</pre></div>
      </div>
    </fieldset>
  </div>
</div>

<div class="row">
  <div class="col col-lg-12">
  	<button id="editbutton" onclick="edit()" class="btn" type="button">Edit</button>
	  <button id="submitbutton" onclick="submit()" class="btn btn-primary" type="button">Submit</button>
    <button id="cancelbutton" onclick="cancel()" class="btn" type="button">Cancel</button>
  </div>
</div>
