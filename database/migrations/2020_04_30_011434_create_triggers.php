<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `cash_flows__update_treasury_on_creation` AFTER INSERT ON `cash_flows` FOR EACH ROW BEGIN 
                SET @amount = NEW.amount; 
                SET @cashFlowId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN 
                    SET @treasury = @amount; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury + @amount; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, balance) VALUES (\'CASH_FLOW\', @cashFlowId, @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `cash_flows__update_treasury_on_delete` AFTER DELETE ON `cash_flows` FOR EACH ROW BEGIN 
                SET @amount = OLD.amount; 
                SET @cashFlowId = OLD.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN 
                    # Should never happen
                    SET @treasury = -@amount; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury - @amount; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'CASH_FLOW\', @cashFlowId, \'DELETE\', @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `cash_flows__update_treasury_on_update` AFTER UPDATE ON `cash_flows` FOR EACH ROW BEGIN 
                SET @oldAmount = OLD.amount; 
                SET @newAmount = NEW.amount; 
                SET @cashFlowId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
                
                IF @oldAmount != @newAmount THEN
                
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                    IF @nbTreasury = 0 THEN 
                        # Should never happen
                        SET @treasury = - @oldAmount + @newAmount; 
                    ELSE 
                        SELECT balance into @treasury 
                            FROM treasury 
                            ORDER BY id DESC
                            LIMIT 1; 
            
                        SET @treasury = @treasury - @oldAmount + @newAmount; 
                    END IF;
            
                    INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'CASH_FLOW\', @cashFlowId, \'UPDATE\', @treasury); 
                
                END IF;
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `orders__update_treasury_on_creation` AFTER INSERT ON `orders` FOR EACH ROW BEGIN 
                SET @price = NEW.final_price; 
                SET @orderId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN 
                    SET @treasury = @price; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury + @price; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, balance) VALUES (\'ORDER\', @orderId, @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `orders__update_treasury_on_delete` AFTER DELETE ON `orders` FOR EACH ROW BEGIN 
                SET @price = OLD.final_price; 
                SET @orderId = OLD.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN
                    # Should never happen
                    SET @treasury = -@price; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury - @price; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'ORDER\', @orderId, \'DELETE\', @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `orders__update_treasury_on_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN 
                SET @oldPrice = OLD.final_price; 
                SET @newPrice = NEW.final_price; 
                SET @orderId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                # Only if the price changed
                IF @oldPrice != @newPrice THEN
            
                    SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                    IF @nbTreasury = 0 THEN 
                        # Should never happen
                        SET @treasury = -@oldPrice + @newPrice; 
                    ELSE 
                        SELECT balance into @treasury 
                            FROM treasury 
                            ORDER BY id DESC
                            LIMIT 1; 
            
                        SET @treasury = @treasury - @oldPrice + @newPrice; 
                    END IF;
            
                    INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'ORDER\', @orderId, \'UPDATE\', @treasury); 
            
                END IF;
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `restockings__update_treasury_on_creation` AFTER INSERT ON `restockings` FOR EACH ROW BEGIN 
                SET @cost = NEW.total_cost; 
                SET @restockingId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN 
                    SET @treasury = -@cost; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury - @cost; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, balance) VALUES (\'RESTOCKING\', @restockingId, @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `restockings__update_treasury_on_delete` AFTER DELETE ON `restockings` FOR EACH ROW BEGIN 
                SET @price = OLD.total_cost; 
                SET @restockingId = OLD.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                IF @nbTreasury = 0 THEN
                    # Should never happen
                    SET @treasury = @price; 
                ELSE 
                    SELECT balance into @treasury 
                        FROM treasury 
                        ORDER BY id DESC
                        LIMIT 1; 
            
                    SET @treasury = @treasury + @price; 
                END IF;
            
            INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'RESTOCKING\', @restockingId, \'DELETE\', @treasury); 
            
            END
        ');

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER `restockings__update_treasury_on_update` AFTER UPDATE ON `restockings` FOR EACH ROW BEGIN 
                SET @oldCost = OLD.total_cost; 
                SET @newCost = NEW.total_cost; 
                SET @restockingId = NEW.id; 
                SET @treasury = 0; 
                SET @nbTreasury = 0; 
            
                # Only if the cost changed
                IF @oldCost != @newCost THEN
            
                    SELECT COUNT(id) INTO @nbTreasury FROM treasury; 
            
                    IF @nbTreasury = 0 THEN 
                        # Should never happen
                        SET @treasury = @oldCost - @newCost; 
                    ELSE 
                        SELECT balance into @treasury 
                            FROM treasury 
                            ORDER BY id DESC
                            LIMIT 1; 
            
                        SET @treasury = @treasury + @oldCost - @newCost; 
                    END IF;
            
                    INSERT INTO treasury (movement_type, movement_id, movement_operation, balance) VALUES (\'RESTOCKING\', @restockingId, \'UPDATE\', @treasury);
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `cash_flows__update_treasury_on_creation`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `cash_flows__update_treasury_on_delete`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `cash_flows__update_treasury_on_update`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `orders__update_treasury_on_creation`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `orders__update_treasury_on_delete`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `orders__update_treasury_on_update`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `restockings__update_treasury_on_creation`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `restockings__update_treasury_on_delete`;');
        \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER IF EXISTS `restockings__update_treasury_on_update`;');
    }
}
