--
-- Ticket
--
CREATE TABLE Ticket (hash CHAR(12) PRIMARY KEY, title VARCHAR(40), description text, created date, owner hash, assigned hash, type int, state int, priority int, category CHAR(12), milestone CHAR(12));
--
-- User
--
CREATE TABLE User (name VARCHAR(40), hash CHAR(12) PRIMARY KEY, password CHAR(32), salt CHAR(32), email VARCHAR(40), admin bool, lastProject CHAR(12));
--
-- Project
--
CREATE TABLE Project (hash CHAR(12) PRIMARY KEY, name VARCHAR(40), description TEXT, public BOOL);
--
-- Milestone
--
CREATE TABLE Milestone (hash CHAR(12) PRIMARY KEY, name VARCHAR(40), description text, finished date, project CHAR(12));
--
-- Activity
--
CREATE TABLE Activity (hash CHAR(12) PRIMARY KEY, description text, user CHAR(12), changed date, project CHAR(12), ticket CHAR(12), comment TEXT);
--
-- Role
--
CREATE TABLE Role (project CHAR(12), hash CHAR(12), name VARCHAR(40), issuesAssigneable BOOL, proj_editProject BOOL, proj_manageMembers BOOL, proj_manageRoles BOOL, iss_editIssues BOOL, iss_addIssues BOOL, iss_deleteIssues BOOL, iss_moveIssue BOOL, iss_editWatchers BOOL, iss_addWatchers BOOL, iss_viewWatchers BOOL);
--
-- ProjectPermission
--
CREATE TABLE ProjectPermission (user CHAR(12), project CHAR(12), role CHAR(12), PRIMARY KEY (user, project));
--
-- ProjectAdmins
--
CREATE TABLE ProjectAdmins (hash CHAR(12) PRIMARY KEY, user CHAR(12), project CHAR(12));

-- Category
--
CREATE TABLE Category (hash CHAR(12) PRIMARY KEY, project CHAR(12), name VARCHAR(40));

--
-- Wiki Entries
--
CREATE TABLE WikiEntry (hash CHAR(32) PRIMARY KEY, title VARCHAR(30), content TEXT, project CHAR(32), created DATE, created_by CHAR(32), edited DATE, edited_by CHAR(32) );

CREATE TABLE WikiChanges
(
	
);

--
-- VIEWS
--
CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user = projectpermission.user;

CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash, * FROM user, ticket WHERE user.hash = ticket.owner;

CREATE VIEW displayableticket AS 
    SELECT priority.name as priorityname, status.name as statusname, type.name as typename,
            owner.hash as userhash, owner.name as username, ticket.hash as tickethash,
            assigned.hash as assignedhash, assigned.name as assignedname, category.name as categoryname, * 
    FROM ticket, user as owner, status, priority, type, category LEFT OUTER JOIN user as assigned
        ON assigned.hash = ticket.assigned
    WHERE owner.hash = ticket.owner AND ticket.state = status.id AND ticket.priority = priority.id 
        AND type.id = ticket.type AND category.hash = ticket.category;

CREATE VIEW displayableactivity AS
    SELECT activity.hash, activity.hash, activity.description, activity.changed, activity.ticket, activity.project, activity.comment,
           user.name as username, user.hash as userhash
    FROM activity, user 
    WHERE activity.user = user.hash;
