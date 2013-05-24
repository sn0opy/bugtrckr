<h2>{{@lng.category}} › {{@lng.editcategory}}</h2>

<div class="msSettings">
    <form action="/project/settings/category/edit" method="post">
        <div class="formRow">
            <div class="formLabel">
                {{@lng.name}}
            </div>
            <div class="formValue">
                <input type="text" name="name" value="{{@category->name}}" />
            </div>
        </div>
        
		<input type="hidden" name="hash" value="{{@category->hash}}" />
        <input type="submit" value="{{@lng.save}}" class="btn btn-primary" />
		<a href="/project/settings#categories" class="btn btn-default">{{@lng.cancel}}</a>
    </form>
</div>
