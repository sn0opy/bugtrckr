<h2>{{@lng.milestone}} › {{@msData->name}}</h2>

<div class="msSettings">
    <form action="{{@BASE}}/project/settings/milestone/edit" method="post">
        <input type="hidden" name="hash" value="{{@msData->hash}}" />

        <div class="formRow">
            <div class="formLabel">
                {{@lng.name}}
            </div>
            <div class="formValue">
                <input type="text" name="name" value="{{@msData->name}}" />
            </div>
        </div>
        
        <div class="formRow">
            <div class="formLabel">
                {{@lng.finisheddate}}
            </div>
            <div class="formValue">
                <input type="date" name="finished" value="{{@msData->finished}}" />
            </div>
        </div>
		
        <div class="formRow">
            <div class="formLabel">
                {{@lng.description}}
            </div>
            <div class="formValue">
                <textarea name="description" class="ticketComment">{{@msData->description}}</textarea>
            </div>
        </div>

        <input type="submit" value="{{@lng.save}}" class="btn" />
    </form>
</div>