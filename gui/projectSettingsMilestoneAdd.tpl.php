<h2>{{@lng.milestone}} › {{@lng.addmilestone}}</h2>

<div class="msSettings">
    <form action="/{{@BASE}}project/settings/milestone/edit" method="post">
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
                {{@lng.description}}
            </div>
            <div class="formValue">
                <textarea name="description"></textarea>
            </div>
        </div>

        <input type="submit" value="{{@lng.save}}" />
    </form>
</div>