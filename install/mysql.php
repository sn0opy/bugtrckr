<?php


DB::sql('CREATE TABLE Ticket (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), title varchar(40), description text, created int, owner int, assigned int, type int, state int, priority int, category int, milestone int);');
DB::sql('CREATE TABLE User (id integer PRIMARY KEY AUTO_INCREMENT, name VARCHAR(45), hash varchar(12), password varchar(40), salt varchar(5), email varchar(50), admin int(1), lastProject int(11));');
DB::sql('CREATE TABLE Project (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), name VARCHAR(45), description TEXT, public int(1));');
DB::sql('CREATE TABLE Milestone (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), name varchar(45), description text, finished date, project int(11));');
DB::sql('CREATE TABLE Activity (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12), description text, user int(11), changed int(11), project int(11), ticket int(11), comment TEXT);');
DB::sql('CREATE TABLE Role (id INTEGER PRIMARY KEY AUTO_INCREMENT , projectId int(11), hash VARCHAR(12), name VARCHAR(45), issuesAssigneable int(1), proj_editProject int(1), proj_manageMembers int(1), proj_manageRoles int(1), iss_editIssues int(1), iss_addIssues int(1), iss_deleteIssues int(1), iss_moveIssue int(1), iss_editWatchers int(1), iss_addWatchers int(1), iss_viewWatchers int(1));');
DB::sql('CREATE TABLE ProjectPermission (userId INTEGER, projectId INTEGER, roleId INTEGER, PRIMARY KEY (userId, projectId));');
DB::sql('CREATE TABLE ProjectAdmins (id INTEGER PRIMARY KEY AUTO_INCREMENT, userID INTEGER, projectID INTEGER);');
DB::sql('CREATE TABLE Category( id INTEGER PRIMARY KEY, name VARCHAR(20), projectId INT(11));');

DB::sql('CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user.id = projectpermission.userId;');
DB::sql('CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash FROM user, ticket WHERE user.id = ticket.owner;');

DB::sql('CREATE VIEW displayableticket AS 
    SELECT Priority.name as priorityname, Status.name as statusname, Type.name as typename, Owner.hash as userhash, Owner.name as username, Ticket.hash as tickethash, Assigned.hash as assignedhash, Assigned.name as assignedname, Category.name as categoryname, Ticket.* 
    FROM User as Owner, Status, Priority, Type, Category, Ticket 
    LEFT OUTER JOIN User as Assigned ON Assigned.id = Ticket.assigned 
    WHERE Owner.id = Ticket.owner AND Ticket.state = Status.id AND Ticket.priority = Priority.id AND Type.id = Ticket.type AND Category.id = Ticket.category;');

DB::sql('CREATE VIEW displayableactivity AS
    SELECT activity.id, activity.hash, activity.description, activity.changed, activity.ticket, activity.project, activity.comment,
           user.name as username, user.hash as userhash
    FROM activity, user 
    WHERE activity.user = user.id;');


?>