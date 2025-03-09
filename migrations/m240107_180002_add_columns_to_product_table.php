<?php

use yii\db\Migration;

/**
 * Добавляет столбцы cost, param1, param2, param3 в таблицу product.
 */
class m240107_180002_add_columns_to_product_table extends Migration
{
    public function up()
    {
        // Добавляем столбец cost (тип integer)
        $this->addColumn('product', 'cost', $this->integer());

        // Добавляем столбцы param1, param2, param3 (тип varchar(255))
        $this->addColumn('product', 'param1', $this->string(255));
        $this->addColumn('product', 'param2', $this->string(255));
        $this->addColumn('product', 'param3', $this->string(255));
    }

    public function down()
    {
        // Удаляем добавленные столбцы
        $this->dropColumn('product', 'cost');
        $this->dropColumn('product', 'param1');
        $this->dropColumn('product', 'param2');
        $this->dropColumn('product', 'param3');
    }
}