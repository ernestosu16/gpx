<?php

namespace App\Utils\Notify;

use App\Service\NotifyService;

final class Toastr
{
    public const POSITION_TOP_LEFT = 'toast-top-left';
    public const POSITION_TOP_RIGHT = 'toast-top-right';
    public const POSITION_TOP_FULL = 'toast-top-full-width';
    public const POSITION_TOP_CENTER = 'toast-top-center';
    public const POSITION_BOTTOM_RIGHT = 'toast-bottom-right';
    public const POSITION_BOTTOM_LEFT = 'toast-bottom-left';
    public const POSITION_BOTTOM_FULL = 'toast-bottom-full-width';
    public const POSITION_BOTTOM_CENTER = 'toast-bottom-center';

    public const OPTIONS = [
        'type' => NotifyService::SUCCESS,
        'duration' => 10000,
        'closeButton' => true,
        'debug' => false,
        'newestOnTop' => false,
        'progressBar' => true,
        'positionClass' => self::POSITION_TOP_RIGHT,
        'preventDuplicates' => false,
        'onclick' => null,
        'showDuration' => 300,
        'hideDuration' => 1000,
        'timeOut' => 5000,
        'extendedTimeOut' => 1000,
        'showEasing' => 'swing',
        'hideEasing' => 'linear',
        'showMethod' => 'fadeIn',
        'hideMethod' => 'fadeOut'
    ];

    private ?string $title;
    private ?string $message;

    public function __construct(private NotifyService $notifyService, private array $options = self::OPTIONS)
    {
        $this->notifyService->setType('toastr');
    }

    private function html(): string
    {
        return sprintf('toastr.%s("%s","%s")', $this->options['type'], $this->message, $this->title);
    }

    public function success(string $message, string $title)
    {
        $this->message = $message;
        $this->title = $title;
        $this->options['type'] = NotifyService::SUCCESS;

        $this->notifyService->setMessage($this->html());
        $this->notifyService->render();
    }

    public function error(string $message, string $title = 'Error')
    {
        $this->message = $message;
        $this->title = $title;
        $this->options['type'] = NotifyService::ERROR;

        $this->notifyService->setMessage($this->html());
        $this->notifyService->render();
    }
}