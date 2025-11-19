<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119181520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE AdminConfigModules (id INT AUTO_INCREMENT NOT NULL, moduleName VARCHAR(255) DEFAULT NULL, moduleDescription LONGTEXT DEFAULT NULL, minRole VARCHAR(255) DEFAULT NULL, moduleCreate DATETIME DEFAULT NULL, moduleBy VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE AvailableEntity (id INT AUTO_INCREMENT NOT NULL, displayName VARCHAR(255) NOT NULL, className VARCHAR(255) NOT NULL, tag VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, icon VARCHAR(255) DEFAULT NULL, sortOrder INT DEFAULT NULL, dependencies JSON DEFAULT NULL, extraMeta JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE DashboardModules (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, isActive TINYINT(1) NOT NULL, minRole VARCHAR(50) DEFAULT NULL, createdAt DATETIME NOT NULL, createdBy VARCHAR(100) NOT NULL, updatedAt DATETIME DEFAULT NULL, updatedBy VARCHAR(100) DEFAULT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE DiscordWebhookHistory (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, hooktext LONGTEXT NOT NULL, hookstatus VARCHAR(50) NOT NULL, username_id INT NOT NULL, INDEX IDX_A72AC792ED766068 (username_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Documentations (id INT AUTO_INCREMENT NOT NULL, docuName VARCHAR(255) NOT NULL, docuCreate DATETIME NOT NULL, docuUpdate DATETIME DEFAULT NULL, docuVersion VARCHAR(50) NOT NULL, docuShortdescr VARCHAR(255) DEFAULT NULL, docuDescription LONGTEXT DEFAULT NULL, docuMaintainer_id INT NOT NULL, INDEX IDX_67128B5E7AFD0D57 (docuMaintainer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE EMailSignature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, linkedin VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, logoPath VARCHAR(255) DEFAULT NULL, bannerPath VARCHAR(255) DEFAULT NULL, disclaimer LONGTEXT DEFAULT NULL, htmlOutput LONGTEXT DEFAULT NULL, template VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE HelpEntry (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, expiresAt DATETIME NOT NULL, createdAt DATETIME NOT NULL, createdBy VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IconIndex (id INT AUTO_INCREMENT NOT NULL, iconPath VARCHAR(255) NOT NULL, iconName VARCHAR(255) NOT NULL, iconCategory VARCHAR(255) NOT NULL, iconTags JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Language (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2DAD13E377153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE LanguageFile (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, updatedAt DATETIME NOT NULL, language_id INT NOT NULL, INDEX IDX_A871F40082F1BAF4 (language_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE LogHistory (id INT AUTO_INCREMENT NOT NULL, target VARCHAR(255) NOT NULL, userID VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, logdump LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE MediaItem (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, sourceSystem VARCHAR(50) NOT NULL, localPath VARCHAR(255) DEFAULT NULL, coverUrl VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, externalId VARCHAR(255) DEFAULT NULL, isFavorite TINYINT(1) DEFAULT NULL, isVisible TINYINT(1) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, updateBy VARCHAR(255) NOT NULL, minRole VARCHAR(255) NOT NULL, sortOrder INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE MenuItem (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, minRole VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, lastUpdate DATETIME NOT NULL, lastUpdateBy VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, updateBy VARCHAR(255) NOT NULL, menu_id INT DEFAULT NULL, sub_menu_id INT DEFAULT NULL, sortOrder INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE MenuSubMenu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, createAt DATETIME NOT NULL, updateAt DATETIME NOT NULL, updateBy VARCHAR(255) NOT NULL, minRole VARCHAR(255) NOT NULL, parentId VARCHAR(255) NOT NULL, sortOrder INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, createdate DATETIME NOT NULL, createBy VARCHAR(255) NOT NULL, permissionRoute VARCHAR(255) NOT NULL, isActive TINYINT(1) DEFAULT 1 NOT NULL, onMobileIOS TINYINT(1) DEFAULT 1, onMobileAndroid TINYINT(1) DEFAULT 1, onOtherMobile TINYINT(1) DEFAULT 1, onChromeOS TINYINT(1) DEFAULT 1, onWindows TINYINT(1) DEFAULT 1, onLinux TINYINT(1) DEFAULT 1, onMacOS TINYINT(1) DEFAULT 1, allowedCountries JSON DEFAULT NULL, blockedCountries JSON DEFAULT NULL, minRole VARCHAR(255) NOT NULL, pinRequired TINYINT(1) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE PopUpCategory (id INT AUTO_INCREMENT NOT NULL, categoryName VARCHAR(255) NOT NULL, erstelltAm DATETIME NOT NULL, erstelltVon VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE PopUpManager (id INT AUTO_INCREMENT NOT NULL, popupName VARCHAR(255) NOT NULL, popupStatus VARCHAR(255) NOT NULL, popupExpires DATETIME NOT NULL, popupCreate DATETIME NOT NULL, popupUser VARCHAR(255) NOT NULL, popupDescription VARCHAR(255) NOT NULL, popupContent LONGTEXT NOT NULL, popupActiveFrom DATETIME NOT NULL, popupCategory VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ReadmeManager (id INT AUTO_INCREMENT NOT NULL, target VARCHAR(128) NOT NULL, targetId INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(32) DEFAULT \'active\' NOT NULL, createdAt DATETIME NOT NULL, author_id INT DEFAULT NULL, minRole_id INT DEFAULT NULL, INDEX IDX_2D73925F675F31B (author_id), INDEX IDX_2D7392576E0A68E (minRole_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE SecurityAccessLog (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, user VARCHAR(64) DEFAULT NULL, sourceIp VARCHAR(64) DEFAULT NULL, sessionType VARCHAR(32) DEFAULT NULL, status VARCHAR(32) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE Statistics (id INT AUTO_INCREMENT NOT NULL, statsDate DATETIME NOT NULL, panelStartet INT DEFAULT NULL, totalFiles INT DEFAULT NULL, totalFolders INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE SystemBackupStatus (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, type VARCHAR(50) NOT NULL, mode VARCHAR(50) NOT NULL, path VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_4BCCD972A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE TranslationKey (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_85527B848A90ABA9 (`key`), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE TranslationValue (id INT AUTO_INCREMENT NOT NULL, value LONGTEXT NOT NULL, translationKey_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_BB8EDFCDE770D4F6 (translationKey_id), INDEX IDX_BB8EDFCD82F1BAF4 (language_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) NOT NULL, isVerified TINYINT(1) NOT NULL, username VARCHAR(255) NOT NULL, regDate DATETIME NOT NULL, lastlogindate DATETIME NOT NULL, userpin VARCHAR(255) NOT NULL, prename VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, usergroups JSON DEFAULT NULL, passwordChangedAt DATETIME DEFAULT NULL, failedAttempts INT DEFAULT NULL, isLocked TINYINT(1) DEFAULT NULL, isActive TINYINT(1) DEFAULT NULL, twoFactorSecret VARCHAR(255) DEFAULT NULL, isTwoFactorEnabled TINYINT(1) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, adminOverride TINYINT(1) DEFAULT NULL, adminOverrideId VARCHAR(255) DEFAULT NULL, userDir VARCHAR(255) DEFAULT NULL, maxDevice VARCHAR(255) DEFAULT \'5\', ldapSyncedAt DATETIME DEFAULT NULL, emailVerificationToken VARCHAR(64) DEFAULT NULL, mobile VARCHAR(32) DEFAULT NULL, mobileVerified TINYINT(1) DEFAULT NULL, customerId VARCHAR(255) NOT NULL, digestHash VARCHAR(255) DEFAULT NULL, language_id INT DEFAULT NULL, INDEX IDX_2DA1797782F1BAF4 (language_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), INDEX IDX_472E5446FED90CCA (permission_id), PRIMARY KEY (user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserDashboardConfig (id INT AUTO_INCREMENT NOT NULL, sortOrder INT NOT NULL, isVisible TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, position VARCHAR(255) DEFAULT NULL, settings JSON DEFAULT \'[]\' NOT NULL, user_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_AEBA1DC9A76ED395 (user_id), INDEX IDX_AEBA1DC9AFC2B591 (module_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserDevice (id INT AUTO_INCREMENT NOT NULL, deviceName VARCHAR(255) NOT NULL, deviceType VARCHAR(100) DEFAULT NULL, deviceFingerprint VARCHAR(255) DEFAULT NULL, ipAddress VARCHAR(255) DEFAULT NULL, userAgent VARCHAR(255) DEFAULT NULL, registeredAt DATETIME NOT NULL, lastLoginAt DATETIME DEFAULT NULL, lastSeenAt DATETIME DEFAULT NULL, isTrusted TINYINT(1) NOT NULL, isActive TINYINT(1) NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_2924A7ACA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserDeviceLog (id INT AUTO_INCREMENT NOT NULL, actionType VARCHAR(50) NOT NULL, ipAddress VARCHAR(255) DEFAULT NULL, timestamp DATETIME NOT NULL, result VARCHAR(50) NOT NULL, note LONGTEXT DEFAULT NULL, user_id INT NOT NULL, device_id INT NOT NULL, INDEX IDX_BA5544F7A76ED395 (user_id), INDEX IDX_BA5544F794A4C7D4 (device_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserGroups (id INT AUTO_INCREMENT NOT NULL, GroupName VARCHAR(255) NOT NULL, GroupDescription LONGTEXT DEFAULT NULL, groupLogo VARCHAR(255) DEFAULT NULL, groupCreate DATETIME NOT NULL, groupCReateBy VARCHAR(255) NOT NULL, groupMembers LONGTEXT DEFAULT NULL, baseDir VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserHistory (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, action VARCHAR(255) NOT NULL, ipAddress VARCHAR(255) DEFAULT NULL, device VARCHAR(255) DEFAULT NULL, browserFingerprint LONGTEXT DEFAULT NULL, metaData JSON DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_F2FA3ABEA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserLoginHistory (id INT AUTO_INCREMENT NOT NULL, loginAt DATETIME NOT NULL, ipAddress VARCHAR(45) DEFAULT NULL, userAgent VARCHAR(512) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, success TINYINT(1) NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_1EF85220A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserMenuConfig (id INT AUTO_INCREMENT NOT NULL, sortOrder INT DEFAULT NULL, menuPosition VARCHAR(255) DEFAULT NULL, menuId_id INT DEFAULT NULL, INDEX IDX_7F7B2402BEF3106 (menuId_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserOnlineStatus (id INT AUTO_INCREMENT NOT NULL, isOnline TINYINT(1) NOT NULL, lastSeenAt DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_31839085A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserProfile (id INT AUTO_INCREMENT NOT NULL, birthdate DATE DEFAULT NULL, phonePrivate VARCHAR(50) DEFAULT NULL, phoneMobile VARCHAR(50) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, postalCode VARCHAR(20) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, language JSON DEFAULT NULL, profileVisibility VARCHAR(20) DEFAULT NULL, linkedin VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, xing VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, motto LONGTEXT DEFAULT NULL, tiktok VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_5417E0FAA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserRoles (id INT AUTO_INCREMENT NOT NULL, roleName VARCHAR(255) NOT NULL, roleCreate DATETIME NOT NULL, roleCreateBy VARCHAR(255) NOT NULL, roleDescription VARCHAR(255) NOT NULL, roleTag VARCHAR(255) NOT NULL, hierarchy INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserSession (id INT AUTO_INCREMENT NOT NULL, sessionId VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, lastActiveAt DATETIME DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, userAgent VARCHAR(512) DEFAULT NULL, isActive TINYINT(1) NOT NULL, user_id INT NOT NULL, INDEX IDX_5049F21A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE UserToken (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL, expiresAt DATETIME NOT NULL, used TINYINT(1) NOT NULL, user_id INT NOT NULL, INDEX IDX_3BA3304EA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE VacationManager (id INT AUTO_INCREMENT NOT NULL, vacationStatus VARCHAR(255) NOT NULL, vacationExpires DATETIME NOT NULL, vacationCreate DATETIME NOT NULL, vacationUser VARCHAR(255) NOT NULL, vacationDescription VARCHAR(255) NOT NULL, vacationContent VARCHAR(255) NOT NULL, vacationStart DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE qr_code_entry (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, colorDark VARCHAR(7) NOT NULL, colorLight VARCHAR(7) NOT NULL, createdAt DATETIME NOT NULL, filePath VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL, sess_data LONGBLOB NOT NULL, sess_lifetime INT NOT NULL, sess_time INT NOT NULL, user_id INT DEFAULT NULL, PRIMARY KEY (sess_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE DiscordWebhookHistory ADD CONSTRAINT FK_A72AC792ED766068 FOREIGN KEY (username_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Documentations ADD CONSTRAINT FK_67128B5E7AFD0D57 FOREIGN KEY (docuMaintainer_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE LanguageFile ADD CONSTRAINT FK_A871F40082F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ReadmeManager ADD CONSTRAINT FK_2D73925F675F31B FOREIGN KEY (author_id) REFERENCES User (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ReadmeManager ADD CONSTRAINT FK_2D7392576E0A68E FOREIGN KEY (minRole_id) REFERENCES UserRoles (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE SystemBackupStatus ADD CONSTRAINT FK_4BCCD972A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE TranslationValue ADD CONSTRAINT FK_BB8EDFCDE770D4F6 FOREIGN KEY (translationKey_id) REFERENCES TranslationKey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE TranslationValue ADD CONSTRAINT FK_BB8EDFCD82F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA1797782F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES Permission (id) ON DELETE CASCADE');
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
        $this->addSql('ALTER TABLE DiscordWebhookHistory DROP FOREIGN KEY FK_A72AC792ED766068');
        $this->addSql('ALTER TABLE Documentations DROP FOREIGN KEY FK_67128B5E7AFD0D57');
        $this->addSql('ALTER TABLE LanguageFile DROP FOREIGN KEY FK_A871F40082F1BAF4');
        $this->addSql('ALTER TABLE ReadmeManager DROP FOREIGN KEY FK_2D73925F675F31B');
        $this->addSql('ALTER TABLE ReadmeManager DROP FOREIGN KEY FK_2D7392576E0A68E');
        $this->addSql('ALTER TABLE SystemBackupStatus DROP FOREIGN KEY FK_4BCCD972A76ED395');
        $this->addSql('ALTER TABLE TranslationValue DROP FOREIGN KEY FK_BB8EDFCDE770D4F6');
        $this->addSql('ALTER TABLE TranslationValue DROP FOREIGN KEY FK_BB8EDFCD82F1BAF4');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA1797782F1BAF4');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FED90CCA');
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
        $this->addSql('DROP TABLE AdminConfigModules');
        $this->addSql('DROP TABLE AvailableEntity');
        $this->addSql('DROP TABLE DashboardModules');
        $this->addSql('DROP TABLE DiscordWebhookHistory');
        $this->addSql('DROP TABLE Documentations');
        $this->addSql('DROP TABLE EMailSignature');
        $this->addSql('DROP TABLE HelpEntry');
        $this->addSql('DROP TABLE IconIndex');
        $this->addSql('DROP TABLE Language');
        $this->addSql('DROP TABLE LanguageFile');
        $this->addSql('DROP TABLE LogHistory');
        $this->addSql('DROP TABLE MediaItem');
        $this->addSql('DROP TABLE Menu');
        $this->addSql('DROP TABLE MenuItem');
        $this->addSql('DROP TABLE MenuSubMenu');
        $this->addSql('DROP TABLE Permission');
        $this->addSql('DROP TABLE PopUpCategory');
        $this->addSql('DROP TABLE PopUpManager');
        $this->addSql('DROP TABLE ReadmeManager');
        $this->addSql('DROP TABLE SecurityAccessLog');
        $this->addSql('DROP TABLE Statistics');
        $this->addSql('DROP TABLE SystemBackupStatus');
        $this->addSql('DROP TABLE TranslationKey');
        $this->addSql('DROP TABLE TranslationValue');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE UserDashboardConfig');
        $this->addSql('DROP TABLE UserDevice');
        $this->addSql('DROP TABLE UserDeviceLog');
        $this->addSql('DROP TABLE UserGroups');
        $this->addSql('DROP TABLE UserHistory');
        $this->addSql('DROP TABLE UserLoginHistory');
        $this->addSql('DROP TABLE UserMenuConfig');
        $this->addSql('DROP TABLE UserOnlineStatus');
        $this->addSql('DROP TABLE UserProfile');
        $this->addSql('DROP TABLE UserRoles');
        $this->addSql('DROP TABLE UserSession');
        $this->addSql('DROP TABLE UserToken');
        $this->addSql('DROP TABLE VacationManager');
        $this->addSql('DROP TABLE qr_code_entry');
        $this->addSql('DROP TABLE sessions');
    }
}
