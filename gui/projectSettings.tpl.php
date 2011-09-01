<div class="tabbed">
    <div class="tabs">
        <ul>
            <li class="active"><a href="#" title="tabContent_1" class="tab">{{@lng.settings}}</a></li>
            <li><a href="#" title="tabContent_2" class="tab">{{@lng.members}}</a></li>
            <li><a href="#" title="tabContent_3" class="tab">{{@lng.milestones}}</a></li>
            <li><a href="#" title="tabContent_4" class="tab">{{@lng.roles}}</a></li>
            <li><a href="#" title="tabContent_5" class="tab">{{@lng.categories}}</a></li>
        </ul>
    </div>

    <div class="tabContent" id="tabContent_1">
        {{* Settings *}}
        <F3:check if="{{@getPermission('proj_editProject')}}">
            <F3:true>
                <form action="{{@BASE}}/project/settings/main/edit" method="post">
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
                <div class="failure message">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>
    <div class="tabContent" id="tabContent_2">
        {{* members *}}
        <F3:check if="{{@getPermission('proj_manageMembers')}}">
            <F3:true>
                <table class="sortable zebra">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rolle</th> 
                        </tr>
                    </thead>
                    <tbody>
                    <F3:repeat group="{{@projMembers}}" value="{{@member}}">
                        <tr>
                            <td class="title">
                                <a href="{{@BASE}}/user/{{@member->name}}">{{@member->name}} {{@member->role}}</a>
                            </td>
                            <td class="type manageMember">
                                <p>
                                <form action="{{@BASE}}/project/settings/member/setrole" method="post" class="floatleft">
                                    <input type="hidden" name="user" value="{{@member->hash}}" />
                                    <select name="role" class="floatleft">
                                        <F3:repeat group="{{@projRoles}}" value="{{@role}}">
                                            <option value="{{@role->hash}}" {{(@member->roleId == @role->id)?'selected="selected"':''}}>{{@role->name}}</option>
                                        </F3:repeat>
                                    </select>
                                    <input type="submit" value="Ändern" />
                                </form>
                                <form action="{{@BASE}}/project/setttings/member/delete" method="post" class="floatleft">
                                    <input type="hidden" name="user" value="{{@member->hash}}" />
                                    <input type="submit" value="{{@lng.delete}}" />
                                </form>
                                </p>
                            </td>
                        </tr>
                    </F3:repeat>
                    </tbody>
                </table>

                <form action="{{@BASE}}/project/settings/member/add" method="post">
                    <p class="addUser">
                        <select name="member">
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
                <div class="failure message">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>
    <div class="tabContent" id="tabContent_3">
        {{* milestones *}}
        <F3:check if="{{@getPermission('proj_manageMilestones')}}">
            <F3:true>
                <table>
                    <thead>
                        <tr>
                            <th>{{@lng.milestone}}</th>
							<th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <F3:repeat group="{{@projMilestones}}" key="{{@i}}" value="{{@milestone}}">
                        <tr class="tr{{@i%2}}">
                            <td class="title">
                                <a href="{{@BASE}}/project/settings/milestone/{{@milestone->hash}}">{{@milestone->name}}</a>
                            </td>
							<td class="delete">
								<a href="{{@BASE}}/project/settings/delete/milestone/{{@milestone->hash}}" onclick="return confirm('{{@lng.sure}}');">{{@lng.delete}}</a>
							</td>
                        </tr>
                    </F3:repeat>
                    </tbody>
                </table>
                <a href="{{@BASE}}/project/settings/milestone/add">{{@lng.addmilestone}}</a>
            </F3:true>
            <F3:false>
                <div class="failure message">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>

    <div class="tabContent" id="tabContent_4">
        {{* roles *}}
        <F3:check if="{{@getPermission('proj_manageRoles')}}">
            <F3:true>
                <table class="zebra sortable">
                    <thead>
                        <tr>
                            <th>{{@lng.role}}</th>
                            <th>{{@lng.actions}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    <F3:repeat group="{{@projRoles}}" value="{{@role}}">
                        <tr>
                            <td class="title">
                                <a href="{{@BASE}}/project/settings/role/{{@role->hash}}">{{@role->name}}</a>
                            </td>
                            <td class="action">
                                <a href="{{@BASE}}/project/settings/role/delete/{{@role->hash}}">{{@lng.delete}}</a>
                            </td>
                        </tr>
                    </F3:repeat>
                    </tbody>
                </table>
                <a href="{{@BASE}}/project/settings/role/add">{{@lng.addrole}}</a>
            </F3:true>
            <F3:false>
                <div class="failure message">{{@lng.noAccess}}
            </F3:false>
        </F3:check>
    </div>
</div>
    
    <div class="tabContent" id="tabContent_5">
        {{* roles *}}
        <F3:check if="{{@getPermission('proj_manageCategories')}}">
            <F3:true>
                <table class="sortable zebra">
                    <thead>
                        <tr>
                            <th>{{@lng.category}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    <F3:repeat group="{{@projCategories}}" value="{{@category}}">
                        <tr>
                            <td class="title">
                                {{@category->name}}
                            </td>
                        </tr>
                    </F3:repeat>
                    </tbody>
                </table>
                <a href="{{@BASE}}/project/settings/category/add">{{@lng.addcategory}}</a>
            </F3:true>
            <F3:false>
                <div class="failure message">{{@lng.noAccess}}</div>
            </F3:false>
        </F3:check>
    </div>
