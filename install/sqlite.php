<?php

DB::sql('CREATE TABLE Ticket (hash CHAR(12) PRIMARY KEY, title VARCHAR(40), description text, created date, owner hash, assigned hash, type int, state int, priority int, category CHAR(12), milestone CHAR(12));');
DB::sql('CREATE TABLE User (name VARCHAR(40), hash CHAR(12) PRIMARY KEY, password CHAR(40), salt CHAR(32), email VARCHAR(40), admin bool, lastProject CHAR(12));');
DB::sql('CREATE TABLE Project (hash CHAR(12) PRIMARY KEY, name VARCHAR(40), description TEXT, public TINYINT(1));');
DB::sql('CREATE TABLE Milestone (hash CHAR(12) PRIMARY KEY, name VARCHAR(40), description text, finished date, project CHAR(12));');
DB::sql('CREATE TABLE Activity (hash CHAR(12) PRIMARY KEY, description text, user CHAR(12), changed date, project CHAR(12), ticket CHAR(12), comment TEXT, changedFields TEXT);');
DB::sql('CREATE TABLE Role (project CHAR(12), hash CHAR(12), name VARCHAR(40), issuesAssigneable BOOL, proj_editProject BOOL, proj_manageMembers BOOL, proj_manageMilestones BOOL, proj_manageRoles BOOL, iss_editIssues BOOL, iss_addIssues BOOL, iss_deleteIssues BOOL, iss_moveIssue BOOL, iss_editWatchers BOOL, iss_addWatchers BOOL, iss_viewWatchers BOOL, wiki_editWiki BOOL);');
DB::sql('CREATE TABLE ProjectPermission (user CHAR(12), project CHAR(12), role CHAR(12), PRIMARY KEY (user, project));');
DB::sql('CREATE TABLE ProjectAdmins (hash CHAR(12) PRIMARY KEY, user CHAR(12), project CHAR(12));');
DB::sql('CREATE TABLE Category (hash CHAR(12) PRIMARY KEY, project CHAR(12), name VARCHAR(40));');
DB::sql('CREATE TABLE WikiEntry (hash CHAR(32) PRIMARY KEY, title VARCHAR(30), content TEXT, project CHAR(32), created DATE, created_by CHAR(32), edited DATE, edited_by CHAR(32));');
DB::sql('CREATE TABLE WikiDiscussion (hash CHAR(32) PRIMARY KEY, entry CHAR(32), content TEXT, created DATE, created_by CHAR(32));');


DB::sql('CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user.hash = projectpermission.user;');
DB::sql('CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash, * FROM user, ticket WHERE user.hash = ticket.owner;');

DB::sql('CREATE VIEW displayableticket AS 
    SELECT owner.hash as userhash, owner.name as username, ticket.hash as tickethash,
            assigned.hash as assignedhash, assigned.name as assignedname, category.name as categoryname, Ticket.* 
    FROM ticket, user as owner, category LEFT OUTER JOIN user as assigned
        ON assigned.hash = ticket.assigned
    WHERE owner.hash = ticket.owner AND category.hash = ticket.category;');

DB::sql('CREATE VIEW displayableactivity AS
    SELECT activity.hash, activity.description, activity.changed, activity.ticket, activity.project, activity.comment, activity.changedFields,
           user.name as username, user.hash as userhash
    FROM activity, user 
    WHERE activity.user = user.hash;');

?>
