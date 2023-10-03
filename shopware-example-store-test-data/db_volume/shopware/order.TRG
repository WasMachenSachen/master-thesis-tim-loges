TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`%` TRIGGER order_cash_rounding_insert BEFORE INSERT ON `order`\n            FOR EACH ROW\n            BEGIN\n                IF @TRIGGER_DISABLED IS NULL OR @TRIGGER_DISABLED = 0 THEN\n                    IF NEW.item_rounding IS NULL THEN\n                        SET NEW.item_rounding = (SELECT item_rounding FROM currency WHERE id = NEW.currency_id);\n                        SET NEW.total_rounding = NEW.item_rounding;\n                    END IF;\n                END IF;\n            END'
sql_modes=1409286144
definers='root@%'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_general_ci'
db_cl_names='utf8mb4_unicode_ci'
created=167770406591
