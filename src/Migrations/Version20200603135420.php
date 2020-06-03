<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<< HEAD:src/Migrations/Version20200603125706.php
final class Version20200603125706 extends AbstractMigration
=======
final class Version20200603135420 extends AbstractMigration
>>>>>>> eb26cc0111f1d105c27f8c5a7ece8b8e08daf23b:src/Migrations/Version20200603135420.php
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE logement (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(200) NOT NULL, type TINYINT(1) NOT NULL, price INT NOT NULL, description LONGTEXT NOT NULL, rooms SMALLINT NOT NULL, publication_date DATETIME NOT NULL, main_photo VARCHAR(50) NOT NULL, slug VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_F0FD4457989D9B62 (slug), INDEX IDX_F0FD4457F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logement_image (id INT AUTO_INCREMENT NOT NULL, logement_id INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, image_size VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_323593AF58ABF955 (logement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, phone VARCHAR(20) NOT NULL, address VARCHAR(250) NOT NULL, registration_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE logement ADD CONSTRAINT FK_F0FD4457F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE logement_image ADD CONSTRAINT FK_323593AF58ABF955 FOREIGN KEY (logement_id) REFERENCES logement (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE logement_image DROP FOREIGN KEY FK_323593AF58ABF955');
        $this->addSql('ALTER TABLE logement DROP FOREIGN KEY FK_F0FD4457F675F31B');
        $this->addSql('DROP TABLE logement');
        $this->addSql('DROP TABLE logement_image');
        $this->addSql('DROP TABLE user');
    }
}
