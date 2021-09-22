<?php
/**
 * Solo para el uso de sesión
 * @url: https://symfony.com/doc/current/session/database.html
 */
namespace App\Entity\System;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_sessions')]
#[ORM\Index(columns: ['sess_lifetime'], name: 'sessions_sess_lifetime_idx')]
final class Session
{
    #[ORM\Id]
    #[ORM\Column(name: 'sess_id', type: 'string', length: 128)]
    private string $id;

    #[ORM\Column(name: 'sess_data', type: 'blob', nullable: false)]
    private string $data;

    #[ORM\Column(name: 'sess_lifetime', type: 'integer', nullable: false, options: ['unsigned' => true])]
    private string $lifetime;

    #[ORM\Column(name: 'sess_time', type: 'integer', nullable: false, options: ['unsigned' => true])]
    private string $time;
}
