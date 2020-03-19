<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200319143045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE circulation_name_and_unit (id INT NOT NULL, name VARCHAR(255) NOT NULL, unit VARCHAR(10) NOT NULL, eto VARCHAR(20) DEFAULT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_n_u (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material_n_u (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE labor_n_u (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE circulation (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipment_n_u ADD CONSTRAINT FK_91C43371BF396750 FOREIGN KEY (id) REFERENCES circulation_name_and_unit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material_n_u ADD CONSTRAINT FK_167EE7E6BF396750 FOREIGN KEY (id) REFERENCES circulation_name_and_unit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE labor_n_u ADD CONSTRAINT FK_EAEDA821BF396750 FOREIGN KEY (id) REFERENCES circulation_name_and_unit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE equipment_n_u DROP FOREIGN KEY FK_91C43371BF396750');
        $this->addSql('ALTER TABLE material_n_u DROP FOREIGN KEY FK_167EE7E6BF396750');
        $this->addSql('ALTER TABLE labor_n_u DROP FOREIGN KEY FK_EAEDA821BF396750');
        $this->addSql('DROP TABLE circulation_name_and_unit');
        $this->addSql('DROP TABLE equipment_n_u');
        $this->addSql('DROP TABLE material_n_u');
        $this->addSql('DROP TABLE labor_n_u');
        $this->addSql('DROP TABLE circulation');
    }
}
