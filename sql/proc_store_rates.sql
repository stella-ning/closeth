delimiter $$

drop procedure store_rates$$

create procedure store_rates()
begin
  declare v_store_id, v_total_sales, v_total_lacks, v_total_backs, v_evaluation_count, v_praise_count int(10) unsigned;
  declare v_praise_rate float(5,2) unsigned;
  declare store_done int default false;
  declare store_cursor cursor for select store_id from ecm_store where state = 1;
  declare continue handler for not found set store_done = true;

  open store_cursor;

  store_loop: loop
    fetch store_cursor into v_store_id;
    if store_done then
      leave store_loop;
    end if;

    select sum(s.sales), sum(s.oos), sum(s.backs) into v_total_sales, v_total_lacks, v_total_backs from ecm_goods g left join ecm_goods_statistics s on g.goods_id = s.goods_id where store_id = v_store_id;

    if v_total_sales > 20 then
       select count(1) into v_evaluation_count from ecm_order_goods g left join ecm_order o on g.order_id = o.order_id where o.seller_id = v_store_id and o.evaluation_status = 1 and g.is_valid = 1;

       select count(1) into v_praise_count from ecm_order_goods g left join ecm_order o on g.order_id = o.order_id where o.seller_id = v_store_id and o.evaluation_status = 1 and g.is_valid = 1 and g.evaluation = 3;

       if v_evaluation_count > 0 then
          set v_praise_rate = v_praise_count/v_evaluation_count*100;
       else
          set v_praise_rate = 100;
       end if;

       insert into ecm_store_rates values (v_store_id, v_total_lacks/v_total_sales*100, v_total_backs/v_total_sales*100, v_praise_rate) on duplicate key update lack_rate = v_total_lacks/v_total_sales*100, back_rate = v_total_backs/v_total_sales*100, praise_rate = v_praise_rate;

       select concat('store ', v_store_id, ' updated') info;
    else
       select concat('store ', v_store_id, ' sales < 20') info;
    end if;

    set store_done = false;
    set v_store_id = null;
    set v_total_sales = null;
    set v_total_lacks = null;
    set v_total_backs = null;
    set v_evaluation_count = null;
    set v_praise_count = null;
    set v_praise_rate = null;
  end loop;

end$$

delimiter ;
