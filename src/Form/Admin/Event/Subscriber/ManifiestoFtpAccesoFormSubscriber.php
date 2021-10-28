<?php

namespace App\Form\Admin\Event\Subscriber;

use App\Config\Data\Nomenclador\EnvioData\ManifiestoFtpAccesoData;
use App\Entity\Nomenclador;
use App\Event\Subscriber\_Subscriber_;
use App\Form\Admin\Nomenclador\FtpAccesoType;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormBuilder;

final class ManifiestoFtpAccesoFormSubscriber extends _Subscriber_
{
    #[ArrayShape(['form.nomenclador' => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            'form.nomenclador' => 'formManifiesto',
        ];
    }

    public function formManifiesto(GenericEvent $event)
    {
        $subject = $event->getSubject();
        if (!$subject instanceof FormBuilder && !$subject->getData() instanceof Nomenclador)
            return;

        $manifiestoData = ManifiestoFtpAccesoData::newInstance();
        /** @var Nomenclador $data */
        $data = $subject->getData();
        if ($data->getId() && $data->getCodigo() === $manifiestoData->getCodeComplete())
            $this->generarFormManifiesto($subject, $data);
    }

    private function generarFormManifiesto(FormBuilder $builder, Nomenclador $data)
    {
        $builder
            ->remove('end')
            ->remove('habilitado')
            ->add('parametros', FtpAccesoType::class)
        ;
    }

}
