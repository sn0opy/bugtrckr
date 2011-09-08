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
CREATE TABLE Role (project CHAR(12), hash CHAR(12), name VARCHAR(40), "issuesAssigneable" BOOL, "proj_editProject" BOOL, "proj_manageMembers" BOOL, proj_manageRoles BOOL, "iss_editIssues" BOOL, "iss_addIssues" BOOL, "iss_deleteIssues" BOOL, "iss_moveIssue" BOOL, "iss_editWatchers" BOOL, "iss_addWatchers" BOOL, "iss_viewWatchers" BOOL);
--
-- ProjectPermission
--
CREATE TABLE ProjectPermission (user CHAR(12), project CHAR(12), role CHAR(12), PRIMARY KEY (user, project));
--
-- ProjectAdmins
--
CREATE TABLE ProjectAdmins (hash CHAR(12) PRIMARY KEY, user CHAR(12), project CHAR(12));

--
-- Priority
--
CREATE TABLE Priority
(
	id INTEGER PRIMARY KEY,
	name VARCHAR(20),
	lang VARCHAR(3)
);

INSERT INTO Priority (id, name, lang) VALUES (1, 'Sehr hoch', 'de');
INSERT INTO Priority (id, name, lang) VALUES (2, 'hoch', 'de');
INSERT INTO Priority (id, name, lang) VALUES (3, 'normal', 'de');
INSERT INTO Priority (id, name, lang) VALUES (4, 'niedrig', 'de');
INSERT INTO Priority (id, name, lang) VALUES (5, 'sehr niedrig', 'de');

--
-- Status
--
CREATE TABLE Status
(
	id INTEGER PRIMARY KEY,
	name VARCHAR(20),
	lang VARCHAR(3)
);

INSERT INTO Status (id, name, lang) VALUES (1, 'Neu', 'de');
INSERT INTO Status (id, name, lang) VALUES (2, 'Zugewiesen', 'de');
INSERT INTO Status (id, name, lang) VALUES (3, 'in Bearbeitung', 'de');
INSERT INTO Status (id, name, lang) VALUES (4, 'Test', 'de');
INSERT INTO Status (id, name, lang) VALUES (5, 'Geschlossen', 'de');

--
-- Type
--
CREATE TABLE Type
(
	id INTEGER PRIMARY KEY,
	name VARCHAR(20),
	lang VARCHAR(3)
);

INSERT INTO Type (id, name, lang) VALUES (1, 'Bug', 'de');
INSERT INTO Type (id, name, lang) VALUES (2, 'Feature', 'de');

--
-- Category
--
CREATE TABLE Category
(
    hash CHAR(12) PRIMARY KEY,
	project CHAR(12),
    name VARCHAR(40)
);

--
-- VIEWS
--
CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user = projectpermission.user;

CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash, * FROM user, ticket WHERE user.hash = ticket.owner; -- modified: 13.5. 19:30

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
