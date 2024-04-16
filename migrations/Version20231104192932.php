<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231104192932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cptproprio DROP FOREIGN KEY FK_E57BA7426B82600');
        $this->addSql('DROP TABLE cptproprio');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cptproprio (id INT AUTO_INCREMENT NOT NULL, proprio_id INT DEFAULT NULL, pourcentage INT NOT NULL, total_du INT NOT NULL, total_paye INT NOT NULL, INDEX IDX_E57BA7426B82600 (proprio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cptproprio ADD CONSTRAINT FK_E57BA7426B82600 FOREIGN KEY (proprio_id) REFERENCES proprio (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
