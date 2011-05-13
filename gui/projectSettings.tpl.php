<div class="tabbed">
    <div class="tabs">
        <ul>
            <li class="active"><a href="#" title="tabContent_1" class="tab">{{@lng.settings}}</a></li>
            <li><a href="#" title="tabContent_2" class="tab">{{@lng.members}}</a></li>
            <li><a href="#" title="tabContent_3" class="tab">{{@lng.milestones}}</a></li>
            <li><a href="#" title="tabContent_4" class="tab">{{@lng.roles}}</a></li>
        </ul>
    </div>
    
    <div class="tabContent" id="tabContent_1">
        {{* Settings *}}
        <F3:check if="{{@getPermission('proj_editProject')}}">
            <F3:true>
            <form action="/{{@BASE}}project/settings/main/edit" method="post">
                <div class="formRow">
                    <div class="formLabel">
                        {{@lng.projectname}}
                    </div>
                    <div class="formValue">
                       <input type="text" name="name" value="{{@projDetails->name}}" />
                    </div>
                </div>

                <div class="formRow">
                    <div class="formLabel">
                        {{@lng.projectdescription}}
                    </div>
                    <div class="formValue">
                       <textarea name="description" class="projectText">{{@projDetails->description}}</textarea>
                    </div>
                </div>

                <div class="formRow">
                    <div class="formLabel">
                        {{@lng.publicproject}}
                    </div>
                    <div class="formValue">
                        <input type="checkbox" name="public" {{(@projDetails->public)?'checked="checked"':''}} />                          
                    </div>
                </div>
                <input type="submit" value="{{@lng.save}}" />
            </form>
            </F3:true>
            <F3:false>
                <div class="error">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>
    <div class="tabContent" id="tabContent_2">
        {{* members *}}
        <F3:check if="{{@getPermission('proj_manageMembers')}}">
            <F3:true>
                <table class="overview">
                    <thead>
                        <tr>
                            <th><a href="#">Name</a></th>
                            <th><a href="#">Rolle</a></th> 
                        </tr>
                    </thead>
                    <tbody>
                        <F3:repeat group="{{@projMembers}}" key="{{@i}}" value="{{@member}}">
                        <tr class="tr{{@i%2}}">
                            <td class="title">
                                <a href="/{{@BASE}}user/{{@member->name}}">{{@member->name}}</a>
                            </td>
                            <td class="type">
                                <form action="/{{@BASE}}project/settings/member/setrole" method="post">
                                    <input type="hidden" name="user" value="{{@member->hash}}" />
                                    <select name="role">
                                    <F3:repeat group="{{@projRoles}}" value="{{@role}}">
                                        <option value="{{@role->hash}}" {{@member->role == @role->id}}>{{@role->name}}</option>
                                    </F3:repeat>
                                    </select>
                                    <input type="submit" value="Ändern" />
                                </form>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>

                <form action="#" method="post">
                    <p class="addUser">
                        <select name="name">
                            <F3:repeat group="{{@users}}" value="{{@user}}">
                                <option value="{{@user->hash}}">{{@user->name}}</option>
                            </F3:repeat>
                        </select>
                        {{@lng.asRole}}
                        <select name="role">
                            <F3:repeat group="{{@projRoles}}" value="{{@role}}">
                                <option value="{{@role->hash}}">{{@role->name}}</option>
                            </F3:repeat>
                        </select>

                        <input type="submit" value="{{@lng.add}}" />
                    </p>
                </form>
            </F3:true>
            <F3:false>
                <div class="error">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>
    <div class="tabContent" id="tabContent_3">
        {{* milestones *}}
        <F3:check if="{{@getPermission('proj_manageMilestones')}}">
            <F3:true>
                <table class="overview">
                    <thead>
                        <tr>
                            <th><a href="#">{{@lng.milestone}}</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <F3:repeat group="{{@projMilestones}}" key="{{@i}}" value="{{@milestone}}">
                        <tr class="tr{{@i%2}}">
                            <td class="title">
                                <a href="/{{@BASE}}project/settings/milestone/{{@milestone->hash}}">{{@milestone->name}}</a>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>
                <a href="/{{@BASE}}project/settings/milestone/add">{{@lng.addmilestone}}</a>
            </F3:true>
            <F3:false>
                <div class="error">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>

    <div class="tabContent" id="tabContent_4">
        {{* roles *}}
        <F3:check if="{{@getPermission('proj_manageRoles')}}">
            <F3:true>
                <table class="overview">
                    <thead>
                        <tr>
                            <th><a href="#">{{@lng.role}}</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <F3:repeat group="{{@projRoles}}" key="{{@i}}" value="{{@role}}">
                        <tr class="tr{{@i%2}}">
                            <td class="title">
                                <a href="/{{@BASE}}project/settings/role/{{@role->hash}}">{{@role->name}}</a>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>
                <a href="/{{@BASE}}project/settings/role/add">{{@lng.addrole}}</a>
            </F3:true>
            <F3:false>
                <div class="error">{{@lng.noAccess}}
            </F3:false>
        </F3:check>
    </div>
</div>