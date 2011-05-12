<h2>{{@lng.role}} › {{@roleData.name}} <span class="hash">#{{@roleData.hash}}</span></h2>

<div class="roleSettings">
<form action="/{{@BASE}}project/settings/role/edit" method="post">
    <input type="hidden" name="hash" value="{{@roleData.hash}}" />
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.name}}
        </div>
        <div class="formValue">
            <input type="text" name="name" value="{{@roleData.name}}" />
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.issuesAssigneable}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.issuesAssigneable}}">
                <true>
                    <input type="checkbox" name="issuesAssigneable" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="issuesAssigneable" />
                </false>
            </check>
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_editProject}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.proj_editProject}}">
                <true>
                    <input type="checkbox" name="proj_editProject" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="proj_editProject" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMembers}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.proj_manageMembers}}">
                <true>
                    <input type="checkbox" name="proj_manageMembers" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="proj_manageMembers" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageMilestones}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.proj_manageMilestones}}">
                <true>
                    <input type="checkbox" name="proj_manageMilestones" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="proj_manageMilestones" />
                </false>
            </check>
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.proj_manageRoles}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.proj_manageRoles}}">
                <true>
                    <input type="checkbox" name="proj_manageRoles" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="proj_manageRoles" />
                </false>
            </check>
        </div>
    </div>
    
    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editIssues}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_editIssues}}">
                <true>
                    <input type="checkbox" name="iss_editIssues" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_editIssues" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addIssues}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_addIssues}}">
                <true>
                    <input type="checkbox" name="iss_addIssues" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_addIssues" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_deleteIssues}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_deleteIssues}}">
                <true>
                    <input type="checkbox" name="iss_deleteIssues" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_deleteIssues" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_moveIssue}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_moveIssue}}">
                <true>
                    <input type="checkbox" name="iss_moveIssue" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_moveIssue" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_editWatchers}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_editWatchers}}">
                <true>
                    <input type="checkbox" name="iss_editWatchers" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_editWatchers" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_addWatchers}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_addWatchers}}">
                <true>
                    <input type="checkbox" name="iss_addWatchers" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_addWatchers" />
                </false>
            </check>
        </div>
    </div>

    <div class="formRow">
        <div class="formLabel">
            {{@lng.iss_viewWatchers}}
        </div>
        <div class="formValue">
            <check if="{{@roleData.iss_viewWatchers}}">
                <true>
                    <input type="checkbox" name="iss_viewWatchers" checked="checked" />
                </true>
                <false>
                    <input type="checkbox" name="iss_viewWatchers" />
                </false>
            </check>
        </div>
    </div>
    
    <input type="submit" value="{{@lng.save}}" />
</form>
</div>