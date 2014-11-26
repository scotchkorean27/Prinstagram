/*

	Project Part 2 sql 

*/
-- PERSON
create table person(
	username varchar(100),
	password  char(32),
	fname varchar(100),
	lname varchar(100),
	PRIMARY KEY (username)
);

-- PHOTO
create table photo(
	pid integer auto_increment,
	poster varchar(100),
	caption varchar(100),
	pdate datetime,
	lnge varchar(100),
	lat varchar(100),
	lname varchar(100),
	is_pub boolean,
	image varchar(300),
	FOREIGN KEY (poster) REFERENCES person(username),
	PRIMARY KEY (pid)
);

-- FRIENDGROUP
create table friendGroup(
	gname varchar(100),
	descr varchar(255),
	ownername varchar(100),
	PRIMARY KEY(gname, ownername),
	FOREIGN KEY (ownername) REFERENCES person(username)
);

-- COMMENT
create table comment(
	cid integer auto_increment,
	ctime datetime,
	ctext varchar(255),
	PRIMARY KEY (cid)
);


-- inGroup
create table inGroup(
	ownername varchar(100),
	gname varchar(100),
	username varchar(100),
	FOREIGN KEY (ownername, gname) REFERENCES friendGroup(ownername, gname),
	FOREIGN KEY (username) REFERENCES person(username),
	PRIMARY KEY (ownername, gname, username)
);
	

-- tag
create table tag(
	pid integer ,
	tagger varchar(100),
	taggee varchar(100),
	ttime datetime,
	tstatus boolean,
	PRIMARY KEY (pid, tagger, taggee),
	FOREIGN KEY (pid) REFERENCES photo(pid),
	FOREIGN KEY (tagger) REFERENCES person(username),
	FOREIGN KEY (taggee) REFERENCES person(username)
);


-- commentOn
create table commentOn(
	cid integer,
	pid integer,
	username varchar(100),
	PRIMARY KEY (cid, pid, username),
	FOREIGN KEY (cid) REFERENCES comment(cid),
	FOREIGN KEY (pid) REFERENCES photo(pid),
	FOREIGN KEY (username) REFERENCES person(username) 
);


-- shared
create table shared(
	pid integer,
	gname varchar(100),
	ownername varchar(100),
	FOREIGN KEY (pid) REFERENCES photo(pid),
	FOREIGN KEY (gname, ownername) REFERENCES friendGroup(gname, ownername),
	PRIMARY KEY (pid, gname, ownername)
);

-- Qwitter Functionality
create table pweets(
  s_id varchar(30) NOT NULL,
  r_id varchar(30) NOT NULL,
  mess varchar(255) NOT NULL,
  mdate datetime NOT NULL,
  FOREIGN KEY (s_id) references person(username),
  FOREIGN KEY (r_id) references person(username)
);
