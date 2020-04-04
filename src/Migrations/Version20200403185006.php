<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200403185006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chapter (id INT AUTO_INCREMENT NOT NULL, my_catalog_id INT NOT NULL, name VARCHAR(20) NOT NULL, number INT NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_F981B52E3BB860AF (my_catalog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE labor (id INT NOT NULL, table_row_id INT NOT NULL, INDEX IDX_E478501DD706C31 (table_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT NOT NULL, table_row_id INT NOT NULL, INDEX IDX_D338D583DD706C31 (table_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT NOT NULL, table_row_id INT NOT NULL, INDEX IDX_7CBE7595DD706C31 (table_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, my_chapter_id INT NOT NULL, main_description VARCHAR(255) NOT NULL, INDEX IDX_F6298F46A01B413B (my_chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catalog (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(12) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE table_row (id INT AUTO_INCREMENT NOT NULL, my_table_id INT NOT NULL, description VARCHAR(255) NOT NULL, sub_description VARCHAR(255) NOT NULL, INDEX IDX_8DD57CBFDEADBD13 (my_table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E3BB860AF FOREIGN KEY (my_catalog_id) REFERENCES catalog (id)');
        $this->addSql('ALTER TABLE labor ADD CONSTRAINT FK_E478501DD706C31 FOREIGN KEY (table_row_id) REFERENCES table_row (id)');
        $this->addSql('ALTER TABLE labor ADD CONSTRAINT FK_E478501BF396750 FOREIGN KEY (id) REFERENCES circulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583DD706C31 FOREIGN KEY (table_row_id) REFERENCES table_row (id)');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583BF396750 FOREIGN KEY (id) REFERENCES circulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE7595DD706C31 FOREIGN KEY (table_row_id) REFERENCES table_row (id)');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE7595BF396750 FOREIGN KEY (id) REFERENCES circulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46A01B413B FOREIGN KEY (my_chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE table_row ADD CONSTRAINT FK_8DD57CBFDEADBD13 FOREIGN KEY (my_table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE circulation ADD discriminator VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46A01B413B');
        $this->addSql('ALTER TABLE table_row DROP FOREIGN KEY FK_8DD57CBFDEADBD13');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E3BB860AF');
        $this->addSql('ALTER TABLE labor DROP FOREIGN KEY FK_E478501DD706C31');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583DD706C31');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE7595DD706C31');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE labor');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE catalog');
        $this->addSql('DROP TABLE table_row');
        $this->addSql('ALTER TABLE circulation DROP discriminator');
    }
}
