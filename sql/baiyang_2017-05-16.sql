DELIMITER $$
DROP PROCEDURE IF EXISTS proc_statis_goods $$

CREATE PROCEDURE proc_statis_goods(
)
BEGIN
	 DECLARE n_total_online_goods,n_max_views,n_max_sales,n_max_behalf ,max_goods_id ,min_goods_id INT(10) UNSIGNED DEFAULT 0;
	 DECLARE tmp_total_online_goods,tmp_max_views,tmp_max_sales,tmp_max_behalf INT(10) UNSIGNED;
	 DECLARE limit_num int default 10000;
	 DECLARE i int DEFAULT 0;
	 DECLARE start_num,end_num int default 0;

	 select max(goods_id),min(goods_id) into max_goods_id , min_goods_id from ecm_goods ;
		
	 set i = min_goods_id;

	 goods_loop: LOOP
			set start_num = i;
			set end_num = start_num+limit_num;
			set i = end_num;

			select start_num , end_num;
			
			select count(g.goods_id) , max(gs.views) ,max(gs.sales)  into tmp_total_online_goods,tmp_max_views,tmp_max_sales
      from (select * from ecm_goods where goods_id >= start_num and goods_id < end_num ) g
      LEFT JOIN ecm_store s ON g.store_id = s.store_id
      LEFT JOIN ecm_goods_statistics gs ON g.goods_id = gs.goods_id
      where g.if_show = 1 AND g.closed = 0 AND g.default_spec > 0 AND s.state = 1;
			
			if tmp_total_online_goods = 0 THEN
				ITERATE goods_loop;
			end if;
			
			IF n_max_views < tmp_max_views then set n_max_views = tmp_max_views;
			END if;

			IF n_max_sales < tmp_max_sales then set n_max_sales = tmp_max_sales;
			END if;

			set n_total_online_goods = n_total_online_goods + tmp_total_online_goods;		
			set i = i+1;

			select max(goods_quantity) into tmp_max_behalf
        from (	SELECT sum(gw.goods_quantity) AS goods_quantity
								FROM	ecm_goods_warehouse gw
								,(select * from ecm_goods where goods_id >= start_num and goods_id<end_num) g 
								, ecm_store s 
								WHERE g.store_id = s.store_id
								AND gw.goods_id = g.goods_id
								AND g.if_show = 1
								AND g.closed = 0
								AND g.default_spec > 0
								AND s.state = 1
								GROUP BY gw.goods_id
                ) as stats;	

			IF n_max_behalf < tmp_max_behalf then set n_max_behalf = tmp_max_behalf;
			END if;

			if end_num > max_goods_id then 
				leave goods_loop;
			end if;

	end loop;
	INSERT into ecm_statis_goods (`total_online_goods` , `max_views` , `max_sales` , `max_behalf`,`ins_date` ) values (n_total_online_goods ,n_max_views , n_max_sales, n_max_behalf , NOW());
END
$$
DELIMITER ;
