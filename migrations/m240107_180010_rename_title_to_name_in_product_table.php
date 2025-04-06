<?php

/**
 * Renames column title to name in product table.
 */
class m240107_180010_rename_title_to_name_in_product_table extends Migration
{
    public function up()
    {
        $this->renameColumn('product', 'title', 'name');
    }

    public function down()
    {
        $this->renameColumn('product', 'name', 'title');
    }
}