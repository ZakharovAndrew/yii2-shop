<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_colors}}`.
 */
class m240107_180029_create_category_colors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category_colors}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'color_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Создаем индекс для категории
        $this->createIndex(
            'idx-category_colors-category_id',
            '{{%category_colors}}',
            'category_id'
        );

        // Создаем индекс для цвета
        $this->createIndex(
            'idx-category_colors-color_id',
            '{{%category_colors}}',
            'color_id'
        );

        // Уникальный индекс для пары категория-цвет
        $this->createIndex(
            'idx-category_colors-unique',
            '{{%category_colors}}',
            ['category_id', 'color_id'],
            true
        );

        // Внешние ключи
        $this->addForeignKey(
            'fk-category_colors-category_id',
            '{{%category_colors}}',
            'category_id',
            '{{%product_category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-category_colors-color_id',
            '{{%category_colors}}',
            'color_id',
            '{{%product_color}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category_colors}}');
    }
}
