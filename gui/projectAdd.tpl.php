<h2>{{@lng.project}} › {{@lng.add}}</h2>

<form action="{{@BASE}}/project/add" method="post">
    <div class="formRow">
        <div class="formLabel">
            {{@lng.projectname}}
        </div>
        <div class="formValue">
            <input type="text" name="name" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.projectdescription}}
        </div>
        <div class="formValue">
            <textarea name="description" class="projectText"></textarea>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.publicproject}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="public" />                          
        </div>
    </div>
    <input type="submit" value="{{@lng.add}}" />
</form>