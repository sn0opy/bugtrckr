<h2>{{@lng.role}} › {{@lng.addrole}}</h2>

<div class="roleSettings">
<form action="/{{@BASE}}project/settings/role/edit" method="post">
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
            {{@lng.issuesAssigneable}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="issuesAssigneable" />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_editProject}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_editProject" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMembers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageMembers" />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMilestones}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageMilestones" />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageRoles}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="proj_manageRoles" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_editIssues" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_addIssues" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_deleteIssues}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_deleteIssues" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_moveIssue}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_moveIssue" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_editWatchers" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_addWatchers" />
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_viewWatchers}}
        </div>
        <div class="formValue">
            <input type="checkbox" name="iss_viewWatchers" />
        </div>
    </div>
    
    <input type="submit" value="{{@lng.save}}" />
</form>
</div>