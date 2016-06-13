#-- drop table if exists rule;
create table rule (
  id int primary key auto_increment,
  conditionString text,
  actionString text,
  priority int default 0,
  active boolean default true,
  ruleset varchar(10) # To categorize the rules	
);
