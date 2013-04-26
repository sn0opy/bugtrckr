<h2>{{@lng.settings}}</h2>
<div class="tabbed">
	<div class="row">
		<div class="settingsMenu span2">
			<ul class="nav nav-tabs nav-stacked">
				<li class="active"><a href="#" title="tabContent_1">{{@lng.settings}}</a></li>
				<li><a href="#members" id="members" title="tabContent_2">{{@lng.members}}</a></li>
				<li><a href="#milestones" id="milestones" title="tabContent_3">{{@lng.milestones}}</a></li>
				<li><a href="#roles" id="roles" title="tabContent_4">{{@lng.roles}}</a></li>
				<li><a href="#categories" id="categories" title="tabContent_5">{{@lng.categories}}</a></li>		
			</ul>
		</div>

		{{* 

			Settings 

		*}}    
		<div class="tabContent span10" id="tabContent_1">
			<check if="{{@getPermission('proj_editProject')}}">
				<true>
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
						<input type="submit" value="{{@lng.save}}" class="btn btn-primary" />
					</form>
				</true>
				<false>
					<div class="alert alert-error">{{@lng.noAccess}}</div>
				</false>
			</check>
		</div>
		{{* 

			members 

		*}}
		<div class="tabContent span10" id="tabContent_2">
			<check if="{{@getPermission('proj_manageMembers')}}">
				<true>
					<table class="sortable table table-striped table-bordered">
						<thead>
							<tr>
								<th>Name</th>
								<th>Rolle</th> 
							</tr>
						</thead>
						<tbody>
						<repeat group="{{@projMembers}}" value="{{@member}}">
							<tr>
								<td class="title">
									<a href="{{@BASE}}/user/{{@member->name}}">{{@member->name}}</a>
								</td>
								<td class="type manageMember">
									<p>
									<form action="{{@BASE}}/project/settings/member/setrole" method="post" class="pull-left">
										<input type="hidden" name="user" value="{{@member->hash}}" />
										<select name="role" class="floatleft">
											<repeat group="{{@projRoles}}" value="{{@role}}">
												<option value="{{@role->hash}}" {{(@member->roleId == @role->id)?'selected="selected"':''}}>{{@role->name}}</option>
											</repeat>
										</select>
										<input type="submit" value="Ändern" class="btn" />
									</form>
									<form action="{{@BASE}}/project/setttings/member/delete" method="post" class="inline-form">
										<input type="hidden" name="user" value="{{@member->hash}}" />
										<input type="submit" value="{{@lng.delete}}" class="btn" />
									</form>
									</p>
								</td>
							</tr>
						</repeat>
						</tbody>
					</table>

					<form action="{{@BASE}}/project/settings/member/add" method="post" class="form-inline">
						<p class="addUser">
							<select name="member">
								<repeat group="{{@users}}" value="{{@user}}">
									<option value="{{@user->hash}}">{{@user->name}}</option>
								</repeat>
							</select>
							{{@lng.asRole}}
							<select name="role">
								<repeat group="{{@projRoles}}" value="{{@role}}">
									<option value="{{@role->hash}}">{{@role->name}}</option>
								</repeat>
							</select>
							<input type="submit" value="{{@lng.add}}" class="btn btn-primary" />
						</p>
					</form>
				</true>
				<false>
					<div class="alert alert-error">{{@lng.noAccess}}</div>
				</false>
			</check>
		</div>
		{{* 

			milestones 

		*}}
		<div class="tabContent span10" id="tabContent_3">
			<check if="{{@getPermission('proj_manageMilestones')}}">
				<true>
					<table class="sortable table table-striped table-bordered">
						<thead>
							<tr>
								<th>{{@lng.milestone}}</th>
								<th>{{@lng.actions}}</th>
							</tr>
						</thead>
						<tbody>
						<repeat group="{{@projMilestones}}" value="{{@milestone}}">
							<tr>
								<td class="title">
									<a href="{{@BASE}}/project/settings/milestone/{{@milestone->hash}}">{{@milestone->name}}</a>
								</td>
								<td>
									<a class="btn" href="{{@BASE}}/project/settings/milestone/delete/{{@milestone->hash}}" onclick="return confirm('{{@lng.sure}}');">{{@lng.delete}}</a>
									<a class="btn" href="{{@BASE}}/project/settings/milestone/{{@milestone->hash}}">{{@lng.edit}}</a>
								</td>
							</tr>
						</repeat>
						</tbody>
					</table>
					<a href="{{@BASE}}/project/settings/milestone/add" class="btn btn-primary">{{@lng.addmilestone}}</a>
				</true>
				<false>
					<div class="alert alert-error">{{@lng.noAccess}}</div>
				</false>
			</check>
		</div>
		{{* 

			roles 

		*}}
		<div class="tabContent span10" id="tabContent_4">
			<check if="{{@getPermission('proj_manageRoles')}}">
				<true>
					<table class="table-striped table sortable table-bordered">
						<thead>
							<tr>
								<th>{{@lng.role}}</th>
								<th>{{@lng.actions}}</th>
							</tr>
						</thead>
						<tbody>
						<repeat group="{{@projRoles}}" value="{{@role}}">
							<tr>
								<td class="title">
									<a href="{{@BASE}}/project/settings/role/{{@role->hash}}">{{@role->name}}</a>
								</td>
								<td>
									<a class="btn" href="{{@BASE}}/project/settings/role/delete/{{@role->hash}}">{{@lng.delete}}</a>
									<a class="btn" href="{{@BASE}}/project/settings/role/{{@role->hash}}">{{@lng.edit}}</a>
								</td>
							</tr>
						</repeat>
						</tbody>
					</table>
					<a href="{{@BASE}}/project/settings/role/add" class="btn btn-primary">{{@lng.addrole}}</a>
				</true>
				<false>
					<div class="alert alert-error">{{@lng.noAccess}}</div>
				</false>
			</check>
		</div>
		{{* 

			categories 

		*}}
		<div class="tabContent span10" id="tabContent_5">
			<check if="{{@getPermission('proj_manageCategories')}}">
				<true>
					<table class="table table-striped sortable table-bordered">
						<thead>
							<tr>
								<th>{{@lng.category}}</th>
								<th>{{@lng.actions}}</th>
							</tr>
						</thead>
						<tbody>
						<repeat group="{{@projCategories}}" value="{{@category}}">
							<tr>
								<td class="title">
									{{@category->name}}
								</td>
								<td>
									<a class="btn" href="{{@BASE}}/project/settings/category/delete/{{@category->hash}}">{{@lng.delete}}</a>
									<a class="btn" href="{{@BASE}}/project/settings/category/edit/{{@category->hash}}">{{@lng.edit}}</a>
								</td>
							</tr>
						</repeat>
						</tbody>
					</table>
					<a href="{{@BASE}}/project/settings/category/add" class="btn btn-primary">{{@lng.addcategory}}</a>
				</true>
				<false>
					<div class="alert alert-error">{{@lng.noAccess}}</div>
				</false>
			</check>
		</div>
	</div>
		
