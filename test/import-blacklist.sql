drop table if exists blacklist;
create table if not exists blacklist(
    word varchar(50) primary key
) engine=innodb;

drop table if exists blacklist_letter_replacer;
create table if not exists blacklist_letter_replacer(
    letter_in varchar(3),
    letter_out varchar(3),
    unique index in_out (letter_in, letter_out)
) engine=innodb;
