<?php

namespace App\Controller\Admin;

use App\Form\Admin\CronJobType;
use Cron\CronBundle\Entity\CronJob;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/cron/job', name: 'admin_cron_job')]
final class CronJobController extends _CrudController_
{
    protected static function entity(): string
    {
        return CronJob::class;
    }

    protected static function formType(): string
    {
        return CronJobType::class;
    }

    protected static function fields(): array
    {
        return ['name', 'description', 'schedule', 'command', 'enabled'];
    }

    protected static function config(): array
    {
        return [
            'translation_domain' => 'cron_bundle',
            'routes' => [
                self::INDEX => 'admin_cron_job_index',
                self::NEW => 'admin_cron_job_new',
                self::EDIT => 'admin_cron_job_edit',
                self::DELETE => 'admin_cron_job_delete',
            ]
        ];
    }
}
