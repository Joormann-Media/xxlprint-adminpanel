<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119164120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Documentations (id INT AUTO_INCREMENT NOT NULL, docuName VARCHAR(255) NOT NULL, docuCreate DATETIME NOT NULL, docuUpdate DATETIME DEFAULT NULL, docuVersion VARCHAR(50) NOT NULL, docuShortdescr VARCHAR(255) DEFAULT NULL, docuDescription LONGTEXT DEFAULT NULL, docuMaintainer_id INT NOT NULL, INDEX IDX_67128B5E7AFD0D57 (docuMaintainer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Language (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2DAD13E377153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, updateBy VARCHAR(255) NOT NULL, minRole VARCHAR(255) NOT NULL, sortOrder INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, createdate DATETIME NOT NULL, createBy VARCHAR(255) NOT NULL, permissionRoute VARCHAR(255) NOT NULL, isActive TINYINT(1) DEFAULT 1 NOT NULL, onMobileIOS TINYINT(1) DEFAULT 1, onMobileAndroid TINYINT(1) DEFAULT 1, onOtherMobile TINYINT(1) DEFAULT 1, onChromeOS TINYINT(1) DEFAULT 1, onWindows TINYINT(1) DEFAULT 1, onLinux TINYINT(1) DEFAULT 1, onMacOS TINYINT(1) DEFAULT 1, allowedCountries JSON DEFAULT NULL, blockedCountries JSON DEFAULT NULL, minRole VARCHAR(255) NOT NULL, pinRequired TINYINT(1) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Statistics (id INT AUTO_INCREMENT NOT NULL, statsDate DATETIME NOT NULL, panelStartet INT DEFAULT NULL, totalFiles INT DEFAULT NULL, totalFolders INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) NOT NULL, isVerified TINYINT(1) NOT NULL, username VARCHAR(255) NOT NULL, regDate DATETIME NOT NULL, lastlogindate DATETIME NOT NULL, userpin VARCHAR(255) NOT NULL, prename VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, usergroups JSON DEFAULT NULL, passwordChangedAt DATETIME DEFAULT NULL, failedAttempts INT DEFAULT NULL, isLocked TINYINT(1) DEFAULT NULL, isActive TINYINT(1) DEFAULT NULL, twoFactorSecret VARCHAR(255) DEFAULT NULL, isTwoFactorEnabled TINYINT(1) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, adminOverride TINYINT(1) DEFAULT NULL, adminOverrideId VARCHAR(255) DEFAULT NULL, userDir VARCHAR(255) DEFAULT NULL, maxDevice VARCHAR(255) DEFAULT \'5\', ldapSyncedAt DATETIME DEFAULT NULL, emailVerificationToken VARCHAR(64) DEFAULT NULL, mobile VARCHAR(32) DEFAULT NULL, mobileVerified TINYINT(1) DEFAULT NULL, customerId VARCHAR(255) NOT NULL, digestHash VARCHAR(255) DEFAULT NULL, language_id INT DEFAULT NULL, INDEX IDX_2DA1797782F1BAF4 (language_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), INDEX IDX_472E5446FED90CCA (permission_id), PRIMARY KEY (user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE qr_code_entry (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, colorDark VARCHAR(7) NOT NULL, colorLight VARCHAR(7) NOT NULL, createdAt DATETIME NOT NULL, filePath VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL, sess_data LONGBLOB NOT NULL, sess_lifetime INT NOT NULL, sess_time INT NOT NULL, user_id INT DEFAULT NULL, PRIMARY KEY (sess_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE Documentations ADD CONSTRAINT FK_67128B5E7AFD0D57 FOREIGN KEY (docuMaintainer_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA1797782F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES Permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE DiscordWebhookHistory ADD CONSTRAINT FK_A72AC792ED766068 FOREIGN KEY (username_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE LanguageFile ADD CONSTRAINT FK_A871F40082F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ReadmeManager ADD CONSTRAINT FK_2D73925F675F31B FOREIGN KEY (author_id) REFERENCES User (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ReadmeManager ADD CONSTRAINT FK_2D7392576E0A68E FOREIGN KEY (minRole_id) REFERENCES UserRoles (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE SystemBackupStatus ADD CONSTRAINT FK_4BCCD972A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE TranslationValue ADD CONSTRAINT FK_BB8EDFCDE770D4F6 FOREIGN KEY (translationKey_id) REFERENCES TranslationKey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE TranslationValue ADD CONSTRAINT FK_BB8EDFCD82F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserDashboardConfig ADD CONSTRAINT FK_AEBA1DC9A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE UserDashboardConfig ADD CONSTRAINT FK_AEBA1DC9AFC2B591 FOREIGN KEY (module_id) REFERENCES DashboardModules (id)');
        $this->addSql('ALTER TABLE UserDevice ADD CONSTRAINT FK_2924A7ACA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE UserDeviceLog ADD CONSTRAINT FK_BA5544F7A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE UserDeviceLog ADD CONSTRAINT FK_BA5544F794A4C7D4 FOREIGN KEY (device_id) REFERENCES UserDevice (id)');
        $this->addSql('ALTER TABLE UserHistory ADD CONSTRAINT FK_F2FA3ABEA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserLoginHistory ADD CONSTRAINT FK_1EF85220A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserMenuConfig ADD CONSTRAINT FK_7F7B2402BEF3106 FOREIGN KEY (menuId_id) REFERENCES Menu (id)');
        $this->addSql('ALTER TABLE UserOnlineStatus ADD CONSTRAINT FK_31839085A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE UserProfile ADD CONSTRAINT FK_5417E0FAA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE UserSession ADD CONSTRAINT FK_5049F21A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserToken ADD CONSTRAINT FK_3BA3304EA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Documentations DROP FOREIGN KEY FK_67128B5E7AFD0D57');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA1797782F1BAF4');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FED90CCA');
        $this->addSql('DROP TABLE Documentations');
        $this->addSql('DROP TABLE Language');
        $this->addSql('DROP TABLE Menu');
        $this->addSql('DROP TABLE Permission');
        $this->addSql('DROP TABLE Statistics');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE qr_code_entry');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('ALTER TABLE DiscordWebhookHistory DROP FOREIGN KEY FK_A72AC792ED766068');
        $this->addSql('ALTER TABLE LanguageFile DROP FOREIGN KEY FK_A871F40082F1BAF4');
        $this->addSql('ALTER TABLE ReadmeManager DROP FOREIGN KEY FK_2D73925F675F31B');
        $this->addSql('ALTER TABLE ReadmeManager DROP FOREIGN KEY FK_2D7392576E0A68E');
        $this->addSql('ALTER TABLE SystemBackupStatus DROP FOREIGN KEY FK_4BCCD972A76ED395');
        $this->addSql('ALTER TABLE TranslationValue DROP FOREIGN KEY FK_BB8EDFCDE770D4F6');
        $this->addSql('ALTER TABLE TranslationValue DROP FOREIGN KEY FK_BB8EDFCD82F1BAF4');
        $this->addSql('ALTER TABLE UserDashboardConfig DROP FOREIGN KEY FK_AEBA1DC9A76ED395');
        $this->addSql('ALTER TABLE UserDashboardConfig DROP FOREIGN KEY FK_AEBA1DC9AFC2B591');
        $this->addSql('ALTER TABLE UserDevice DROP FOREIGN KEY FK_2924A7ACA76ED395');
        $this->addSql('ALTER TABLE UserDeviceLog DROP FOREIGN KEY FK_BA5544F7A76ED395');
        $this->addSql('ALTER TABLE UserDeviceLog DROP FOREIGN KEY FK_BA5544F794A4C7D4');
        $this->addSql('ALTER TABLE UserHistory DROP FOREIGN KEY FK_F2FA3ABEA76ED395');
        $this->addSql('ALTER TABLE UserLoginHistory DROP FOREIGN KEY FK_1EF85220A76ED395');
        $this->addSql('ALTER TABLE UserMenuConfig DROP FOREIGN KEY FK_7F7B2402BEF3106');
        $this->addSql('ALTER TABLE UserOnlineStatus DROP FOREIGN KEY FK_31839085A76ED395');
        $this->addSql('ALTER TABLE UserProfile DROP FOREIGN KEY FK_5417E0FAA76ED395');
        $this->addSql('ALTER TABLE UserSession DROP FOREIGN KEY FK_5049F21A76ED395');
        $this->addSql('ALTER TABLE UserToken DROP FOREIGN KEY FK_3BA3304EA76ED395');
    }
}
