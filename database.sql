CREATE DATABASE IF NOT EXISTS api_rest_symfony;
USE api_rest_symfony;

CREATE TABLE users(
id             int(255) auto_increment not null,
name           varchar(50) NOT NULL,
surname        varchar(150),
email          varchar(255) NOT NULL,
password       varchar(255) NOT NULL,
role           varchar(20) NOT NULL,
created_at     datetime DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;


CREATE TABLE videos(
id           int(255) auto_increment not null,
user_id      int(255) not null,
title        varchar(255) not null,
description  text, 
url          varchar(255) not null,
status       varchar(255) not null,
created_at     datetime DEFAULT CURRENT_TIMESTAMP,
updated_at     datetime DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT pk_videos PRIMARY KEY(id),
CONSTRAINT fk_videos_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;