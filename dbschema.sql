--
-- Ticket
--
CREATE TABLE Ticket (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12) UNIQUE, title varchar(40), description text, created int, owner int, assigned int, type int, state int, priority int, category int, milestone int);
INSERT INTO Ticket (id, hash, title, description, created, owner, assigned, type, state, priority, category, milestone) VALUES (1, 'b026324c6904b2a9cb4b88d6d61c81d1', 'Example Ticket', 'This Ticket is just a little example', 1, 1, 2, 1, 1, 3, 1, 1);
--
-- User
--
CREATE TABLE User (id integer PRIMARY KEY AUTO_INCREMENT, name VARCHAR(45), hash varchar(12) UNIQUE, password varchar, salt varchar, email varchar, admin bool, lastProject int);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (1, 'adm', 'Ub026324c690', 1, 1, 'admin@bugtrckr', 1, 0);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (2, 'guest', '', 1, 1, 'guest', 1, 0);
INSERT INTO User (id, name, hash, password, salt, email, admin, lastProject) VALUES (10, 'johndoe', 'b0db90d72e28', 2, 2, 'johndoe@bugtrckr', 0, 0);
--
-- Project
--
CREATE TABLE Project (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12) UNIQUE, name VARCHAR, description TEXT, public BOOL);
INSERT INTO Project (id, hash, name, description, public) VALUES (1, 'b026324c6904', 'Example Project', 'Just a sample project', 1);
--
-- Milestone
--
CREATE TABLE Milestone (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12) UNIQUE, name varchar(30), description text, finished date, project int);
INSERT INTO Milestone (id, hash, name, description, finished, project) VALUES (1, 'b026324c6901', 'First Milestone', 'UUUUAH', '2001-02-02', 1);
--
-- Activity
--
CREATE TABLE Activity (id INTEGER PRIMARY KEY AUTO_INCREMENT, hash VARCHAR(12) UNIQUE, description text, user int, changed int, project int, ticket int, comment TEXT);
INSERT INTO Activity (id, hash, description, user, changed, project, ticket) VALUES (1, 'b026324c6904', 'Example Activity', 1, 1, 1, 1, 'This is a comment for that ticket');
--
-- Role
--
CREATE TABLE Role (id INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE , projectId INTEGER, hash VARCHAR(12), name VARCHAR, issuesAssigneable BOOL, proj_editProject BOOL, proj_manageMembers BOOL, proj_manageRoles BOOL, iss_editIssues BOOL, iss_addIssues BOOL, iss_deleteIssues BOOL, iss_moveIssue BOOL, iss_editWatchers BOOL, iss_addWatchers BOOL, iss_viewWatchers BOOL);
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
CREATE VIEW user_perms as SELECT * FROM User, ProjectPermission WHERE User.id = ProjectPermission.userId;

CREATE VIEW user_ticket AS SELECT User.hash as userhash, Ticket.hash as tickethash, * FROM User, Ticket WHERE User.id = Ticket.owner; -- modified: 13.5. 19:30

CREATE VIEW displayableticket AS 
    SELECT Priority.name as priorityname, Status.name as statusname, Type.name as typename,
            Owner.hash as userhash, Owner.name as username, Ticket.hash as tickethash,
            Assigned.hash as assignedhash, Assigned.name as assignedname, Category.name as categoryname, Ticket.* 
    FROM User as Owner, Status, Priority, Type, Category, Ticket LEFT OUTER JOIN User as Assigned
        ON Assigned.id = Ticket.assigned WHERE Owner.id = Ticket.owner AND Ticket.state = Status.id AND Ticket.priority = Priority.id 
        AND Type.id = Ticket.type AND Category.id = Ticket.category;

CREATE VIEW displayableactivity AS
    SELECT Activity.id, Activity.hash, Activity.description, Activity.changed, Activity.ticket, Activity.project, Activity.comment,
           User.name as username, User.hash as userhash
    FROM Activity, User 
    WHERE Activity.user = User.id;
