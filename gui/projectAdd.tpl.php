<h2>{{@lng.project}} › {{@lng.add}}</h2>
<F3:check if="{{@SESSION.user.id}}">
    <F3:true>
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
                    <input type="checkbox" name="public" checked="checked" />                          
                </div>
            </div>
            <input type="submit" value="{{@lng.add}}" />
        </form>
    </F3:true>
    <F3:false>
        <div class="info">{{@lng.noAccess}}</div>
    </F3:false>
</F3:check>