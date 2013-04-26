<h2>{{@lng.registration}}</h2>

<F3:check if="{{isset(@SESSION.user.hash)}}">
    <F3:true>
        <p>{{@lng.alreadyLoggedIn}}</p>
    </F3:true>
    <F3:false>
        <form action="user/new" method="post">
            <div class="formRow">
                <div class="formLabel">{{@lng.name}}: </div>
                <div class="formValue"><input type="text" name="name" /></div>
            </div>
            <div class="formRow">
                <div class="formLabel">{{@lng.password}}: </div>
                <div class="formValue"><input type="password" name="password" /></div>
            </div>
            <div class="formRow">
                <div class="formLabel">{{@lng.email}}: </div>
                <div class="formValue"><input type="text" name="email" /></div>
            </div>
            <div class="formRow">
                <div class="formLabel"> </div>
                <div class="formValue"><input type="submit" value="{{@lng.register}}" /></div>
            </div>
        </form>
    </F3:false>
</F3:check>
