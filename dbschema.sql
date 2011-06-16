--
-- Ticket
--
CREATE TABLE Ticket (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, title varchar(40), description text, created int, owner int, assigned int, type int, state int, priority int, category int, milestone int);
INSERT INTO Ticket (id, hash, title, description, created, owner, assigned, type, state, priority, category, milestone) VALUES (1, 'b026324c6904b2a9cb4b88d6d61c81d1', 'Example Ticket', 'This Ticket is just a little example', 1, 1, 2, 1, 1, 3, 1, 1);
--
-- User
--
CREATE TABLE User (id integer PRIMARY KEY AUTOINCREMENT, name VARCHAR(45), hash varchar(12) UNIQUE, password varchar, salt varchar, email varchar, admin bool, lastProject int);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (1, 'adm', 'Ub026324c690', 1, 1, 'admin@bugtrckr', 1, 0);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (2, 'guest', '', 1, 1, 'guest', 1, 0);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (10, 'johndoe', 'b0db90d72e28', 2, 2, 'johndoe@bugtrckr', 0, 0);
--
-- Project
--
CREATE TABLE Project (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, name VARCHAR, description TEXT, public BOOL);
INSERT INTO Project (id, hash, name, description, public) VALUES (1, 'b026324c6904', 'Example Project', 'Just a sample project', 1);
--
-- Milestone
--
CREATE TABLE Milestone (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, name varchar, description text, finished int, project int);
INSERT INTO Milestone (id, hash, name, description, finished, project) VALUES (1, 'b026324c6901', 'First Milestone', 'UUUUAH', 1, 1);
--
-- Activity
--
CREATE TABLE Activity (id INTEGER PRIMARY KEY AUTOINCREMENT, hash VARCHAR(12) UNIQUE, description text, user int, changed int, project int, ticket int);
INSERT INTO Activity (id, hash, description, user, changed, project, ticket) VALUES (1, 'b026324c6904', 'Example Activity', 1, 1, 1, 1);
--
-- Role
--
CREATE TABLE Role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE , projectId INTEGER, hash VARCHAR(12), name VARCHAR, "issuesAssigneable" BOOL, "proj_editProject" BOOL, "proj_manageMembers" BOOL, proj_manageRoles BOOL, "iss_editIssues" BOOL, "iss_addIssues" BOOL, "iss_deleteIssues" BOOL, "iss_moveIssue" BOOL, "iss_editWatchers" BOOL, "iss_addWatchers" BOOL, "iss_viewWatchers" BOOL);--
--
-- ProjectPermission
--
CREATE TABLE ProjectPermission (userId INTEGER, projectId INTEGER, roleId INTEGER, PRIMARY KEY (userId, projectId));
--
-- ProjectAdmins
--
CREATE TABLE ProjectAdmins (id INTEGER PRIMARY KEY AUTOINCREMENT, userID INTEGER, projectID INTEGER);

--
-- Priority
--
CREATE TABLE Priority
(
	id INTEGER PRIMARY KEY,
	name VARCHAR(20),
	lang VARCHAR(5)
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
	lang VARCHAR(5)
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
	lang VARCHAR(5)
);

INSERT INTO Type (id, name, lang) VALUES (1, 'Bug', 'de');
INSERT INTO Type (id, name, lang) VALUES (2, 'Feature', 'de');

--
-- Category
--
CREATE TABLE Category
(
    id INTEGER PRIMARY KEY,
    name VARCHAR(20)
);

INSERT INTO Category (id, name) VALUES (1, 'Test');
INSERT INTO Category (id, name) VALUES (2, 'Test2');

--
-- VIEWS
--
CREATE VIEW user_perms as SELECT * FROM user, projectpermission WHERE user.id = projectpermission.userId;
CREATE VIEW user_ticket AS SELECT user.hash as userhash, ticket.hash as tickethash, * FROM user, ticket WHERE user.id = ticket.owner; -- modified: 13.5. 19:30
CREATE VIEW displayableticket AS 
    SELECT priority.name as priorityname, status.name as statusname, type.name as typename,
            owner.hash as userhash, owner.name as username, ticket.hash as tickethash,
            assigned.hash as assignedhash, assigned.name as assignedname, category.name as categoryname, * 
    FROM ticket, user as owner, status, priority, type, category LEFT OUTER JOIN user as assigned
        ON assigned.id = ticket.assigned
    WHERE owner.id = ticket.owner AND ticket.state = status.id AND ticket.priority = priority.id 
        AND type.id = ticket.type AND category.id = ticket.category;
CREATE VIEW displayableactivity AS
    SELECT activity.id, activity.hash, activity.description, activity.changed, activity.ticket, activity.project,
           user.name as username, user.hash as userhash
    FROM activity, user 
    WHERE activity.user = user.id;