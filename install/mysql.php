<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

$this->get('DB')->sql('CREATE TABLE Ticket (hash CHAR(12) PRIMARY KEY, title VARCHAR(40), description text, created date, owner CHAR(12), assigned CHAR(12), type int, state int, priority int, category CHAR(12), milestone CHAR(12));');
$this->get('DB')->sql('CREATE TABLE User (name VARCHAR(40), hash CHAR(12) PRIMARY KEY, password CHAR(40), salt CHAR(32), email VARCHAR(40), admin bool, lastProject CHAR(12));');
$this->get('DB')->sql('CREATE TABLE Project (hash VARCHAR(12), name VARCHAR(45), description TEXT, public int(1));');
$this->get('DB')->sql('CREATE TABLE Milestone (hash CHAR(12) PRIMARY KEY, name VARCHAR(40), description text, finished date, project CHAR(12));');
$this->get('DB')->sql('CREATE TABLE Activity (hash CHAR(12) PRIMARY KEY, description text, user CHAR(12), changed date, project CHAR(12), ticket CHAR(12), comment TEXT, changedFields TEXT);');
$this->get('DB')->sql('CREATE TABLE Role (project CHAR(12), hash CHAR(12), name VARCHAR(40), issuesAssigneable BOOL, proj_editProject BOOL, proj_manageMembers BOOL, proj_manageMilestones BOOL, proj_manageRoles BOOL, iss_editIssues BOOL, iss_addIssues BOOL, iss_deleteIssues BOOL, iss_moveIssue BOOL, iss_editWatchers BOOL, iss_addWatchers BOOL, iss_viewWatchers BOOL, wiki_editWiki BOOL, proj_manageCategories BOOL);');
$this->get('DB')->sql('CREATE TABLE ProjectPermission (user CHAR(12), project CHAR(12), role CHAR(12), PRIMARY KEY (user, project));');
$this->get('DB')->sql('CREATE TABLE ProjectAdmins (hash CHAR(12) PRIMARY KEY, user CHAR(12), project CHAR(12));');
$this->get('DB')->sql('CREATE TABLE Category (hash CHAR(12) PRIMARY KEY, project CHAR(12), name VARCHAR(40));');
$this->get('DB')->sql('CREATE TABLE WikiEntry (hash CHAR(32) PRIMARY KEY, title VARCHAR(30), content TEXT, project CHAR(32), created DATE, created_by CHAR(32), edited DATE, edited_by CHAR(32));');
$this->get('DB')->sql('CREATE TABLE WikiDiscussion (hash CHAR(32) PRIMARY KEY, entry CHAR(32), content TEXT, created DATE, created_by CHAR(32));');

$this->get('DB')->sql('CREATE VIEW user_perms as SELECT * FROM User, ProjectPermission WHERE User.hash = ProjectPermission.user;');
$this->get('DB')->sql('CREATE VIEW user_ticket AS SELECT User.hash as userhash, Ticket.hash as tickethash FROM User, Ticket WHERE User.hash = Ticket.owner;');

$this->get('DB')->sql('CREATE VIEW displayableticket AS 
    SELECT owner.hash as userhash, owner.name as username, Ticket.hash as tickethash, 
            assigned.hash as assignedhash, assigned.name as assignedname, Category.name as categoryname, Ticket.*
    FROM User as owner, Category, Ticket LEFT OUTER JOIN User as assigned
        ON assigned.hash = Ticket.assigned
    WHERE owner.hash = Ticket.owner AND Category.hash = Ticket.category;');


$this->get('DB')->sql('CREATE VIEW displayableactivity AS
    SELECT Activity.hash, Activity.description, Activity.changed, Activity.ticket, Activity.project, Activity.comment, Activity.changedFields, 
           User.name as username, User.hash as userhash
    FROM Activity, User
    WHERE Activity.user = User.hash;');
