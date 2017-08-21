delimiter $$

drop procedure register_user$$

create procedure register_user(
  in i_username varchar(255),
  in i_password varchar(255)
)
begin
  declare v_username, v_salt varchar(255);
  declare v_uid int(10) unsigned;
  select username into v_username from ucenter51.uc_members where username = i_username;

  if v_username is null then
    set v_username = i_username;
    set v_salt = substr(rand(), 3, 4);
    insert into ucenter51.uc_members set secques='', username=v_username, password=md5(concat(md5(i_password), v_salt)), email=concat(v_username, '@51zwd.com'), regip='112.124.54.224', regdate=1443279999, salt=v_salt;
    set v_uid = last_insert_id();
    insert into ucenter51.uc_memberfields set uid = v_uid;
    insert into ecm_member set user_id=v_uid, user_name=v_username, password=md5(concat(md5(i_password), v_salt)), email=concat(v_username, '@51zwd.com'), reg_time=1443279999;
  end if;

  set v_username = null;
  set v_uid = null;
end$$

delimiter ;


call register_user('noreply', 'SJloCA3WCtf9His0B18TsRtFI0E=');
