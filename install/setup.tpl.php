<!doctype html>
<html>
	<head>
        <meta charset="UTF-8" />
		<title>Bugtrckr Setup</title>
		<link href="{{@BASE}}/install/style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="{{@BASE}}/gui/js/jquery.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            $('.dbtype').click(function() {
                if($(this).val() == 'sqlitedb') {
                    $('#sqliteChosen').show();
                    $('#mysqlChosen').hide();
                } 
                
                if($(this).val() == 'mysqldb'){
                    $('#sqliteChosen').hide();
                    $('#mysqlChosen').show();                    
                }
            });            
            
            
            $('#newadm').change(function() {
                if($('#admpw').val() == $('#admpwre').val()) {
                    $('#admpwreerr').fadeOut();
                } else {
                    $('#admpwreerr').fadeIn();
                }
            });
            
            $('#theform').submit(function() {
                if($('#sqlitedb').val() == true) {
                    if($('#sqlitedb').val() == "") {
                        $('#sqlitedberr').fadeIn();
                        return false;
                    }
                }
            });
            
            <F3:check if="{{@mysqldata}}">
                $('#sqliteChosen').hide();
                $('#mysqlChosen').show(); 
            </F3:check>
        });
        </script>
	</head>

    <body>
        <div class="container">
            <div class="head">
                <h1><a href="{{@BASE}}/setup.php">Bugtrckr Setup</a></h1>
            </div>
            <div id="content">
                <F3:check if="{{@dbexists}}">
                    <p class="failure message">{{@lng.dbexists}}</p>
                </F3:check>
                
                <F3:check if="{{@mysqldata}}">
                    <p class="failure message">{{@lng.mysqlfailed}}</p>
                </F3:check>
                
                
                <F3:check if="{{@usererror}}">
                    <p class="failure message">{{@lng.usererror}}</p>
                </F3:check>
                
                <F3:check if="{{@INSTALLED}}">
                    <F3:true>
                        <div class="content">
                            <p class="success message">
                            {{@lng.done}}
                            <a href="{{@BASE}}/"> <br/>&raquo; {{@lng.toApp}}</a></p>
                        </div>
                    </F3:true>
                    <F3:false>
                    <div class="content">
                        <h2>{{@lng.step1}}</h2>
                        <p>{{@lng.letssee}}</p>
                        <div class="formRow">
                            <div class="formLabel">
                                SQLite <small>(pdo)</small>: 
                            </div>
                            <div class="formValue">
                                <F3:check if="{{@NEEDED.sqlite}}">
                                    <true><span class="true">✔</span></true>
                                    <false><span class="false">✖</span></false>
                                </F3:check>
                            </div>
                        </div>
                        <div class="formRow">
                            <div class="formLabel">
                                MySQL <small>(pdo)</small>: 
                            </div>
                            <div class="formValue">
                                <F3:check if="{{@NEEDED.mysql}}">
                                    <true><span class="true">✔</span></true>
                                    <false><span class="false">✖</span></false>
                                </F3:check>
                            </div>
                        </div>
                        <div class="formRow">
                            <div class="formLabel">
                                {{@lng.writePermission}}: 
                            </div>
                            <div class="formValue">
                                <F3:check if="{{@NEEDED.writepermission}}">
                                    <true><span class="true">✔</span></true>
                                    <false><span class="false">✖ {{@lng.permsOn}} <em>data/</em></span></false>
                                </F3:check>
                            </div>
                        </div>
                        <div class="formRow">
                            <div class="formLabel">
                                {{@lng.confExists}}: 
                            </div>
                            <div class="formValue">
                                <F3:check if="{{@NEEDED.configexists}}">
                                    <true><span class="false">✖ {{@lng.alreadyExists}}</span></true>
                                    <false><span class="true">✔</span></false>
                                </F3:check>
                            </div>
                        </div>

                        <F3:check if="{{@BERROR}}">
                            <true>
                                <br class="clearfix" />
                                <h2>{{@lng.error}}</h2>
                                <p class="false">{{@lng.fixErrors}}</p>
                            </true>
                            <false>
                                <form action="{{@BASE}}/setup.php" method="post" id="theform">
                                    <br class="clearfix" />
                                    <h2>{{@lng.step2}}</h2>

                                    <p class="dbtype" style="text-align: center; margin-bottom: 20px;">
                                        <input type="radio" name="dbtype" class="dbtype" value="sqlitedb" id="sqlitedb" {{@mysqldata?@'':'checked="checked"'}} />
                                        <label for="sqlitedb">SQLite</label>

                                        <input type="radio" name="dbtype" class="dbtype" value="mysqldb" id="mysqldb" {{@mysqldata?'checked="checked"':''}}/>
                                        <label for="mysqldb">MySQL</label>
                                    </p>

                                    <div id="sqliteChosen">
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.dbname}}: </div>
                                            <div class="formValue"><input type="text" name="dbname" id="sqlitedb" value="bugtrckr.db" /> <span class="false" id="sqlitedberr" style="display: none;">{{@lng.notEmpty}}</span></div>
                                        </div>
                                    </div>
                                    <div id="mysqlChosen">
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.server}}: </div>
                                            <div class="formValue"><input type="text" name="sqlhost" value="{{@mysqldata?@mysqldata.host:'localhost'}}" /></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.user}}: </div>
                                            <div class="formValue"><input type="text" name="sqluser" value="{{@mysqldata?@mysqldata.user:''}}" /></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.password}}: </div>
                                            <div class="formValue"><input type="password" name="sqlpw" /></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.dbname}}: </div>
                                            <div class="formValue"><input type="text" name="sqldb" value="{{@mysqldata?@mysqldata.db:'bugtrckr'}}" /></div>
                                        </div>
                                    </div>

                                    <br class="clearfix" />
                                    
                                    <h2>{{@lng.step3}}</h2>
                                    <p>{{@lng.lastStep}}</p>
                                    <div id="newadm">
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.name}}: </div>
                                            <div class="formValue"><input type="text" name="name" /></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.password}}: </div>
                                            <div class="formValue"><input type="password" name="pw" id="admpw" /></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.passwordRepeat}}: </div>
                                            <div class="formValue"><input type="password" name="pwre" id="admpwre" /> <span class="false" id="admpwreerr" style="display: none;">{{@lng.noMatch}}</span></div>
                                        </div>
                                        <div class="formRow">
                                            <div class="formLabel">{{@lng.email}}: </div>
                                            <div class="formValue"><input type="text" name="email" /></div>
                                        </div>
                                    </div>

                                    <input type="submit" value="{{@lng.letsGo}}" />
                                </form>
                            </false>
                        <br class="clearfix" />
                        </F3:check>
                    </div>
                    </F3:false>
                </F3:check>
            </div>
            <div class="footer"></div>
        </div>
    </body>
</html>