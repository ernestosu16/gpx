<?php

namespace App\Service;

use App\Utils\Notify\Toastr;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


final class NotifyService extends _Service_
{
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const ERROR = 'error';

    private ?string $type;
    private ?string $message;

    public function __construct(private SessionInterface $session)
    {
    }

    public function setType(?string $type): NotifyService
    {
        $this->type = $type;
        return $this;
    }

    public function setMessage(?string $message): NotifyService
    {
        $this->message = $message;
        return $this;
    }

    public function toastr(array $options = Toastr::OPTIONS): Toastr
    {
        return new Toastr($this, $options);
    }

    public function render(): void
    {
        $this->session->getFlashBag()->add($this->type, $this->message);
    }

    public function clearAll()
    {
        return $this->session->getFlashBag()->clear();
    }
}
