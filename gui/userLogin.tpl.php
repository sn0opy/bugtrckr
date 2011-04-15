<h1>{@lng.login}</h1>

<form action="/{@BASE}user/login" method="post">
    <div class="formRow">
        <div class="formLabel">{@lng.email}: </div>
        <div class="formValue"><input type="text" name="email" /></div>
    </div>
    <div class="formRow">
        <div class="formLabel">{@lng.password}: </div>
        <div class="formValue"><input type="password" name="password" /></div>
    </div>
    <div class="formRow">
        <div class="formLabel">&nbsp;</div>
        <div class="formValue"><input type="submit" value="{@lng.login}" /></div>
    </div>
</form>