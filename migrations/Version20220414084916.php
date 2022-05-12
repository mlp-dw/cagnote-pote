<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414084916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY fk_participant_campaign1_idx');
        $this->addSql('ALTER TABLE campaign CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE participant CHANGE campaign_id campaign_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY fk_payment_participant1');
        $this->addSql('ALTER TABLE payment CHANGE participant_id participant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE campaign CHANGE id id VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F639F774');
        $this->addSql('ALTER TABLE participant CHANGE campaign_id campaign_id VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT fk_participant_campaign1_idx FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9D1C3019');
        $this->addSql('ALTER TABLE payment CHANGE participant_id participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_payment_participant1 FOREIGN KEY (participant_id) REFERENCES participant (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
