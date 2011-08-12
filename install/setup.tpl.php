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
        });
        </script>
	</head>

    <body>
        <div class="container">
            <div class="head">
                <h1>Bugtrckr Setup</h1>
            </div>
            <div id="content">
                <div class="content">
                    <h2>Schritt 1</h2>
                    <p>Lass uns doch erst mal schauen, was wir hier haben...</p>
                    <div class="formRow">
                        <div class="formLabel">
                            SQLite: 
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
                            Schreibrechte: 
                        </div>
                        <div class="formValue">
                            <F3:check if="{{@NEEDED.writepermission}}">
                                <true><span class="true">✔</span></true>
                                <false><span class="false">✖</span></false>
                            </F3:check>
                        </div>
                    </div>
                    <div class="formRow">
                        <div class="formLabel">
                            Config vorhanden: 
                        </div>
                        <div class="formValue">
                            <F3:check if="{{!@NEEDED.configexists}}">
                                <true><span class="false">✖ existiert schon</span></true>
                                <false><span class="true">✔</span></false>
                            </F3:check>
                        </div>
                    </div>
                    
                    <F3:check if="{{!@ERROR}}">
                        <true>
                            <br class="clearfix" />
                            <h2>Fehler</h2>
                            <p class="false">Es sind Fehler aufgetreten, bitte behebe diese zuerst und lade die Seite neu.</p>
                        </true>
                        <false>
                            <form action="{{@BASE}}/setup.php" method="post">
                                <br class="clearfix" />
                                <h2>Schritt 2</h2>
                                
                                <p class="dbtype" style="text-align: center; margin-bottom: 20px;">
                                    <input type="radio" name="dbtype" class="dbtype" value="sqlitedb" id="sqlitedb" checked="checked" />
                                    <label for="sqlitedb">SQLite</label>
                                    
                                    <input type="radio" name="dbtype" class="dbtype" value="mysqldb" id="mysqldb" />
                                    <label for="mysqldb">MySQL</label>
                                </p>
                                
                                <div id="sqliteChosen">
                                    <div class="formRow">
                                        <div class="formLabel">Datenbankname: </div>
                                        <div class="formValue"><input type="text" name="dbname" /></div>
                                    </div>
                                </div>
                                <div id="mysqlChosen">
                                    <div class="formRow">
                                        <div class="formLabel">Server: </div>
                                        <div class="formValue"><input type="text" name="sqlhost" value="localhost" /></div>
                                    </div>
                                    <div class="formRow">
                                        <div class="formLabel">Benutzer: </div>
                                        <div class="formValue"><input type="text" name="sqluser" /></div>
                                    </div>
                                    <div class="formRow">
                                        <div class="formLabel">Passwort: </div>
                                        <div class="formValue"><input type="password" name="sqlpw" /></div>
                                    </div>
                                    <div class="formRow">
                                        <div class="formLabel">Datenbank: </div>
                                        <div class="formValue"><input type="text" name="sqldb" /></div>
                                    </div>
                                </div>

                                <br class="clearfix" />
                                <h2>Schritt 3</h2>
                                <p>Zu letzt lege noch deinen Adminaccount an</p>

                                <div class="formRow">
                                    <div class="formLabel">Name: </div>
                                    <div class="formValue"><input type="text" name="name" /></div>
                                </div>
                                <div class="formRow">
                                    <div class="formLabel">Passwort: </div>
                                    <div class="formValue"><input type="password" name="password" /></div>
                                </div>
                                <div class="formRow">
                                    <div class="formLabel">Passwort wdh: </div>
                                    <div class="formValue"><input type="password" name="passwordre" /></div>
                                </div>
                                <div class="formRow">
                                    <div class="formLabel">E-Mail: </div>
                                    <div class="formValue"><input type="text" name="email" /></div>
                                </div>
                                
                                <input type="submit" value="Okay, auf geht's" />
                            </form>
                        </false>
                    </F3:check>
                    <br class="clearfix" />
                </div>
            </div>
            <div class="footer"></div>
        </div>
    </body>
</html>