<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210219142000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE `oxuser`
          ADD COLUMN `OEGRAPHQL_ANON_USERID` char(32) CHARACTER SET latin1
          COLLATE latin1_general_ci NOT NULL
          DEFAULT '' COMMENT 'anonymous user id'
          ;");
    }

    public function down(Schema $schema) : void
    {
    }
}
