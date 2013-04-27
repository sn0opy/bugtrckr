<h2>{{@lng.milestone}} › {{@lng.addmilestone}}</h2>

<div class="msSettings">
    <form action="/project/settings/milestone/edit" method="post">
        <div class="formRow">
            <div class="formLabel">
                {{@lng.name}}
            </div>
            <div class="formValue">
                <input type="text" name="name" />
            </div>
        </div>
        
        <div class="formRow">
            <div class="formLabel">
                {{@lng.finisheddate}}
            </div>
            <div class="formValue">
                <input type="date" name="finished" value="{{@today}}" />
            </div>
        </div>
		
		<div class="formRow">
            <div class="formLabel">
                {{@lng.description}}
            </div>
            <div class="formValue">
                <textarea name="description" class="ticketComment"></textarea>
            </div>
        </div>

        <input type="submit" value="{{@lng.save}}" class="btn" />
    </form>
</div>