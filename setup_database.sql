create table page-data (
  id INT UNIQUE AUTO INCREMENT,
  data LONGTEXT,
  PRIMARY KEY(id)
) engine=InnoDB;

create table page-name (
  id INT UNIQUE AUTO INCREMENT,
  name VARCHAR(255) UNIQUE,
  data INT,
  headers INT,
  PRIMARY KEY(id),
  KEY(name),
  FOREIGN KEY(data) REFERENCES page-data(id),
  FOREIGN KEY(headers) REFERENCES page-data(id)
) engine=InnoDB;

create table page-variables (
  id INT UNIQUE AUTO INCREMENT,
  name VARCHAR(255),
  value MEDIUMTEX,
  PRIMARY KEY(id)
) engine=InnoDB;

create table name-variable-interp (
  id INT UNIQUE AUTO INCREMENT,
  page-name INT,
  variable INT,
  PRIMARY KEY(id),
  FOREIGN KEY(page-name) REFERENCES page-name(id),
  FOREIGN KEY(variable) REFERENCES page-variables(id)
) engine=InnoDB;

create table users (
  id INT AUTO INCREMENT UNIQUE,
  username VARCHAR(255) UNIQUE,
  password VARCHAR(512),
  contact VARCHAR(255),
  flags INT,
  PRIMARY KEY(id),
  KEY(username)
) engine=InnoDB;
