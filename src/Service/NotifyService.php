<?php

namespace App\Service;

use App\Utils\BsAlert;
use App\Utils\Toastr;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class NotifyService extends _Service_
{
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const ERROR = 'error';

    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $title
     * @param string $message
     * @param array $options
     * @return $this
     */
    public function addToastr(string $title, string $message, array $options = Toastr::OPTIONS): static
    {
        $toastr = new Toastr($message, $title, $options);
        $this->session->getFlashBag()->add('toastr', $toastr);
        return $this;
    }

    public function addBsAlert(string $message, string $type = NotifyService::SUCCESS, string $icon = 'fa fa-check'): static
    {
        $this->session->getFlashBag()->add('bsalert', new BsAlert($message, $type, $icon));

        return $this;
    }

    public function clearAll(){
        return $this->session->getFlashBag()->clear();
    }
}
