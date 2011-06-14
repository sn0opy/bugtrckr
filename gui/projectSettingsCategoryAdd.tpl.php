<h2>{{@lng.category}} › {{@lng.addcategory}}</h2>

<div class="msSettings">
    <form action="/{{@BASE}}project/settings/category/add" method="post">
        <div class="formRow">
            <div class="formLabel">
                {{@lng.name}}
            </div>
            <div class="formValue">
                <input type="text" name="name" />
            </div>
        </div>
        
        <input type="submit" value="{{@lng.save}}" />
    </form>
</div>