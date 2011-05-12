<F3:check if="{{1==1}}">
    <F3:true>
        <div class="tabbed">
            <div class="tabs">
                <ul>
                    <li class="active"><a href="#" title="tabContent_1" class="tab">{{@lng.settings}}</a></li>
                    <li><a href="#" title="tabContent_2" class="tab">{{@lng.members}}</a></li>
                    <li><a href="#" title="tabContent_3" class="tab">{{@lng.milestones}}</a></li>
                    <li><a href="#" title="tabContent_4" class="tab">{{@lng.roles}}</a></li>
                </ul>
            </div>
            
            <br class="clearfix" />

            <div class="tabContent" id="tabContent_1">
                {{* Settings *}}
                <form action="/{{@BASE}}project/settings/main/edit" method="post">
                    <div class="formRow">
                        <div class="formLabel">
                            {{@lng.projectname}}
                        </div>
                        <div class="formValue">
                           <input type="text" name="name" value="{{@projDetails.name}}" />
                        </div>
                    </div>

                    <div class="formRow">
                        <div class="formLabel">
                            {{@lng.projectdescription}}
                        </div>
                        <div class="formValue">
                           <textarea name="description" class="projectText">{{@projDetails.description}}</textarea>
                        </div>
                    </div>

                    <div class="formRow">
                        <div class="formLabel">
                            {{@lng.publicproject}}
                        </div>
                        <div class="formValue">
                            <F3:check if="{{@projDetails.public}}">
                                <F3:true>
                                    <input type="checkbox" name="public" checked="checked" />
                                </F3:true>                                
                                <F3:false>
                                    <input type="checkbox" name="public" />
                                </F3:false>
                            </F3:check>                           
                        </div>
                    </div>
                    <input type="submit" value="{{@lng.save}}" />
                </form>
            </div>
            <div class="tabContent" id="tabContent_2">
                {{* members *}}
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
                                <a href="/{{@BASE}}user/{{@member.name}}">{{@member.name}}</a>
                            </td>
                            <td class="type">
                                <form action="/{{@BASE}}project/settings/member/setrole" method="post">
                                    <input type="hidden" name="user" value="{{@member.hash}}" />
                                    <select name="role">
                                    <F3:repeat group="{{@projRoles}}" value="{{@role}}" key="{{@j}}">
                                        <F3:check if="{{@member.role == @role.id}}">
                                            <F3:true>
                                                <option selected="selected" value="{{@role.hash}}">{{@role.name}}</option>
                                            </F3:true>
                                            <F3:false>
                                                <option value="{{@role.hash}}">{{@role.name}}</option>
                                            </F3:false>
                                        </F3:check>
                                    </F3:repeat>
                                    </select>
                                    <input type="submit" value="Ändern" />
                                </form>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>
            </div>
            <div class="tabContent" id="tabContent_3">
                {{* milestones *}}
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
                                <a href="/{{@BASE}}project/settings/milestone/{{@milestone.hash}}">{{@milestone.name}}</a>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>
                <a href="/{{@BASE}}project/settings/milestone/add">{{@lng.addmilestone}}</a>
            </div>

            <div class="tabContent" id="tabContent_4">
                {{* roles *}}
                                
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
                                <a href="/{{@BASE}}project/settings/role/{{@role.hash}}">{{@role.name}}</a>
                            </td>
                        </tr>
                        </F3:repeat>
                    </tbody>
                </table>
                <a href="/{{@BASE}}project/settings/role/add">{{@lng.addrole}}</a>
            </div>
        </div>
    </F3:true>
    <F3:false>
        <div class="error">
            <p>{{@lng.noAccess}}</p>
        </div>
    </F3:false>
</F3:check>