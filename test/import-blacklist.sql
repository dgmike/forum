drop table if exists blacklist;
create table if not exists blacklist(
    word varchar(50) primary key
) engine=innodb;

drop table if exists blacklist_letter_replacer;
create table if not exists blacklist_letter_replacer(
    word_in varchar(3), word_out varchar(3),
    index in_out (word_in, word_out)
) engine=innodb;
