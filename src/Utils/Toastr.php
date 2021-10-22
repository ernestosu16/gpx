<?php

namespace App\Utils;

use App\Service\NotifyService;

class Toastr
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
        'showDuration' => '300',
        'hideDuration' => '1000',
        'timeOut' => '5000',
        'extendedTimeOut' => '1000',
        'showEasing' => 'swing',
        'hideEasing' => 'linear',
        'showMethod' => 'fadeIn',
        'hideMethod' => 'fadeOut'
    ];

    public string $title;

    public string $message;

    public array $options;

    public function __construct(string $message, ?string $title = null, array $options = self::OPTIONS)
    {
        $this->title = $title;
        $this->message = $message;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('toastr.%s("%s","%s")', $this->options['type'], $this->message, $this->title);
    }
}