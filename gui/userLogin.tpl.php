<h2>{{@lng.login}}</h2>

<F3:check if="{{@SESSION.userId}}">
    <F3:true>
        <p>{{@lng.alreadyLoggedIn}}</p>
    </F3:true>
    <F3:false>
        <form action="{{@BASE}}user/login" method="post">
            <div class="formRow">
                <div class="formLabel">{{@lng.email}}: </div>
                <div class="formValue"><input type="text" name="email" /></div>
            </div>
            <div class="formRow">
                <div class="formLabel">{{@lng.password}}: </div>
                <div class="formValue"><input type="password" name="password" /></div>
            </div>
            <div class="formRow">
                <div class="formLabel"> </div>
                <div class="formValue"><input type="submit" value="{{@lng.login}}" /></div>
            </div>
        </form>
    <F3:false>
</F3:check>