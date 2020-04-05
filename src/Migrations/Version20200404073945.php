<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404073945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE table_row DROP FOREIGN KEY FK_8DD57CBFDEADBD13');
        $this->addSql('CREATE TABLE cl_table (id INT AUTO_INCREMENT NOT NULL, my_chapter_id INT NOT NULL, main_description VARCHAR(255) NOT NULL, INDEX IDX_BA839CD9A01B413B (my_chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cl_table ADD CONSTRAINT FK_BA839CD9A01B413B FOREIGN KEY (my_chapter_id) REFERENCES chapter (id)');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('ALTER TABLE table_row DROP FOREIGN KEY FK_8DD57CBFDEADBD13');
        $this->addSql('ALTER TABLE table_row ADD CONSTRAINT FK_8DD57CBFDEADBD13 FOREIGN KEY (my_table_id) REFERENCES cl_table (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE table_row DROP FOREIGN KEY FK_8DD57CBFDEADBD13');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, my_chapter_id INT NOT NULL, main_description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F6298F46A01B413B (my_chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46A01B413B FOREIGN KEY (my_chapter_id) REFERENCES chapter (id)');
        $this->addSql('DROP TABLE cl_table');
        $this->addSql('ALTER TABLE table_row DROP FOREIGN KEY FK_8DD57CBFDEADBD13');
        $this->addSql('ALTER TABLE table_row ADD CONSTRAINT FK_8DD57CBFDEADBD13 FOREIGN KEY (my_table_id) REFERENCES `table` (id)');
    }
}
