<?php


DB::sql('CREATE TABLE Ticket (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), title varchar(40), description text, created int, owner int, assigned int, type int, state int, priority int, category int, milestone int);');
DB::sql('CREATE TABLE User (id integer PRIMARY KEY AUTO_INCREMENT, name VARCHAR(45), hash varchar(12), password varchar(40), salt varchar(5), email varchar(50), admin int(1), lastProject int(11));');
DB::sql('CREATE TABLE Project (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), name VARCHAR(45), description TEXT, public int(1));');
DB::sql('CREATE TABLE Milestone (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), name varchar(45), description text, finished date, project int(11));');
DB::sql('CREATE TABLE Activity (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), description text, user int(11), changed int(11), project int(11), ticket int(11), comment TEXT);');
DB::sql('CREATE TABLE Role (id INTEGER PRIMARY KEY AUTO_INCREMENT , projectId int(11), hash VARCHAR(12), name VARCHAR(45), issuesAssigneable int(1), proj_editProject int(1), proj_manageMembers int(1), proj_manageRoles int(1), iss_editIssues int(1), iss_addIssues int(1), iss_deleteIssues int(1), iss_moveIssue int(1), iss_editWatchers int(1), iss_addWatchers int(1), iss_viewWatchers int(1));');
DB::sql('CREATE TABLE ProjectPermission (userId INTEGER, projectId INTEGER, roleId INTEGER, PRIMARY KEY (userId, projectId));');
DB::sql('CREATE TABLE ProjectAdmins (id INTEGER PRIMARY KEY AUTO_INCREMENT, userID INTEGER, projectID INTEGER);');
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
DB::sql('CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash FROM user, ticket WHERE user.id = ticket.owner;');

/* 'displayableticket' is still broken */
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