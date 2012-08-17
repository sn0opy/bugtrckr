<h2>{{@lng.role}} › {{@roleData->name}}</h2>

<div class="roleSettings">
<form action="{{@BASE}}/project/settings/role/edit" method="post">
    <input type="hidden" name="hash" value="{{@roleData->hash}}" />
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.name}}
        </div>
        <div class="formValue">
            <input type="text" name="name" value="{{@roleData->name}}" />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.issuesAssigneable}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="issuesAssigneable" {{(@roleData->issuesAssigneable)?'checked="checked"':''}} />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_editProject}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_editProject" {{(@roleData->proj_editProject)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMembers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageMembers" {{(@roleData->proj_manageMembers)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMilestones}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageMilestones" {{(@roleData->proj_manageMilestones)?'checked="checked"':''}} />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageRoles}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageRoles" {{(@roleData->proj_manageRoles)?'checked="checked"':''}} />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_editIssues" {{(@roleData->iss_editIssues)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_addIssues" {{(@roleData->iss_addIssues)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_deleteIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_deleteIssues" {{(@roleData->iss_deleteIssues)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_moveIssue}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_moveIssue" {{(@roleData->iss_moveIssue)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_editWatchers" {{(@roleData->iss_editWatchers)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_addWatchers" {{(@roleData->iss_addWatchers)?'checked="checked"':''}} />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_viewWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_viewWatchers" {{(@roleData->iss_viewWatchers)?'checked="checked"':''}} />
        </div>
    </div>
    
    <input type="submit" value="{{@lng.save}}" />
</form>
</div>