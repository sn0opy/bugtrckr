<?php


DB::sql('CREATE TABLE Ticket (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, title varchar(40), description text, created int, owner int, assigned int, type int, state int, priority int, category int, milestone int);');
DB::sql('CREATE TABLE User (id integer PRIMARY KEY AUTOINCREMENT, name VARCHAR(45), hash varchar(12) UNIQUE, password varchar, salt varchar, email varchar, admin bool, lastProject int);');
DB::sql('CREATE TABLE Project (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, name VARCHAR, description TEXT, public BOOL);');
DB::sql('CREATE TABLE Milestone (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, name varchar, description text, finished int, project int);');
DB::sql('CREATE TABLE Activity (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, description text, user int, changed int, project int, ticket int, comment TEXT);');
DB::sql('CREATE TABLE Role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE , projectId INTEGER, hash VARCHAR(12), name VARCHAR, "issuesAssigneable" BOOL, "proj_editProject" BOOL, "proj_manageMembers" BOOL, proj_manageRoles BOOL, "iss_editIssues" BOOL, "iss_addIssues" BOOL, "iss_deleteIssues" BOOL, "iss_moveIssue" BOOL, "iss_editWatchers" BOOL, "iss_addWatchers" BOOL, "iss_viewWatchers" BOOL);');
DB::sql('CREATE TABLE ProjectPermission (userId INTEGER, projectId INTEGER, roleId INTEGER, PRIMARY KEY (userId, projectId));');
DB::sql('CREATE TABLE ProjectAdmins (id INTEGER PRIMARY KEY AUTOINCREMENT, userID INTEGER, projectID INTEGER);');
DB::sql('CREATE TABLE Category( id INTEGER PRIMARY KEY, name VARCHAR(20));');

DB::sql('CREATE TABLE Priority ( id INTEGER PRIMARY KEY, name VARCHAR(20), lang VARCHAR(5));');
DB::sql('INSERT INTO Priority (id, name, lang) VALUES (1, \'sehr hoch\', \'de\');');
DB::sql('INSERT INTO Priority (id, name, lang) VALUES (2, \'hoch\', \'de\');');
DB::sql('INSERT INTO Priority (id, name, lang) VALUES (3, \'normal\', \'de\');');
DB::sql('INSERT INTO Priority (id, name, lang) VALUES (4, \'niedrig\', \'de\');');
DB::sql('INSERT INTO Priority (id, name, lang) VALUES (5, \'sehr niedrig\', \'de\');');

DB::sql('CREATE TABLE Status(id INTEGER PRIMARY KEY, name VARCHAR(20), lang VARCHAR(5));');
DB::sql('INSERT INTO Status (id, name, lang) VALUES (1, \'Neu\', \'de\');');
DB::sql('INSERT INTO Status (id, name, lang) VALUES (2, \'Zugewiesen\', \'de\');');
DB::sql('INSERT INTO Status (id, name, lang) VALUES (3, \'in Bearbeitung\', \'de\');');
DB::sql('INSERT INTO Status (id, name, lang) VALUES (4, \'Test\', \'de\');');
DB::sql('INSERT INTO Status (id, name, lang) VALUES (5, \'Geschlossen\', \'de\');');

DB::sql('CREATE TABLE Type( id INTEGER PRIMARY KEY, name VARCHAR(20), lang VARCHAR(5));');
DB::sql('INSERT INTO Type (id, name, lang) VALUES (1, \'Bug\', \'de\');');
DB::sql('INSERT INTO Type (id, name, lang) VALUES (2, \'Feature\', \'de\');');

DB::sql('CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user.id = projectpermission.userId;');
DB::sql('CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash, * FROM user, ticket WHERE user.id = ticket.owner;');

DB::sql('CREATE VIEW displayableticket AS 
    SELECT priority.name as priorityname, status.name as statusname, type.name as typename,
            owner.hash as userhash, owner.name as username, ticket.hash as tickethash,
            assigned.hash as assignedhash, assigned.name as assignedname, category.name as categoryname, * 
    FROM ticket, user as owner, status, priority, type, category LEFT OUTER JOIN user as assigned
        ON assigned.id = ticket.assigned
    WHERE owner.id = ticket.owner AND ticket.state = status.id AND ticket.priority = priority.id 
        AND type.id = ticket.type AND category.id = ticket.category;');

DB::sql('CREATE VIEW displayableactivity AS
    SELECT activity.id, activity.hash, activity.description, activity.changed, activity.ticket, activity.project, activity.comment,
           user.name as username, user.hash as userhash
    FROM activity, user 
    WHERE activity.user = user.id;');


?>