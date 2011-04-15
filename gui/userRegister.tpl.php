<form action="/{@BASE}user/new" method="post">
    <div class="formRow">
        <div class="formLabel">{@lng.name}: </div>
        <div class="formValue"><input type="text" name="name" /></div>
    </div>
    <div class="formRow">
        <div class="formLabel">{@lng.password}: </div>
        <div class="formValue"><input type="password" name="password" /></div>
    </div>
    <div class="formRow">
        <div class="formLabel">{@lang.email}: </div>
        <div class="formValue"><input type="text" name="email" /></div>
    </div>
    <div class="formRow">
        <div class="formLabel">&nbsp;</div>
        <div class="formValue"><input type="submit" value="{@lng.submit}" /></div>
    </div>
</form>